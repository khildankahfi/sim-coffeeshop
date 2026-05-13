<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard dengan statistik ringkasan.
     * Hanya user yang sudah login (admin & kasir) yang bisa akses.
     */
    public function index()
    {
        // ── Statistik hari ini ──────────────────────────
        $today = today();

        $todayOrders = Order::whereDate('created_at', $today)
                            ->where('status', 'paid')
                            ->count();

        $todayRevenue = Order::whereDate('created_at', $today)
                             ->where('status', 'paid')
                             ->sum('total_amount');

        // ── Statistik keseluruhan ───────────────────────
        $totalProducts  = Product::count();
        $totalCategories = Category::count();
        $totalUsers     = User::count();

        // ── Produk stok menipis (< 5) ──────────────────
        $lowStockProducts = Product::where('stock', '<', 5)
                                   ->where('is_active', true)
                                   ->with('category')
                                   ->orderBy('stock')
                                   ->take(5)
                                   ->get();

        // ── 5 Transaksi terbaru ─────────────────────────
        $recentOrders = Order::with('user')
                             ->where('status', 'paid')
                             ->latest()
                             ->take(5)
                             ->get();

        // ── Pendapatan 7 hari terakhir (untuk chart) ───
        $weeklyRevenue = collect(range(6, 0))->map(function ($daysAgo) {
            $date = today()->subDays($daysAgo);
            return [
                'date'    => $date->format('d M'),
                'revenue' => Order::whereDate('created_at', $date)
                                  ->where('status', 'paid')
                                  ->sum('total_amount'),
            ];
        });

        return view('dashboard.index', compact(
            'todayOrders',
            'todayRevenue',
            'totalProducts',
            'totalCategories',
            'totalUsers',
            'lowStockProducts',
            'recentOrders',
            'weeklyRevenue',
        ));
    }
}
