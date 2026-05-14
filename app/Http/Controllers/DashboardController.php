<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard dengan statistik ringkasan.
     *
     * Catatan timezone:
     * Setelah config/app.php diset ke 'Asia/Jakarta', semua Carbon::now()
     * dan today() otomatis pakai WIB. Kita tetap eksplisit pakai
     * nowInTimezone() sebagai dokumentasi dan jaga-jaga kalau
     * config belum diubah.
     */
    public function index()
    {
        // Pastikan "hari ini" selalu dihitung dalam WIB (UTC+7)
        // Ini penting agar transaksi jam 00.00-06.59 WIB tidak
        // dianggap "kemarin" oleh server yang masih UTC
        $tz    = config('app.timezone', 'Asia/Jakarta');
        $today = Carbon::now($tz)->startOfDay();
        $end   = Carbon::now($tz)->endOfDay();

        // ── Statistik hari ini ──────────────────────────────────────
        $todayOrders = Order::whereBetween('created_at', [$today, $end])
                            ->where('status', 'paid')
                            ->count();

        $todayRevenue = Order::whereBetween('created_at', [$today, $end])
                             ->where('status', 'paid')
                             ->sum('total_amount');

        // ── Statistik keseluruhan ───────────────────────────────────
        $totalProducts   = Product::count();
        $totalCategories = Category::count();
        $totalUsers      = User::count();

        // ── Produk stok menipis (< 5) ───────────────────────────────
        $lowStockProducts = Product::where('stock', '<', 5)
                                   ->where('is_active', true)
                                   ->with('category')
                                   ->orderBy('stock')
                                   ->take(5)
                                   ->get();

        // ── 5 Transaksi terbaru ─────────────────────────────────────
        $recentOrders = Order::with('user')
                             ->where('status', 'paid')
                             ->latest()
                             ->take(5)
                             ->get();

        // ── Pendapatan 7 hari terakhir (untuk chart) ────────────────
        // Gunakan range waktu eksplisit per hari dalam WIB
        // supaya tidak ada transaksi yang "jatuh" ke hari yang salah
        $weeklyRevenue = collect(range(6, 0))->map(function ($daysAgo) use ($tz) {
            $dayStart = Carbon::now($tz)->subDays($daysAgo)->startOfDay();
            $dayEnd   = Carbon::now($tz)->subDays($daysAgo)->endOfDay();

            return [
                'date'    => $dayStart->translatedFormat('d M'), // format: "06 Mei"
                'revenue' => Order::whereBetween('created_at', [$dayStart, $dayEnd])
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