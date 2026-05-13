<x-app-layout>
    <x-slot name="title">Detail Transaksi</x-slot>
    <x-slot name="subtitle">{{ $order->invoice_number }}</x-slot>

    {{-- Tombol aksi di topbar --}}
    <x-slot name="actions">
        <button onclick="printReceipt()" class="btn btn-primary btn-sm no-print">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Nota
        </button>

        {{-- Tombol void: hanya admin, hanya order yang masih paid --}}
        @if(auth()->user()->isAdmin() && $order->status === 'paid')
            <button type="button"
                    class="btn btn-sm no-print"
                    style="background:var(--danger-bg); color:var(--danger); border:1.5px solid rgba(244,63,94,.2);"
                    onclick="document.getElementById('void-modal').style.display='flex'">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Void Transaksi
            </button>
        @endif

        <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm no-print">
            ← Riwayat
        </a>
    </x-slot>

    {{-- ── LAYOUT: Info kiri + Nota kanan ─────────────────────────────── --}}
    <div class="receipt-layout no-print">

        {{-- Panel kiri: Ringkasan cepat --}}
        <div class="receipt-meta">

            <div class="card mb-4">
                <div class="card-header"><h3>Status Transaksi</h3></div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:.85rem;">
                    <div>
                        <div style="font-size:.72rem; font-weight:700; color:var(--coffee-400);
                                    text-transform:uppercase; letter-spacing:.08em; margin-bottom:.25rem;">
                            Invoice
                        </div>
                        <div class="font-mono"
                             style="font-size:1rem; font-weight:800; color:var(--amber-500);">
                            {{ $order->invoice_number }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size:.72rem; font-weight:700; color:var(--coffee-400);
                                    text-transform:uppercase; letter-spacing:.08em; margin-bottom:.25rem;">
                            Tanggal & Waktu
                        </div>
                        <div style="font-size:.875rem; font-weight:600; color:var(--coffee-800);">
                            {{ $order->created_at->format('d F Y') }}
                        </div>
                        <div style="font-size:.8rem; color:var(--coffee-500);">
                            {{ $order->created_at->format('H:i:s') }} WIB
                        </div>
                    </div>
                    <div>
                        <div style="font-size:.72rem; font-weight:700; color:var(--coffee-400);
                                    text-transform:uppercase; letter-spacing:.08em; margin-bottom:.25rem;">
                            Kasir
                        </div>
                        <div style="font-size:.875rem; font-weight:600; color:var(--coffee-800);">
                            {{ $order->user->name }}
                        </div>
                        <span class="badge badge-kasir">{{ ucfirst($order->user->role) }}</span>
                    </div>
                    <div>
                        <div style="font-size:.72rem; font-weight:700; color:var(--coffee-400);
                                    text-transform:uppercase; letter-spacing:.08em; margin-bottom:.25rem;">
                            Status
                        </div>
                        <span class="badge badge-success" style="font-size:.78rem; padding:.35rem .85rem;">
                            ✓ {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Ringkasan Pembayaran --}}
            <div class="card mb-4">
                <div class="card-header"><h3>Ringkasan Pembayaran</h3></div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:.6rem;">
                    <div class="cart-total-row">
                        <span>Total Belanja</span>
                        <span class="fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="cart-total-row">
                        <span>Uang Diterima</span>
                        <span>Rp {{ number_format($order->amount_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="cart-total-row"
                         style="border-top:2px dashed var(--coffee-100); padding-top:.65rem; margin-top:.25rem;
                                font-size:1.1rem; font-weight:900; color:var(--success);">
                        <span>Kembalian</span>
                        <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                    </div>
                    @if($order->notes)
                        <div style="margin-top:.35rem; padding:.6rem .75rem; background:var(--coffee-50);
                                    border-radius:8px; font-size:.8rem; color:var(--coffee-600);
                                    border-left:3px solid var(--amber-400);">
                            📝 {{ $order->notes }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Aksi --}}
            <div class="d-flex gap-3" style="flex-direction:column;">
                <button onclick="printReceipt()" class="btn btn-primary btn-block">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Nota
                </button>
                <a href="{{ route('orders.create') }}" class="btn btn-secondary btn-block">
                    ➕ Transaksi Baru
                </a>
            </div>
        </div>

        {{-- Panel kanan: Preview Nota --}}
        <div>
            <div style="font-size:.72rem; font-weight:700; color:var(--coffee-400);
                        text-transform:uppercase; letter-spacing:.1em; margin-bottom:.85rem;">
                Preview Nota
            </div>
            @include('orders.partials.receipt', ['order' => $order])
        </div>

    </div>

    {{-- ── PRINT ONLY: hanya nota, tanpa layout admin ──────────────────── --}}
    <div class="print-only">
        @include('orders.partials.receipt', ['order' => $order])
    </div>

</x-app-layout>

{{-- ── CSS khusus halaman ini ──────────────────────────────────────────── --}}
<style>
/* Layout 2 kolom: meta kiri, nota kanan */
.receipt-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 1.5rem;
    align-items: start;
    max-width: 900px;
    margin: 0 auto;
}
.receipt-meta { position: sticky; top: calc(var(--topbar-h) + 1rem); }

/* Kolom kanan: nota di tengah */
.receipt-layout > div:last-child {
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Print-only section tersembunyi di layar */
.print-only { display: none; }

/* ── Print Styles ─────────────────────────────────────────────────────── */
@media print {
    /* Sembunyikan semua kecuali print-only */
    .receipt-layout,
    .topbar,
    .sidebar,
    .no-print,
    .alert-success { display: none !important; }

    .print-only {
        display: block !important;
    }

    body, .main-content {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff !important;
        background-image: none !important;
    }
    .main-content { margin-left: 0 !important; }
    .page-content { padding: 0 !important; }

    /* Nota mengisi penuh kertas */
    .receipt-card {
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
        max-width: 100% !important;
        margin: 0 !important;
    }
}

@media (max-width: 768px) {
    .receipt-layout { grid-template-columns: 1fr; }
    .receipt-meta { position: static; }
}
</style>

{{-- ── JAVASCRIPT ───────────────────────────────────────────────────────── --}}
<script>
function printReceipt() {
    window.print();
}

// Auto-print jika redirect dari transaksi baru (ada flash success)
@if(session('success'))
    setTimeout(() => window.print(), 600);
@endif

// Tutup void modal saat klik backdrop
document.getElementById('void-modal')?.addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});

document.getElementById('void-form')?.addEventListener('submit', function() {
    const btn = document.getElementById('void-submit-btn');
    btn.disabled  = true;
    btn.innerHTML = '⏳ Memproses...';
});
</script>

{{-- ── VOID MODAL (hanya render untuk admin) ───────────────────────────── --}}
@if(auth()->user()->isAdmin() && $order->status === 'paid')
<div id="void-modal"
     style="display:none; position:fixed; inset:0; z-index:999;
            background:rgba(0,0,0,.5); backdrop-filter:blur(4px);
            align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#fff; border-radius:1rem; padding:2rem; max-width:440px; width:100%;
                box-shadow:0 24px 60px rgba(0,0,0,.18); animation:slideUp .25s ease both;">

        <div style="display:flex; align-items:flex-start; gap:.85rem; margin-bottom:1.25rem;">
            <div style="width:46px; height:46px; border-radius:12px; flex-shrink:0;
                        background:rgba(244,63,94,.1); display:flex; align-items:center; justify-content:center;">
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
                    Invoice
                    <strong style="color:var(--amber-500); font-family:'DM Mono',monospace;">
                        {{ $order->invoice_number }}
                    </strong>
                    akan dibatalkan dan stok produk dikembalikan otomatis.
                </p>
            </div>
        </div>

        <div style="background:rgba(244,63,94,.06); border:1px solid rgba(244,63,94,.15);
                    border-radius:8px; padding:.75rem 1rem; margin-bottom:1.25rem;
                    font-size:.8rem; color:#9f1239; font-weight:500; line-height:1.5;">
            ⚠️ Tindakan ini <strong>tidak dapat dibatalkan</strong>.
            Data tetap tersimpan sebagai audit trail.
        </div>

        <form id="void-form" method="POST"
              action="{{ route('orders.void', $order) }}">
            @csrf
            @method('PATCH')

            <div class="form-group">
                <label class="form-label" for="void-reason">
                    Alasan Pembatalan <span class="required">*</span>
                </label>
                <textarea id="void-reason" name="void_reason" rows="3"
                          class="form-control" style="resize:none;"
                          placeholder="Contoh: Salah input produk, pesanan pelanggan dibatalkan..."
                          required minlength="5"></textarea>
                <div class="form-hint">Alasan ini akan dicatat untuk keperluan audit.</div>
            </div>

            <div class="d-flex gap-3" style="margin-top:1.5rem;">
                <button type="submit" id="void-submit-btn" class="btn btn-danger" style="flex:1;">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Ya, Batalkan
                </button>
                <button type="button" class="btn btn-secondary" style="flex:1;"
                        onclick="document.getElementById('void-modal').style.display='none'">
                    Kembali
                </button>
            </div>
        </form>
    </div>
</div>
@endif