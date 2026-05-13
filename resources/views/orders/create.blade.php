<x-app-layout>
    <x-slot name="title">Kasir / POS</x-slot>
    <x-slot name="subtitle">Buat transaksi baru</x-slot>

    <div class="pos-layout">

        {{-- ── PANEL KIRI: Daftar Produk ─────────────────────────────── --}}
        <div class="pos-products">

            {{-- Sticky Search Bar --}}
            <div class="pos-sticky">
                <div class="search-box">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" id="product-search"
                           placeholder="Cari produk..." oninput="filterProducts()">
                </div>
            </div>

            {{-- Grid Produk per Kategori --}}
            @php
                $grouped = $products->groupBy(fn($p) => $p->category->name ?? 'Lainnya');
                $categoryEmojis = ['Kopi' => '☕', 'Non-Kopi' => '🧋', 'Makanan' => '🍽️', 'Snack' => '🥐', 'Minuman' => '🥤', 'Dessert' => '🍰'];
            @endphp

            @if($products->isEmpty())
                <div class="empty-state">
                    <div class="empty-state-icon">☕</div>
                    <h4>Belum ada produk aktif</h4>
                    <p>Tambahkan produk di menu Manajemen Produk.</p>
                </div>
            @else
                @foreach($grouped as $categoryName => $items)
                    <div class="mb-6 category-group" data-category="{{ strtolower($categoryName) }}">
                        <div style="display:flex; align-items:center; gap:.5rem; margin-bottom:.85rem;">
                            <span style="font-size:1rem;">{{ $categoryEmojis[$categoryName] ?? '🛍️' }}</span>
                            <h4 style="font-size:.72rem; font-weight:800; color:var(--coffee-400);
                                       text-transform:uppercase; letter-spacing:.1em;">
                                {{ $categoryName }}
                            </h4>
                            <div style="flex:1; height:1px; background:var(--coffee-100);"></div>
                        </div>
                        <div class="products-grid">
                            @foreach($items as $product)
                                <div class="product-card-pos {{ $product->stock == 0 ? 'out-of-stock' : '' }}"
                                     id="product-{{ $product->id }}"
                                     data-id="{{ $product->id }}"
                                     data-name="{{ $product->name }}"
                                     data-price="{{ (int) $product->price }}"
                                     data-stock="{{ $product->stock }}"
                                     data-search="{{ strtolower($product->name) }} {{ strtolower($categoryName) }}"
                                     onclick="addToCart(this)">

                                    {{-- Out of stock badge --}}
                                    @if($product->stock == 0)
                                        <div class="badge-oos">Habis</div>
                                    @elseif($product->stock <= 5)
                                        <div class="badge-oos" style="background:rgba(245,158,11,.1); color:var(--amber-600);">
                                            Sisa {{ $product->stock }}
                                        </div>
                                    @endif

                                    <span class="product-emoji">
                                        {{ $categoryEmojis[$categoryName] ?? '🛍️' }}
                                    </span>
                                    <div class="product-name">{{ $product->name }}</div>
                                    <div class="product-price">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- ── PANEL KANAN: Keranjang ─────────────────────────────────── --}}
        <div class="cart-panel">

            {{-- Cart Header --}}
            <div class="cart-header">
                <div style="display:flex; align-items:center; justify-content:space-between;">
                    <div>
                        <h3>
                            Keranjang Belanja
                            <span class="cart-count" id="cart-count-badge" style="display:none;">0</span>
                        </h3>
                        <p id="cart-summary-text">Klik produk untuk ditambahkan</p>
                    </div>
                    <button onclick="clearCart()" id="btn-clear"
                            style="display:none; background:rgba(255,255,255,.1); border:none; border-radius:8px;
                                   padding:.35rem .65rem; color:rgba(255,255,255,.7); cursor:pointer;
                                   font-size:.72rem; font-weight:700; font-family:'Poppins',sans-serif;
                                   transition:var(--transition);"
                            onmouseover="this.style.background='rgba(255,255,255,.2)'"
                            onmouseout="this.style.background='rgba(255,255,255,.1)'">
                        Kosongkan
                    </button>
                </div>
            </div>

            {{-- Cart Items --}}
            <div class="cart-items" id="cart-items">
                <div class="cart-empty" id="cart-empty">
                    <div class="cart-empty-icon">🛒</div>
                    <p>Pilih produk dari kiri</p>
                </div>
            </div>

            {{-- Cart Footer --}}
            <div class="cart-footer">
                <div class="cart-total-row">
                    <span>Subtotal</span>
                    <span id="subtotal-display">Rp 0</span>
                </div>

                {{-- Input uang diterima --}}
                <div class="form-group mt-3">
                    <label class="form-label" for="amount-paid">Uang Diterima (Rp)</label>
                    <input type="number" id="amount-paid" class="form-control"
                           min="0" step="1000" placeholder="0"
                           oninput="calcChange()">
                    {{-- Quick amount buttons --}}
                    <div style="display:flex; gap:.4rem; margin-top:.5rem; flex-wrap:wrap;"
                         id="quick-amounts"></div>
                </div>

                {{-- Kembalian --}}
                <div class="cart-total-row" id="change-row" style="display:none;">
                    <span>Kembalian</span>
                    <span id="change-display" style="font-weight:800;">Rp 0</span>
                </div>

                <div class="cart-total-row total">
                    <span>Total</span>
                    <span id="total-display">Rp 0</span>
                </div>

                {{-- Catatan --}}
                <div class="form-group mt-3">
                    <textarea id="notes" class="form-control" rows="2"
                              placeholder="Catatan pesanan (opsional)..."
                              style="resize:none; font-size:.82rem;"></textarea>
                </div>

                {{-- Tombol Bayar --}}
                <button id="btn-bayar" onclick="submitOrder()"
                        class="btn-pay" disabled>
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Proses Pembayaran
                </button>
            </div>
        </div>
    </div>

    {{-- Hidden form untuk submit ke server --}}
    <form id="order-form" method="POST" action="{{ route('orders.store') }}" style="display:none;">
        @csrf
        <input type="hidden" name="amount_paid" id="form-amount-paid">
        <input type="hidden" name="notes"        id="form-notes">
        <div id="form-items"></div>
    </form>

</x-app-layout>

{{-- ── JAVASCRIPT (inline, tidak pakai @push supaya pasti ter-render) ── --}}
<script>
/* ─── State ─────────────────────────────────────────────────────────── */
let cart = {}; // { productId: { name, price, quantity, stock } }

/* ─── Helpers ───────────────────────────────────────────────────────── */
function formatRp(amount) {
    return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
}

/* ─── Tambah ke keranjang ────────────────────────────────────────────── */
function addToCart(el) {
    const id    = el.dataset.id;
    const name  = el.dataset.name;
    const price = parseInt(el.dataset.price);
    const stock = parseInt(el.dataset.stock);

    if (stock === 0) return;

    if (cart[id]) {
        if (cart[id].quantity >= stock) {
            showToast('Stok tidak mencukupi! Maks ' + stock + ' item.', 'warning');
            return;
        }
        cart[id].quantity++;
    } else {
        cart[id] = { name, price, quantity: 1, stock };
    }

    el.classList.add('selected');
    renderCart();
}

/* ─── Ubah qty di keranjang ──────────────────────────────────────────── */
function changeQty(id, delta) {
    if (!cart[id]) return;
    cart[id].quantity += delta;
    if (cart[id].quantity <= 0) {
        delete cart[id];
        const el = document.getElementById('product-' + id);
        if (el) el.classList.remove('selected');
    }
    renderCart();
}

/* ─── Kosongkan keranjang ────────────────────────────────────────────── */
function clearCart() {
    cart = {};
    document.querySelectorAll('.product-card-pos.selected')
            .forEach(el => el.classList.remove('selected'));
    renderCart();
}

/* ─── Render ulang list keranjang ───────────────────────────────────── */
function renderCart() {
    const container = document.getElementById('cart-items');
    const emptyEl   = document.getElementById('cart-empty');
    const ids       = Object.keys(cart);

    // Hitung totals
    let totalQty   = 0;
    let totalPrice = 0;
    ids.forEach(id => {
        totalQty   += cart[id].quantity;
        totalPrice += cart[id].price * cart[id].quantity;
    });

    // Tampilkan/sembunyikan empty state
    if (ids.length === 0) {
        container.innerHTML = `
            <div class="cart-empty" id="cart-empty">
                <div class="cart-empty-icon">🛒</div>
                <p>Pilih produk dari kiri</p>
            </div>`;
        document.getElementById('cart-count-badge').style.display = 'none';
        document.getElementById('btn-clear').style.display        = 'none';
        document.getElementById('cart-summary-text').textContent  = 'Klik produk untuk ditambahkan';
    } else {
        // Build cart item HTML
        let html = '';
        ids.forEach(id => {
            const item = cart[id];
            html += `
            <div class="cart-item">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">
                        ${formatRp(item.price)} &times; ${item.quantity}
                        &nbsp;=&nbsp;
                        <strong>${formatRp(item.price * item.quantity)}</strong>
                    </div>
                </div>
                <div class="cart-qty">
                    <button class="qty-btn" onclick="changeQty('${id}', -1)">−</button>
                    <span class="qty-display">${item.quantity}</span>
                    <button class="qty-btn" onclick="changeQty('${id}', 1)"
                            ${item.quantity >= item.stock ? 'disabled style="opacity:.4;cursor:not-allowed"' : ''}>+</button>
                </div>
            </div>`;
        });
        container.innerHTML = html;

        // Update header badge
        const badge = document.getElementById('cart-count-badge');
        badge.textContent  = totalQty;
        badge.style.display = 'inline-flex';
        document.getElementById('btn-clear').style.display       = 'inline-block';
        document.getElementById('cart-summary-text').textContent = totalQty + ' item dipilih';
    }

    // Update totals
    document.getElementById('subtotal-display').textContent = formatRp(totalPrice);
    document.getElementById('total-display').textContent    = formatRp(totalPrice);

    // Update quick amount buttons
    renderQuickAmounts(totalPrice);

    // Tombol bayar aktif hanya jika ada item
    document.getElementById('btn-bayar').disabled = ids.length === 0;

    calcChange();
}

/* ─── Quick amount buttons (uang pas & pembulatan) ──────────────────── */
function renderQuickAmounts(total) {
    const container = document.getElementById('quick-amounts');
    if (!total) { container.innerHTML = ''; return; }

    // Buat beberapa pilihan nominal uang
    const options = new Set();
    options.add(total);                                           // uang pas
    options.add(Math.ceil(total / 5000)  * 5000);               // bulat 5rb
    options.add(Math.ceil(total / 10000) * 10000);              // bulat 10rb
    options.add(Math.ceil(total / 50000) * 50000);              // bulat 50rb

    container.innerHTML = [...options].slice(0, 4).map(amt => `
        <button onclick="setAmount(${amt})"
                style="padding:.3rem .65rem; font-size:.7rem; font-weight:700;
                       background:var(--coffee-50); border:1.5px solid var(--coffee-100);
                       border-radius:8px; cursor:pointer; font-family:'Poppins',sans-serif;
                       color:var(--coffee-700); transition:var(--transition);"
                onmouseover="this.style.borderColor='var(--amber-400)'; this.style.color='var(--amber-600)'"
                onmouseout="this.style.borderColor='var(--coffee-100)'; this.style.color='var(--coffee-700)'">
            ${formatRp(amt)}
        </button>`).join('');
}

function setAmount(amount) {
    document.getElementById('amount-paid').value = amount;
    calcChange();
}

/* ─── Hitung kembalian ───────────────────────────────────────────────── */
function calcChange() {
    const paid      = parseInt(document.getElementById('amount-paid').value) || 0;
    const totalEl   = document.getElementById('total-display').textContent;
    // Parse total dari display text (hapus "Rp " dan titik)
    const total     = parseInt(totalEl.replace(/[^0-9]/g, '')) || 0;
    const changeRow = document.getElementById('change-row');
    const changeEl  = document.getElementById('change-display');

    if (paid > 0 && total > 0) {
        const change = paid - total;
        changeEl.textContent  = formatRp(change);
        changeEl.style.color  = change >= 0 ? 'var(--success)' : 'var(--danger)';
        changeRow.style.display = 'flex';
    } else {
        changeRow.style.display = 'none';
    }
}

/* ─── Submit order ke server ─────────────────────────────────────────── */
function submitOrder() {
    const ids   = Object.keys(cart);
    const paid  = parseInt(document.getElementById('amount-paid').value) || 0;
    const notes = document.getElementById('notes').value;

    if (ids.length === 0) { showToast('Keranjang masih kosong!', 'warning'); return; }

    // Hitung total aktual dari cart (bukan dari display)
    let total = 0;
    ids.forEach(id => total += cart[id].price * cart[id].quantity);

    if (paid < total) {
        showToast('Uang diterima kurang! Total: ' + formatRp(total), 'error');
        document.getElementById('amount-paid').focus();
        return;
    }

    // Isi hidden form
    document.getElementById('form-amount-paid').value = paid;
    document.getElementById('form-notes').value        = notes;

    const formItems = document.getElementById('form-items');
    formItems.innerHTML = '';
    ids.forEach((id, idx) => {
        formItems.innerHTML += `
            <input type="hidden" name="items[${idx}][product_id]" value="${id}">
            <input type="hidden" name="items[${idx}][quantity]"   value="${cart[id].quantity}">`;
    });

    // Disable tombol supaya tidak double-submit
    const btn = document.getElementById('btn-bayar');
    btn.disabled     = true;
    btn.innerHTML    = '⏳ Memproses...';

    document.getElementById('order-form').submit();
}

/* ─── Filter produk berdasarkan search input ─────────────────────────── */
function filterProducts() {
    const q = document.getElementById('product-search').value.toLowerCase().trim();

    document.querySelectorAll('.product-card-pos').forEach(el => {
        const match = el.dataset.search.includes(q);
        el.style.display = match ? '' : 'none';
    });

    // Sembunyikan header kategori jika semua produknya hilang
    document.querySelectorAll('.category-group').forEach(group => {
        const visible = [...group.querySelectorAll('.product-card-pos')]
                            .some(el => el.style.display !== 'none');
        group.style.display = visible ? '' : 'none';
    });
}

/* ─── Toast notification mini ───────────────────────────────────────── */
function showToast(msg, type = 'info') {
    const colors = {
        warning: { bg: 'rgba(245,158,11,.95)', color: '#fff' },
        error:   { bg: 'rgba(244,63,94,.95)',  color: '#fff' },
        success: { bg: 'rgba(16,185,129,.95)', color: '#fff' },
        info:    { bg: 'rgba(26,13,0,.9)',     color: '#fff' },
    };
    const c = colors[type] || colors.info;

    const toast = document.createElement('div');
    toast.textContent = msg;
    Object.assign(toast.style, {
        position: 'fixed', bottom: '1.5rem', right: '1.5rem', zIndex: '9999',
        background: c.bg, color: c.color,
        padding: '.75rem 1.25rem', borderRadius: '10px',
        fontSize: '.875rem', fontWeight: '700', fontFamily: "'Poppins', sans-serif",
        boxShadow: '0 8px 24px rgba(0,0,0,.2)',
        animation: 'slideUp .25s ease both',
        maxWidth: '320px',
    });
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>