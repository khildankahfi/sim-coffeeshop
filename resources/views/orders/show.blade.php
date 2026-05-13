<x-app-layout>
    <x-slot name="title">Detail Transaksi</x-slot>
    <x-slot name="subtitle">Invoice: {{ $order->invoice_number }}</x-slot>
    <x-slot name="actions">
        <button onclick="window.print()" class="btn btn-secondary no-print">🖨️ Cetak Nota</button>
        <a href="{{ route('orders.index') }}" class="btn btn-secondary no-print">← Kembali</a>
    </x-slot>

    <div style="max-width:520px;margin:0 auto">
        <div class="card">
            {{-- Header Nota --}}
            <div style="text-align:center;padding:1.5rem 1.5rem 1rem;border-bottom:2px dashed var(--coffee-100)">
                <div style="font-size:2rem;margin-bottom:.5rem">☕</div>
                <h2 style="font-size:1.1rem;font-weight:800;color:var(--coffee-900)">SIM Coffeeshop</h2>
                <p style="font-size:.78rem;color:var(--coffee-400)">Nota Pembelian</p>
                <div style="margin-top:.75rem;font-size:.82rem">
                    <div class="fw-semibold" style="color:var(--amber-500)">{{ $order->invoice_number }}</div>
                    <div class="text-muted">{{ $order->created_at->format('d F Y, H:i') }}</div>
                    <div class="text-muted">Kasir: {{ $order->user->name }}</div>
                </div>
            </div>

            {{-- Item List --}}
            <div style="padding:1rem 1.5rem">
                <table style="width:100%;font-size:.85rem">
                    <thead>
                        <tr style="border-bottom:1px solid var(--coffee-100)">
                            <th style="padding:.5rem 0;text-align:left;color:var(--coffee-500);font-weight:600">Produk</th>
                            <th style="padding:.5rem 0;text-align:center;color:var(--coffee-500);font-weight:600">Qty</th>
                            <th style="padding:.5rem 0;text-align:right;color:var(--coffee-500);font-weight:600">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr style="border-bottom:1px solid var(--coffee-50)">
                            <td style="padding:.6rem 0">
                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                <div class="text-muted" style="font-size:.75rem">Rp {{ number_format($item->price, 0, ',', '.') }} / item</div>
                            </td>
                            <td style="padding:.6rem 0;text-align:center">{{ $item->quantity }}</td>
                            <td style="padding:.6rem 0;text-align:right;font-weight:600">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Ringkasan Pembayaran --}}
            <div style="padding:1rem 1.5rem;border-top:2px dashed var(--coffee-100)">
                <div class="cart-total-row">
                    <span>Total Belanja</span>
                    <span class="fw-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="cart-total-row">
                    <span>Uang Diterima</span>
                    <span>Rp {{ number_format($order->amount_paid, 0, ',', '.') }}</span>
                </div>
                <div class="cart-total-row" style="font-weight:700;font-size:1rem;color:var(--success);margin-top:.5rem;padding-top:.5rem;border-top:1px solid var(--coffee-100)">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($order->change_amount, 0, ',', '.') }}</span>
                </div>
                @if($order->notes)
                <div style="margin-top:.75rem;padding:.6rem;background:var(--coffee-50);border-radius:.4rem;font-size:.8rem;color:var(--coffee-600)">
                    📝 {{ $order->notes }}
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div style="text-align:center;padding:1rem;border-top:1px solid var(--coffee-100);font-size:.78rem;color:var(--coffee-400)">
                Terima kasih atas kunjungan Anda! ☕<br>
                <span class="badge badge-success mt-1">✅ {{ ucfirst($order->status) }}</span>
            </div>
        </div>

        <div class="d-flex gap-3 mt-3 no-print" style="justify-content:center">
            <a href="{{ route('orders.create') }}" class="btn btn-primary">➕ Transaksi Baru</a>
            <a href="{{ route('orders.index') }}" class="btn btn-secondary">📋 Riwayat</a>
        </div>
    </div>
</x-app-layout>
