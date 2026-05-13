{{--
    Partial ini dipakai DUA KALI di orders/show.blade.php:
    1. Di layar  → sebagai "Preview Nota"
    2. Saat print → via .print-only div

    Variabel: $order (dengan relasi items & user sudah di-load)
--}}

<div class="receipt-card"
     style="background:#fff; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,.08);
            border:1px solid rgba(0,0,0,.05); max-width:380px; width:100%;
            font-family:'Poppins',sans-serif;">

    {{-- ── Header Nota ─────────────────────────────────────────────── --}}
    <div style="text-align:center; padding:1.75rem 1.5rem 1.25rem;
                border-bottom:2px dashed #e3bb8a;">
        <div style="font-size:2.25rem; margin-bottom:.4rem; line-height:1;">☕</div>
        <div style="font-size:1.1rem; font-weight:900; color:#1a0d00; letter-spacing:-.03em;">
            SIM Coffeeshop
        </div>
        <div style="font-size:.72rem; color:#8b4f1e; font-weight:600;
                    letter-spacing:.1em; text-transform:uppercase; margin-top:.15rem;">
            Premium Brew
        </div>

        {{-- Divider titik --}}
        <div style="border-top:1px dashed #e3bb8a; margin:.85rem 0;"></div>

        <div style="font-size:.75rem; color:#8b4f1e; line-height:1.7;">
            <div style="font-weight:800; font-size:.82rem; color:#f59e0b; letter-spacing:.03em;
                        font-family:'DM Mono',monospace;">
                {{ $order->invoice_number }}
            </div>
            <div>{{ $order->created_at->format('d F Y, H:i') }} WIB</div>
            <div>Kasir: <strong style="color:#1a0d00;">{{ $order->user->name }}</strong></div>
        </div>
    </div>

    {{-- ── Daftar Item ─────────────────────────────────────────────── --}}
    <div style="padding:.75rem 1.5rem;">
        <table style="width:100%; border-collapse:collapse; font-size:.82rem;">
            <thead>
                <tr style="border-bottom:1px solid #f3dfc0;">
                    <th style="padding:.5rem 0; text-align:left; color:#8b4f1e;
                                font-weight:700; font-size:.68rem; text-transform:uppercase;
                                letter-spacing:.07em; width:50%;">
                        Produk
                    </th>
                    <th style="padding:.5rem 0; text-align:center; color:#8b4f1e;
                                font-weight:700; font-size:.68rem; text-transform:uppercase;
                                letter-spacing:.07em; width:15%;">
                        Qty
                    </th>
                    <th style="padding:.5rem 0; text-align:right; color:#8b4f1e;
                                font-weight:700; font-size:.68rem; text-transform:uppercase;
                                letter-spacing:.07em;">
                        Harga
                    </th>
                    <th style="padding:.5rem 0; text-align:right; color:#8b4f1e;
                                font-weight:700; font-size:.68rem; text-transform:uppercase;
                                letter-spacing:.07em;">
                        Subtotal
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr style="border-bottom:1px solid #fdf6ec;">
                    <td style="padding:.55rem 0; vertical-align:top;">
                        <div style="font-weight:700; color:#1a0d00; line-height:1.3;">
                            {{ $item->product_name }}
                        </div>
                    </td>
                    <td style="padding:.55rem 0; text-align:center; color:#4a2008; font-weight:600;">
                        {{ $item->quantity }}
                    </td>
                    <td style="padding:.55rem 0; text-align:right; color:#6b3510; font-size:.78rem;">
                        {{ number_format($item->price, 0, ',', '.') }}
                    </td>
                    <td style="padding:.55rem 0; text-align:right; font-weight:700; color:#1a0d00;">
                        {{ number_format($item->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ── Ringkasan Pembayaran ────────────────────────────────────── --}}
    <div style="padding:.75rem 1.5rem 1.25rem; border-top:2px dashed #e3bb8a;">

        {{-- Total item --}}
        <div style="font-size:.72rem; color:#8b4f1e; font-weight:600; margin-bottom:.5rem;">
            {{ $order->items->sum('quantity') }} item &bull; {{ $order->items->count() }} menu
        </div>

        {{-- Baris total --}}
        @foreach([
            ['label' => 'Total Belanja', 'value' => $order->total_amount,  'bold' => false, 'color' => '#2e1503'],
            ['label' => 'Uang Diterima', 'value' => $order->amount_paid,   'bold' => false, 'color' => '#4a2008'],
        ] as $row)
        <div style="display:flex; justify-content:space-between; align-items:center;
                    padding:.3rem 0; font-size:.83rem;">
            <span style="color:#6b3510; font-weight:{{ $row['bold'] ? '700' : '500' }};">
                {{ $row['label'] }}
            </span>
            <span style="font-weight:{{ $row['bold'] ? '800' : '600' }}; color:{{ $row['color'] }};">
                Rp {{ number_format($row['value'], 0, ',', '.') }}
            </span>
        </div>
        @endforeach

        {{-- Kembalian — baris menonjol --}}
        <div style="display:flex; justify-content:space-between; align-items:center;
                    margin-top:.5rem; padding:.6rem .75rem;
                    background:rgba(16,185,129,.07); border-radius:8px;
                    border:1px solid rgba(16,185,129,.15);">
            <span style="font-size:.9rem; font-weight:800; color:#065f46;">Kembalian</span>
            <span style="font-size:1rem; font-weight:900; color:#059669;">
                Rp {{ number_format($order->change_amount, 0, ',', '.') }}
            </span>
        </div>

        {{-- Catatan --}}
        @if($order->notes)
            <div style="margin-top:.75rem; padding:.55rem .75rem;
                        background:#fdf6ec; border-radius:8px;
                        font-size:.78rem; color:#6b3510;
                        border-left:3px solid #f59e0b;">
                📝 {{ $order->notes }}
            </div>
        @endif
    </div>

    {{-- ── Footer Nota ─────────────────────────────────────────────── --}}
    <div style="text-align:center; padding:.75rem 1.5rem 1.25rem;
                border-top:1px solid #f3dfc0;">
        <div style="display:inline-flex; align-items:center; gap:.4rem;
                    background:rgba(16,185,129,.08); border:1px solid rgba(16,185,129,.2);
                    padding:.3rem .85rem; border-radius:999px; margin-bottom:.75rem;">
            <svg width="13" height="13" fill="none" stroke="#059669" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            <span style="font-size:.72rem; font-weight:800; color:#059669;
                         text-transform:uppercase; letter-spacing:.06em;">
                Transaksi Berhasil
            </span>
        </div>
        <div style="font-size:.78rem; color:#8b4f1e; line-height:1.6;">
            Terima kasih atas kunjungan Anda!<br>
            <span style="font-weight:700;">Sampai jumpa lagi ☕</span>
        </div>

        {{-- Divider bawah --}}
        <div style="border-top:2px dashed #e3bb8a; margin:.85rem 0;"></div>

        <div style="font-size:.65rem; color:#cc9055; font-family:'DM Mono',monospace;">
            {{ $order->invoice_number }} &bull; {{ $order->created_at->format('d/m/Y H:i') }}
        </div>
    </div>

</div>