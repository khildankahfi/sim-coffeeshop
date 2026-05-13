<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman laporan penjualan.
     * Hanya admin yang bisa mengakses (dijaga di route).
     *
     * Mendukung filter periode: hari ini, minggu ini, bulan ini, atau custom range.
     */
    public function index(Request $request): View
    {
        // ── Tentukan periode laporan ────────────────────
        $period    = $request->get('period', 'today');
        $startDate = match ($period) {
            'today'    => today(),
            'week'     => today()->startOfWeek(),
            'month'    => today()->startOfMonth(),
            'custom'   => \Carbon\Carbon::parse($request->get('start_date', today())),
            default    => today(),
        };
        $endDate = $period === 'custom'
            ? \Carbon\Carbon::parse($request->get('end_date', today()))
            : today();

        // ── Query orders dalam periode ──────────────────
        $ordersQuery = Order::where('status', 'paid')
                            ->whereBetween('created_at', [
                                $startDate->startOfDay(),
                                $endDate->copy()->endOfDay(),
                            ]);

        // ── Statistik utama ─────────────────────────────
        $totalRevenue      = $ordersQuery->clone()->sum('total_amount');
        $totalTransactions = $ordersQuery->clone()->count();
        $averageTransaction = $totalTransactions > 0
            ? $totalRevenue / $totalTransactions
            : 0;

        // ── Produk terlaris dalam periode ──────────────
        $topProducts = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'paid')
            ->whereBetween('orders.created_at', [
                $startDate->startOfDay(),
                $endDate->copy()->endOfDay(),
            ])
            ->select(
                'order_items.product_name',
                \DB::raw('SUM(order_items.quantity) as total_quantity'),
                \DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_quantity')
            ->take(10)
            ->get();

        // ── Pendapatan per hari dalam periode (untuk chart) ──
        $dailyRevenue = Order::where('status', 'paid')
            ->whereBetween('created_at', [
                $startDate->startOfDay(),
                $endDate->copy()->endOfDay(),
            ])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // ── Transaksi terbaru ───────────────────────────
        $recentOrders = $ordersQuery->clone()
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
