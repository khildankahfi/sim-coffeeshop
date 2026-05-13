{{--
    resources/views/dashboard/index.blade.php
    Variabel dari DashboardController:
      $todayOrders       int
      $todayRevenue      int
      $totalProducts     int
      $totalCategories   int
      $totalUsers        int
      $lowStockProducts  Collection<Product> (with category, stock < 5)
      $recentOrders      Collection<Order>   (with user, status paid)
      $weeklyRevenue     Collection [['date' => '06 May', 'revenue' => 0], ...]
--}}

<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>
    <x-slot name="subtitle">Selamat datang, {{ auth()->user()->name }}!</x-slot>

    {{-- ── STAT CARDS ──────────────────────────────────────────────────── --}}
    <div class="stats-grid stagger">

        <div class="stat-card amber">
            <div class="stat-icon">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value stat-animate">{{ $todayOrders }}</div>
                <div class="stat-label">Transaksi Hari Ini</div>
            </div>
        </div>

        <div class="stat-card emerald">
            <div class="stat-icon">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-info">
                {{-- font-size dikecilkan sedikit karena string Rp bisa panjang --}}
                <div class="stat-value stat-animate" style="font-size:1.35rem;">
                    Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                </div>
                <div class="stat-label">Pendapatan Hari Ini</div>
            </div>
        </div>

        <div class="stat-card coffee">
            <div class="stat-icon">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value stat-animate">{{ $totalProducts }}</div>
                <div class="stat-label">Total Produk</div>
            </div>
        </div>

        <div class="stat-card indigo">
            <div class="stat-icon">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value stat-animate">{{ $totalUsers }}</div>
                <div class="stat-label">Total Pengguna</div>
            </div>
        </div>

    </div>

    {{-- ── CHART + STOK MENIPIS ─────────────────────────────────────────── --}}
    <div class="content-grid mb-6">

        {{-- Chart Pendapatan 7 Hari --}}
        <div class="card">
            <div class="card-header">
                <h3>Pendapatan 7 Hari Terakhir</h3>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">
                    Laporan Lengkap
                </a>
            </div>
            <div class="card-body" style="padding: 1.25rem 1.75rem 1.5rem;">
                <div class="chart-container" style="height: 220px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Stok Menipis --}}
        <div class="card">
            <div class="card-header">
                <h3>Stok Menipis</h3>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">Kelola</a>
                @endif
            </div>

            @forelse($lowStockProducts as $product)
                <div class="d-flex align-center justify-between"
                     style="padding: .9rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,.04);">
                    <div style="min-width:0; flex:1;">
                        <div class="truncate"
                             style="font-size:.875rem; font-weight:700; color:var(--coffee-900);">
                            {{ $product->name }}
                        </div>
                        <div style="font-size:.75rem; color:var(--coffee-400); margin-top:2px;">
                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                        </div>
                    </div>
                    <div class="d-flex align-center gap-2" style="flex-shrink:0; margin-left:.75rem;">
                        <span style="font-size:.875rem; font-weight:800; color:var(--coffee-800);">
                            {{ $product->stock }}
                        </span>
                        @if($product->stock === 0)
                            <span class="badge badge-danger">Habis</span>
                        @elseif($product->stock <= 2)
                            <span class="badge badge-danger">Kritis</span>
                        @else
                            <span class="badge badge-warning">Menipis</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state" style="padding:2.5rem 1.5rem;">
                    <div style="font-size:2.5rem; opacity:.3;">✅</div>
                    <p style="font-size:.875rem; font-weight:600; color:var(--coffee-500); margin-top:.5rem;">
                        Stok aman!
                    </p>
                </div>
            @endforelse
        </div>

    </div>

    {{-- ── TRANSAKSI TERBARU ────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <h3>Transaksi Terbaru</h3>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Kasir</th>
                        <th>Total</th>
                        <th>Waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td class="font-mono" style="font-size:.8rem; color:var(--coffee-500);">
                                #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td style="font-weight:600; color:var(--coffee-800);">
                                {{ $order->user->name ?? '—' }}
                            </td>
                            <td style="font-weight:800; color:var(--coffee-950);">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td style="font-size:.8rem; color:var(--coffee-500);">
                                {{ $order->created_at->format('d M, H:i') }}
                            </td>
                            <td>
                                <span class="badge badge-success">Lunas</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="empty-state" style="padding:2.5rem;">
                                    <div style="font-size:2.5rem; opacity:.25;">🧾</div>
                                    <p style="font-size:.875rem; font-weight:700; color:var(--coffee-600); margin-top:.5rem;">
                                        Belum ada transaksi hari ini
                                    </p>
                                    <p style="font-size:.8rem; color:var(--coffee-400); margin-top:.25rem;">
                                        Transaksi yang selesai akan muncul di sini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    // Ambil data dari $weeklyRevenue collection yang sudah di-encode Blade
    // weeklyRevenue = [{ date: '06 May', revenue: 0 }, ...]
    const weekly = @json($weeklyRevenue);
    const labels  = weekly.map(d => d.date);
    const dataset = weekly.map(d => d.revenue);

    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Pendapatan',
                data: dataset,
                // Dynamic gradient — dibuat tiap render supaya responsif
                backgroundColor: (context) => {
                    const chart      = context.chart;
                    const { ctx: c, chartArea } = chart;
                    if (!chartArea) return 'rgba(245,158,11,.7)';
                    const grad = c.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                    grad.addColorStop(0, 'rgba(245,158,11,.9)');
                    grad.addColorStop(1, 'rgba(245,158,11,.2)');
                    return grad;
                },
                borderColor:          'rgba(245,158,11,1)',
                borderWidth:          2,
                borderRadius:         6,
                borderSkipped:        false,
                hoverBackgroundColor: 'rgba(245,158,11,1)',
            }]
        },
        options: {
            responsive:          true,
            maintainAspectRatio: false,
            animation:           { duration: 700, easing: 'easeOutQuart' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1a0d00',
                    titleColor:      'rgba(255,255,255,.55)',
                    bodyColor:       '#fff',
                    bodyFont:        { family: 'Poppins', weight: '700', size: 14 },
                    padding:         12,
                    cornerRadius:    8,
                    callbacks: {
                        label: (ctx) => ' Rp ' + ctx.parsed.y.toLocaleString('id-ID'),
                    }
                }
            },
            scales: {
                x: {
                    grid:   { display: false },
                    border: { display: false },
                    ticks:  { color: '#8b4f1e', font: { family: 'Poppins', size: 11, weight: '600' } }
                },
                y: {
                    grid:   { color: 'rgba(0,0,0,.05)' },
                    border: { display: false, dash: [4, 4] },
                    ticks:  {
                        color: '#8b4f1e',
                        font:  { family: 'Poppins', size: 11 },
                        // Format sumbu Y: 150000 → "150k", 0 → "Rp 0"
                        callback: (val) => val === 0
                            ? 'Rp 0'
                            : 'Rp ' + (val >= 1000000
                                ? (val / 1000000).toFixed(1) + 'jt'
                                : (val / 1000).toFixed(0) + 'k'),
                    }
                }
            }
        }
    });
})();
</script>
@endpush