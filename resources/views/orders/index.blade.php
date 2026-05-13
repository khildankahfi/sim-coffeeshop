<x-app-layout>
    <x-slot name="title">Riwayat Transaksi</x-slot>
    <x-slot name="subtitle">Daftar semua transaksi penjualan</x-slot>
    <x-slot name="actions">
        <a href="{{ route('orders.create') }}" class="btn btn-primary">➕ Transaksi Baru</a>
    </x-slot>

    {{-- Filter tanggal --}}
    <form method="GET" class="card" style="margin-bottom:1rem">
        <div class="card-body" style="padding:.75rem 1rem">
            <div class="d-flex gap-3 align-center">
                <label class="form-label" style="margin:0;white-space:nowrap">Filter Tanggal:</label>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="width:180px">
                <button type="submit" class="btn btn-secondary">Cari</button>
                @if(request('date'))
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-wrapper">
            @if($orders->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">🧾</div>
                    <h3>Belum ada transaksi</h3>
                    <p>Mulai transaksi baru dari halaman Kasir.</p>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary mt-3">Buka Kasir</a>
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Kasir</th>
                            <th>Total</th>
                            <th>Bayar</th>
                            <th>Kembali</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th style="width:80px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <span class="fw-semibold" style="color:var(--amber-500)">
                                    {{ $order->invoice_number }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $order->user->name }}</td>
                            <td class="fw-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($order->amount_paid, 0, ',', '.') }}</td>
                            <td style="color:var(--success);font-weight:600">
                                Rp {{ number_format($order->change_amount, 0, ',', '.') }}
                            </td>
                            <td>
                                @php $badge = ['paid'=>'badge-success','pending'=>'badge-warning','cancelled'=>'badge-danger'] @endphp
                                <span class="badge {{ $badge[$order->status] ?? 'badge-info' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary btn-sm">🧾</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination-wrapper">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
