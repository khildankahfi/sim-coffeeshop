<x-app-layout>
    <x-slot name="title">Riwayat Transaksi</x-slot>
    <x-slot name="subtitle">Daftar semua transaksi penjualan</x-slot>
    <x-slot name="actions">
        <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Transaksi Baru
        </a>
    </x-slot>

    {{-- Filter --}}
    <form method="GET" class="card mb-4">
        <div class="card-body" style="padding:.75rem 1.25rem;">
            <div class="d-flex gap-3 align-center" style="flex-wrap:wrap;">
                <div class="d-flex align-center gap-2">
                    <label class="form-label" style="margin:0; white-space:nowrap;">Tanggal:</label>
                    <input type="date" name="date" value="{{ request('date') }}"
                           class="form-control" style="width:175px;">
                </div>
                <div class="d-flex align-center gap-2">
                    <label class="form-label" style="margin:0;">Status:</label>
                    <select name="status" class="form-control" style="width:145px;">
                        <option value="">Semua</option>
                        <option value="paid"      {{ request('status')==='paid'      ? 'selected' : '' }}>Lunas</option>
                        <option value="cancelled" {{ request('status')==='cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
                @if(request()->hasAny(['date','status']))
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-wrapper">
            @if($orders->isEmpty())
                <div class="empty-state" style="padding:3rem;">
                    <div class="empty-state-icon">🧾</div>
                    <h4>Belum ada transaksi</h4>
                    <p>Transaksi yang selesai akan muncul di sini.</p>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary" style="margin-top:1rem;">
                        Buka Kasir
                    </a>
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Kasir</th>
                            <th class="text-right">Total</th>
                            <th class="text-right">Bayar</th>
                            <th class="text-right">Kembali</th>
                            <th>Status</th>
                            <th>Waktu</th>
                            <th style="width:100px; text-align:center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr style="{{ $order->status === 'cancelled' ? 'opacity:.55;' : '' }}">
                            <td>
                                <a href="{{ route('orders.show', $order) }}"
                                   class="font-mono"
                                   style="color:var(--amber-500); font-weight:700; font-size:.82rem;">
                                    {{ $order->invoice_number }}
                                </a>
                            </td>
                            <td style="font-weight:600; color:var(--coffee-800);">
                                {{ $order->user->name }}
                            </td>
                            <td class="text-right fw-bold" style="color:var(--coffee-950);">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="text-right" style="color:var(--coffee-600);">
                                Rp {{ number_format($order->amount_paid, 0, ',', '.') }}
                            </td>
                            <td class="text-right" style="color:var(--success); font-weight:600;">
                                Rp {{ number_format($order->change_amount, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($order->status === 'paid')
                                    <span class="badge badge-success">✓ Lunas</span>
                                @elseif($order->status === 'cancelled')
                                    <span class="badge badge-danger">✕ Dibatalkan</span>
                                @else
                                    <span class="badge badge-warning">{{ ucfirst($order->status) }}</span>
                                @endif
                            </td>
                            <td style="font-size:.8rem; color:var(--coffee-500);">
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td style="text-align:center;">
                                <div class="d-flex gap-2" style="justify-content:center;">
                                    {{-- Tombol lihat nota --}}
                                    <a href="{{ route('orders.show', $order) }}"
                                       class="btn btn-secondary btn-sm" title="Lihat Nota">
                                        🧾
                                    </a>

                                    {{-- Tombol void — hanya admin, hanya status paid --}}
                                    @if(auth()->user()->isAdmin() && $order->status === 'paid')
                                        <button type="button"
                                                class="btn btn-sm"
                                                style="background:var(--danger-bg); color:var(--danger);
                                                       border:1.5px solid rgba(244,63,94,.2);"
                                                onclick="openVoidModal('{{ $order->id }}', '{{ $order->invoice_number }}')"
                                                title="Batalkan Transaksi">
                                            ✕
                                        </button>
                                    @endif
                                </div>
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

{{-- ── VOID MODAL ───────────────────────────────────────────────────────── --}}
@if(auth()->user()->isAdmin())
<div id="void-modal"
     style="display:none; position:fixed; inset:0; z-index:999;
            background:rgba(0,0,0,.5); backdrop-filter:blur(4px);
            align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#fff; border-radius:1rem; padding:2rem; max-width:440px; width:100%;
                box-shadow:0 24px 60px rgba(0,0,0,.18); animation:slideUp .25s ease both;">

        {{-- Header modal --}}
        <div style="display:flex; align-items:flex-start; gap:.85rem; margin-bottom:1.25rem;">
            <div style="width:46px; height:46px; border-radius:12px; flex-shrink:0;
                        background:rgba(244,63,94,.1); display:flex; align-items:center;
                        justify-content:center;">
                <svg width="22" height="22" fill="none" stroke="#f43f5e" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 style="font-size:1rem; font-weight:800; color:var(--coffee-950); margin-bottom:.25rem;">
                    Batalkan Transaksi?
                </h3>
                <p style="font-size:.82rem; color:var(--coffee-500); line-height:1.5;">
                    Invoice <strong id="void-invoice-display" style="color:var(--amber-500); font-family:'DM Mono',monospace;"></strong>
                    akan dibatalkan dan stok produk akan dikembalikan secara otomatis.
                </p>
            </div>
        </div>

        {{-- Warning box --}}
        <div style="background:rgba(244,63,94,.06); border:1px solid rgba(244,63,94,.15);
                    border-radius:8px; padding:.75rem 1rem; margin-bottom:1.25rem;
                    font-size:.8rem; color:#9f1239; font-weight:500; line-height:1.5;">
            ⚠️ Tindakan ini <strong>tidak dapat dibatalkan</strong>.
            Data transaksi tetap tersimpan sebagai audit trail.
        </div>

        {{-- Form void --}}
        <form id="void-form" method="POST">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label class="form-label" for="void-reason">
                    Alasan Pembatalan <span class="required">*</span>
                </label>
                <textarea id="void-reason" name="void_reason" rows="3"
                          class="form-control"
                          placeholder="Contoh: Salah input produk, pesanan pelanggan dibatalkan..."
                          style="resize:none;"
                          required minlength="5"></textarea>
                <div class="form-hint">Minimal 5 karakter. Alasan ini akan dicatat untuk audit.</div>
            </div>

            <div class="d-flex gap-3" style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-danger" style="flex:1;" id="void-submit-btn">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Ya, Batalkan
                </button>
                <button type="button" class="btn btn-secondary" style="flex:1;"
                        onclick="closeVoidModal()">
                    Batal
                </button>
            </div>
        </form>

    </div>
</div>
@endif

<script>
function openVoidModal(orderId, invoiceNumber) {
    // Set invoice number di modal
    document.getElementById('void-invoice-display').textContent = invoiceNumber;

    // Set action URL form ke route void order yang dipilih
    document.getElementById('void-form').action = '/orders/' + orderId + '/void';

    // Reset textarea
    document.getElementById('void-reason').value = '';

    document.getElementById('void-modal').style.display = 'flex';
    setTimeout(() => document.getElementById('void-reason').focus(), 100);
}

function closeVoidModal() {
    document.getElementById('void-modal').style.display = 'none';
}

// Tutup modal saat klik backdrop
document.getElementById('void-modal')?.addEventListener('click', function(e) {
    if (e.target === this) closeVoidModal();
});

// Prevent double-submit
document.getElementById('void-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('void-submit-btn');
    btn.disabled  = true;
    btn.innerHTML = '⏳ Memproses...';
});
</script>