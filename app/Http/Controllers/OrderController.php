<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
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
        $query = Order::with('user')->latest();

        // Kasir hanya melihat transaksi miliknya sendiri
        if (auth()->user()->isKasir()) {
            $query->where('user_id', auth()->id());
        }

        // Filter berdasarkan tanggal
        if (request('date')) {
            $query->whereDate('created_at', request('date'));
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    /**
     * Tampilkan halaman kasir (POS - Point of Sale).
     * Menampilkan semua produk aktif yang masih ada stoknya.
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
     * Menggunakan DB::transaction() agar semua operasi atomik (semua berhasil atau semua dibatalkan).
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, &$order) {
            $totalAmount = 0;
            $orderItems  = [];

            // ── Hitung total & siapkan data item ──────────
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Validasi stok masih cukup
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok {$product->name} tidak mencukupi! Stok tersedia: {$product->stock}");
                }

                $subtotal      = $product->price * $item['quantity'];
                $totalAmount  += $subtotal;

                $orderItems[] = [
                    'product_id'   => $product->id,
                    'product_name' => $product->name,   // Snapshot
                    'price'        => $product->price,   // Snapshot
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $subtotal,
                ];

                // ── Kurangi stok produk ──────────────────
                $product->decrement('stock', $item['quantity']);
            }

            // ── Buat header order ─────────────────────────
            $order = Order::create([
                'user_id'        => auth()->id(),
                'invoice_number' => Order::generateInvoiceNumber(),
                'total_amount'   => $totalAmount,
                'amount_paid'    => $validated['amount_paid'],
                'change_amount'  => $validated['amount_paid'] - $totalAmount,
                'status'         => 'paid',
                'notes'          => $validated['notes'] ?? null,
            ]);

            // ── Simpan semua item sekaligus ───────────────
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
        // Load relasi items beserta produknya
        $order->load('items.product', 'user');
        return view('orders.show', compact('order'));
    }
}
