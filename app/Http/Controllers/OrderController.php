<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Tampilkan riwayat semua transaksi.
     * Admin melihat semua, kasir hanya miliknya sendiri.
     */
    public function index(): View
    {
        $tz    = config('app.timezone', 'Asia/Jakarta');
        $query = Order::with('user')->latest();

        if (auth()->user()->isKasir()) {
            $query->where('user_id', auth()->id());
        }

        // Filter tanggal pakai range WIB agar tidak ada transaksi yang terlewat
        if (request('date')) {
            $dayStart = Carbon::parse(request('date'), $tz)->startOfDay();
            $dayEnd   = Carbon::parse(request('date'), $tz)->endOfDay();
            $query->whereBetween('created_at', [$dayStart, $dayEnd]);
        }

        if (request('status')) {
            $query->where('status', request('status'));
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    /**
     * ── CEK STOK REAL-TIME (AJAX) ───────────────────────────────────────
     * Dipanggil oleh POS sebelum submit order via fetch().
     * Memvalidasi apakah stok tiap produk masih cukup untuk qty yang dipesan.
     *
     * Request body: { items: [{ product_id, quantity }] }
     * Response:
     *   { ok: true }  → semua stok cukup, lanjut submit
     *   { ok: false, errors: [{ product_id, name, requested, available }] }
     */
    public function checkStock(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'items'              => ['required', 'array'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $errors = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);

            if (!$product || $product->stock < $item['quantity']) {
                $errors[] = [
                    'product_id' => $item['product_id'],
                    'name'       => $product?->name ?? 'Produk tidak ditemukan',
                    'requested'  => $item['quantity'],
                    'available'  => $product?->stock ?? 0,
                ];
            }
        }

        if (!empty($errors)) {
            return response()->json(['ok' => false, 'errors' => $errors], 422);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Tampilkan halaman kasir (POS).
     */
    public function create(): View
    {
        $products = Product::active()
                           ->inStock()
                           ->with('category')
                           ->orderBy('name')
                           ->get();

        return view('orders.create', compact('products'));
    }

    /**
     * Proses dan simpan transaksi baru.
     * Menggunakan DB::transaction() agar atomik.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, &$order) {
            $totalAmount = 0;
            $orderItems  = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi! Stok tersedia: {$product->stock}");
                }

                $subtotal      = $product->price * $item['quantity'];
                $totalAmount  += $subtotal;

                $orderItems[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'price'        => $product->price,
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $subtotal,
                ];

                $product->decrement('stock', $item['quantity']);
            }

            $order = Order::create([
                'user_id'        => auth()->id(),
                'invoice_number' => Order::generateInvoiceNumber(),
                'total_amount'   => $totalAmount,
                'amount_paid'    => $validated['amount_paid'],
                'change_amount'  => $validated['amount_paid'] - $totalAmount,
                'status'         => 'paid',
                'notes'          => $validated['notes'] ?? null,
            ]);

            $order->items()->createMany($orderItems);
        });

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Transaksi berhasil! Invoice: ' . $order->invoice_number);
    }

    /**
     * Tampilkan detail transaksi / nota.
     */
    public function show(Order $order): View
    {
        $order->load('items.product', 'user');
        return view('orders.show', compact('order'));
    }

    /**
     * ── VOID TRANSAKSI ─────────────────────────────────────────────────
     * Membatalkan transaksi yang sudah paid.
     * Hanya admin yang bisa melakukan void.
     *
     * Yang terjadi saat void:
     *   1. Status order diubah jadi 'cancelled'
     *   2. Stok produk dikembalikan sesuai qty di order items
     *   3. Alasan void disimpan di kolom notes
     *
     * Tidak ada penghapusan data — order tetap ada untuk audit trail.
     */
    public function void(Request $request, Order $order): RedirectResponse
    {
        // Hanya admin yang boleh void
        abort_unless(auth()->user()->isAdmin(), 403, 'Hanya admin yang dapat membatalkan transaksi.');

        // Transaksi yang sudah dibatalkan tidak bisa di-void lagi
        if ($order->status === 'cancelled') {
            return back()->with('error', 'Transaksi ini sudah dibatalkan sebelumnya.');
        }

        // Validasi alasan void wajib diisi
        $request->validate([
            'void_reason' => ['required', 'string', 'min:5', 'max:255'],
        ], [
            'void_reason.required' => 'Alasan pembatalan wajib diisi.',
            'void_reason.min'      => 'Alasan terlalu singkat, minimal 5 karakter.',
        ]);

        DB::transaction(function () use ($request, $order) {
            // Kembalikan stok tiap produk
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)
                       ->increment('stock', $item->quantity);
            }

            // Update status dan simpan alasan void
            $order->update([
                'status' => 'cancelled',
                'notes'  => '[VOID oleh ' . auth()->user()->name . '] ' . $request->void_reason
                          . ($order->notes ? ' | Catatan asal: ' . $order->notes : ''),
            ]);
        });

        return redirect()
            ->route('orders.index')
            ->with('success', "Transaksi {$order->invoice_number} berhasil dibatalkan. Stok produk telah dikembalikan.");
    }
}