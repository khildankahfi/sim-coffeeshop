<x-app-layout>
    <x-slot name="title">Laporan Penjualan</x-slot>
    <x-slot name="subtitle">Analisis pendapatan dan transaksi</x-slot>

    {{-- ── FILTER PERIODE ──────────────────────────────────────────────── --}}
    <form method="GET" class="card mb-6">
        <div class="card-body" style="padding:.85rem 1.5rem;">
            <div class="d-flex gap-3 align-center" style="flex-wrap:wrap;">
                <div class="d-flex gap-2">
                    @foreach(['today' => 'Hari Ini', 'week' => 'Minggu Ini', 'month' => 'Bulan Ini', 'custom' => 'Custom'] as $val => $label)
                        <a href="{{ route('reports.index', ['period' => $val]) }}"
                           class="btn {{ $period === $val ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                @if($period === 'custom')
                    <div class="d-flex gap-2 align-center" style="flex-wrap:wrap;">
                        <input type="hidden" name="period" value="custom">
                        <input type="date" name="start_date"
                               value="{{ request('start_date', today()->format('Y-m-d')) }}"
                               class="form-control" style="width:160px;">
                        <span class="text-muted" style="font-weight:600;">s/d</span>
                        <input type="date" name="end_date"
                               value="{{ request('end_date', today()->format('Y-m-d')) }}"
                               class="form-control" style="width:160px;">
                        <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
                    </div>
                @endif
            </div>
        </div>
    </form>

    {{-- ── STAT CARDS ──────────────────────────────────────────────────── --}}
    <div class="stats-grid stagger" style="grid-template-columns: repeat(3, 1fr); margin-bottom:2rem;">

        <div class="stat-card emerald">
            <div class="stat-icon">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value stat-animate" style="font-size:1.35rem;">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
                <div class="stat-label">Total Pendapatan</div>
            </div>
        </div>

        <div class="stat-card amber">
            <div class="stat-icon">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value stat-animate">{{ $totalTransactions }}</div>
                <div class="stat-label">Total Transaksi</div>
            </div>
        </div>

        <div class="stat-card indigo">
            <div class="stat-icon">
                <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="stat-info">
                <div class="stat-value stat-animate" style="font-size:1.35rem;">
                    Rp {{ number_format($averageTransaction, 0, ',', '.') }}
                </div>
                <div class="stat-label">Rata-rata per Transaksi</div>
            </div>
        </div>

    </div>

    {{-- ── CHART + TOP PRODUK ───────────────────────────────────────────── --}}
    <div class="content-grid mb-6">

        {{-- Chart Pendapatan --}}
        <div class="card">
            <div class="card-header">
                <h3>Tren Pendapatan</h3>
            </div>
            <div class="card-body" style="padding:1.25rem 1.75rem 1.5rem;">
                @if($dailyRevenue->isNotEmpty())
                    <div class="chart-container" style="height:220px;">
                        <canvas id="dailyChart"></canvas>
                    </div>
                @else
                    <div class="empty-state">
                        <div style="font-size:2.5rem;opacity:.25;">📈</div>
                        <p style="font-size:.875rem;font-weight:600;color:var(--coffee-500);margin-top:.5rem;">
                            Tidak ada data di periode ini
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Produk Terlaris --}}
        <div class="card">
            <div class="card-header"><h3>Produk Terlaris</h3></div>

            @if($topProducts->isEmpty())
                <div class="empty-state" style="padding:2.5rem;">
                    <div style="font-size:2.5rem;opacity:.25;">🏆</div>
                    <p style="font-size:.875rem;font-weight:600;color:var(--coffee-500);margin-top:.5rem;">
                        Belum ada data
                    </p>
                </div>
            @else
                @foreach($topProducts as $i => $p)
                    <div class="d-flex align-center gap-3"
                         style="padding:.85rem 1.5rem; border-bottom:1px solid rgba(0,0,0,.04);">
                        <div style="
                            width:30px; height:30px; border-radius:50%; flex-shrink:0;
                            display:flex; align-items:center; justify-content:center;
                            font-size:.8rem; font-weight:800;
                            background: {{ $i===0 ? 'linear-gradient(135deg,#f59e0b,#d97706)' : ($i===1 ? 'linear-gradient(135deg,#94a3b8,#64748b)' : ($i===2 ? 'linear-gradient(135deg,#b45309,#92400e)' : 'var(--coffee-100)')) }};
                            color: {{ $i < 3 ? '#fff' : 'var(--coffee-500)' }};
                            box-shadow: {{ $i===0 ? '0 3px 10px rgba(245,158,11,.35)' : 'none' }};
                        ">{{ $i + 1 }}</div>

                        <div style="flex:1; min-width:0;">
                            <div class="truncate fw-semibold" style="font-size:.875rem; color:var(--coffee-900);">
                                {{ $p->product_name }}
                            </div>
                            <div style="font-size:.75rem; color:var(--coffee-400); margin-top:1px;">
                                {{ $p->total_quantity }} porsi terjual
                            </div>
                        </div>

                        <div style="font-size:.82rem; font-weight:800; color:var(--amber-500); flex-shrink:0;">
                            Rp {{ number_format($p->total_revenue, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

    </div>

    {{-- ── TABEL TRANSAKSI ─────────────────────────────────────────────── --}}
    <div class="card">
        <div class="card-header">
            <h3>Daftar Transaksi
                <span class="badge badge-coffee" style="font-size:.7rem; margin-left:.25rem;">
                    {{ $totalTransactions }}
                </span>
            </h3>
        </div>
        <div class="table-wrapper">
            @if($recentOrders->isEmpty())
                <div class="empty-state" style="padding:3rem;">
                    <div style="font-size:2.5rem;opacity:.25;">📋</div>
                    <p style="font-size:.875rem;font-weight:700;color:var(--coffee-600);margin-top:.5rem;">
                        Tidak ada transaksi di periode ini
                    </p>
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Kasir</th>
                            <th class="text-right">Total</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('orders.show', $order) }}"
                                   style="color:var(--amber-500); font-weight:700;
                                          font-family:'DM Mono',monospace; font-size:.82rem;">
                                    {{ $order->invoice_number }}
                                </a>
                            </td>
                            <td style="font-weight:600; color:var(--coffee-800);">
                                {{ $order->user->name }}
                            </td>
                            <td class="text-right fw-bold" style="color:var(--coffee-950);">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td style="font-size:.82rem; color:var(--coffee-500);">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</x-app-layout>

{{-- ── CHART SCRIPT (inline — bukan @push agar pasti ter-render) ──────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function () {
    const daily = @json($dailyRevenue);

    if (!daily || daily.length === 0) return;

    const labels  = daily.map(d => {
        // Format tanggal: "2025-05-17" → "17 Mei"
        const parts = d.date.split('-');
        const dt    = new Date(parts[0], parts[1] - 1, parts[2]);
        return dt.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
    });
    const dataset = daily.map(d => parseFloat(d.revenue) || 0);

    const ctx = document.getElementById('dailyChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Pendapatan',
                data: dataset,
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
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 700, easing: 'easeOutQuart' },
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
                    border: { display: false, dash: [4,4] },
                    beginAtZero: true,
                    ticks: {
                        color: '#8b4f1e',
                        font:  { family: 'Poppins', size: 11 },
                        callback: (val) => val === 0
                            ? 'Rp 0'
                            : 'Rp ' + (val >= 1000000
                                ? (val/1000000).toFixed(1) + 'jt'
                                : (val/1000).toFixed(0) + 'k'),
                    }
                }
            }
        }
    });
})();
</script>