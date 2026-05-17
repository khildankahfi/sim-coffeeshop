<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman laporan penjualan.
     * Hanya admin yang bisa mengakses (dijaga di route).
     *
     * Semua kalkulasi tanggal pakai timezone WIB (Asia/Jakarta)
     * agar filter "hari ini" dan "minggu ini" akurat.
     */
    public function index(Request $request): View
    {
        $tz     = config('app.timezone', 'Asia/Jakarta');
        $now    = Carbon::now($tz);
        $period = $request->get('period', 'today');

        // ── Tentukan range periode dalam WIB ────────────────────────
        [$startDate, $endDate] = match ($period) {
            'today'  => [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
            ],
            'week'   => [
                $now->copy()->startOfWeek(),
                $now->copy()->endOfDay(),
            ],
            'month'  => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfDay(),
            ],
            'custom' => [
                Carbon::parse($request->get('start_date', $now->toDateString()), $tz)->startOfDay(),
                Carbon::parse($request->get('end_date',   $now->toDateString()), $tz)->endOfDay(),
            ],
            default  => [
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
            ],
        };

        // ── Query orders dalam periode ──────────────────────────────
        $baseQuery = Order::where('status', 'paid')
                          ->whereBetween('created_at', [$startDate, $endDate]);

        // ── Statistik utama ─────────────────────────────────────────
        $totalRevenue       = (clone $baseQuery)->sum('total_amount');
        $totalTransactions  = (clone $baseQuery)->count();
        $averageTransaction = $totalTransactions > 0
            ? round($totalRevenue / $totalTransactions)
            : 0;

        // ── Produk terlaris dalam periode ───────────────────────────
        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'paid')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();

        // ── Pendapatan per hari (untuk chart) ───────────────────────
        // Pakai DATE_ADD +7 jam (WIB) alih-alih CONVERT_TZ
        // karena CONVERT_TZ butuh MySQL timezone tables yang sering tidak ter-install
        $dailyRevenue = Order::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw(
                "DATE(DATE_ADD(created_at, INTERVAL 7 HOUR)) as date,
                 SUM(total_amount) as revenue,
                 COUNT(*) as transactions"
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Transaksi terbaru ────────────────────────────────────────
        $recentOrders = (clone $baseQuery)
                            ->with('user')
                            ->latest()
                            ->take(20)
                            ->get();

        return view('reports.index', compact(
            'period',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalTransactions',
            'averageTransaction',
            'topProducts',
            'dailyRevenue',
            'recentOrders',
        ));
    }
}