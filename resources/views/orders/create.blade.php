<x-app-layout>
    <x-slot name="title">Kasir / POS</x-slot>
    <x-slot name="subtitle">Buat transaksi baru</x-slot>

    {{-- ── TAB SWITCHER (mobile only) ─────────────────────────────────── --}}
    <div class="pos-tabs">
        <button class="pos-tab active" id="tab-products" onclick="switchTab('products')">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            Produk
        </button>
        <button class="pos-tab" id="tab-cart" onclick="switchTab('cart')">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Keranjang
            <span class="pos-tab-badge" id="tab-cart-badge" style="display:none;">0</span>
        </button>
    </div>

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
                    <div style="display:flex; align-items:center; gap:.5rem;">
                        {{-- Shortcut hint button --}}
                        <button onclick="document.getElementById('shortcut-modal').style.display='flex'"
                                title="Keyboard Shortcuts (F1)"
                                style="background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12);
                                       border-radius:8px; padding:.3rem .55rem; cursor:pointer;
                                       color:rgba(255,255,255,.5); font-size:.65rem; font-weight:700;
                                       font-family:'Poppins',sans-serif; letter-spacing:.04em;
                                       transition:var(--transition);"
                                onmouseover="this.style.background='rgba(255,255,255,.15)'; this.style.color='#fff'"
                                onmouseout="this.style.background='rgba(255,255,255,.08)'; this.style.color='rgba(255,255,255,.5)'">
                            ⌨️ F1
                        </button>
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
            showToast(`Stok "${name}" habis! Maksimal ${stock} item.`, 'error');
            shakeElement(el);
            return;
        }
        cart[id].quantity++;
    } else {
        cart[id] = { name, price, quantity: 1, stock };
    }

    el.classList.add('selected');
    renderCart();

    // ── Peringatan stok setelah penambahan ──────────────────────────
    const remaining = stock - cart[id].quantity;
    if (remaining === 0) {
        showToast(`⚠️ Stok "${name}" habis terpakai!`, 'error');
        // Redupkan card produk secara visual
        el.style.opacity = '.5';
        el.style.filter  = 'grayscale(.5)';
    } else if (remaining <= 2) {
        showToast(`🔴 Sisa stok "${name}": ${remaining} item`, 'error');
        updateProductBadge(el, remaining, 'critical');
    } else if (remaining <= 5) {
        showToast(`⚠️ Stok "${name}" menipis: sisa ${remaining}`, 'warning');
        updateProductBadge(el, remaining, 'low');
    }
}

/* ── Update badge stok di product card secara real-time ─────────────── */
function updateProductBadge(el, remaining, level) {
    let badge = el.querySelector('.badge-oos');
    if (!badge) {
        badge = document.createElement('div');
        badge.className = 'badge-oos';
        el.appendChild(badge);
    }
    if (level === 'critical') {
        badge.textContent = `Sisa ${remaining}!`;
        badge.style.cssText = 'background:rgba(244,63,94,.15); color:var(--danger); font-weight:800;';
    } else {
        badge.textContent = `Sisa ${remaining}`;
        badge.style.cssText = 'background:rgba(245,158,11,.15); color:#b45309; font-weight:800;';
    }
}

/* ── Animasi shake saat stok habis ──────────────────────────────────── */
function shakeElement(el) {
    el.style.animation = 'none';
    el.offsetHeight; // reflow
    el.style.animation = 'shake .35s ease';
    setTimeout(() => el.style.animation = '', 400);
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
            const item      = cart[id];
            const remaining = item.stock - item.quantity;
            const isLow     = remaining <= 5 && remaining > 0;
            const isEmpty   = remaining === 0;

            // Warna border item berdasarkan kondisi stok
            const borderColor = isEmpty
                ? 'rgba(244,63,94,.25)'
                : isLow ? 'rgba(245,158,11,.25)' : 'rgba(0,0,0,.04)';

            html += `
            <div class="cart-item" style="border-color:${borderColor};">
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">
                        ${formatRp(item.price)} &times; ${item.quantity}
                        &nbsp;=&nbsp;
                        <strong>${formatRp(item.price * item.quantity)}</strong>
                    </div>
                    ${isEmpty
                        ? `<div style="font-size:.68rem; font-weight:800; color:var(--danger);
                                       margin-top:2px;">🔴 Stok habis terpakai</div>`
                        : isLow
                            ? `<div style="font-size:.68rem; font-weight:700; color:#b45309;
                                           margin-top:2px;">⚠️ Sisa stok: ${remaining}</div>`
                            : ''}
                </div>
                <div class="cart-qty">
                    <button class="qty-btn" onclick="changeQty('${id}', -1)">−</button>
                    <span class="qty-display">${item.quantity}</span>
                    <button class="qty-btn" onclick="changeQty('${id}', 1)"
                            ${isEmpty ? 'disabled style="opacity:.4;cursor:not-allowed"' : ''}>+</button>
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
async function submitOrder() {
    const ids   = Object.keys(cart);
    const paid  = parseInt(document.getElementById('amount-paid').value) || 0;
    const notes = document.getElementById('notes').value;

    if (ids.length === 0) { showToast('Keranjang masih kosong!', 'warning'); return; }

    let total = 0;
    ids.forEach(id => total += cart[id].price * cart[id].quantity);

    if (paid < total) {
        showToast('Uang diterima kurang! Total: ' + formatRp(total), 'error');
        document.getElementById('amount-paid').focus();
        return;
    }

    // ── AJAX: validasi stok terkini ke server sebelum submit ────────
    // Mencegah race condition: kasir A & kasir B pesan produk sama
    const btn = document.getElementById('btn-bayar');
    btn.disabled  = true;
    btn.innerHTML = '🔍 Cek stok...';

    try {
        const payload = ids.map(id => ({
            product_id: id,
            quantity:   cart[id].quantity,
        }));

        const resp = await fetch('{{ route("orders.checkStock") }}', {
            method:  'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ items: payload }),
        });

        const result = await resp.json();

        // Jika ada produk stok tidak cukup, tampilkan error dan batalkan
        if (!result.ok) {
            btn.disabled  = false;
            btn.innerHTML = `<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg> Proses Pembayaran`;

            // Update stok di cart berdasarkan data terbaru dari server
            result.errors.forEach(err => {
                showToast(`❌ ${err.name}: stok tersedia hanya ${err.available}, kamu pesan ${err.requested}`, 'error');

                // Update stok di cart & product card
                if (cart[err.product_id]) {
                    cart[err.product_id].stock = err.available;
                    // Kalau qty di cart melebihi stok terbaru, potong
                    if (cart[err.product_id].quantity > err.available) {
                        cart[err.product_id].quantity = err.available;
                    }
                }

                // Update data-stock di product card
                const card = document.getElementById('product-' + err.product_id);
                if (card) {
                    card.dataset.stock = err.available;
                    updateProductBadge(card, err.available - (cart[err.product_id]?.quantity || 0), err.available <= 2 ? 'critical' : 'low');
                }
            });

            renderCart();
            return;
        }
    } catch (err) {
        // Kalau AJAX gagal (offline / server error), tetap lanjut submit biasa
        console.warn('Stock check failed, proceeding anyway:', err);
    }

    // ── Semua OK, submit form ────────────────────────────────────────
    btn.innerHTML = '⏳ Memproses...';

    document.getElementById('form-amount-paid').value = paid;
    document.getElementById('form-notes').value        = notes;

    const formItems = document.getElementById('form-items');
    formItems.innerHTML = '';
    ids.forEach((id, idx) => {
        formItems.innerHTML += `
            <input type="hidden" name="items[${idx}][product_id]" value="${id}">
            <input type="hidden" name="items[${idx}][quantity]"   value="${cart[id].quantity}">`;
    });

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

/* ─── KEYBOARD SHORTCUTS ─────────────────────────────────────────────────
 *
 *  F1 / ?          → tampilkan daftar shortcut
 *  /               → fokus ke search produk
 *  Escape          → clear search / tutup shortcut modal
 *  Enter           → proses pembayaran (jika cart tidak kosong)
 *  Backspace       → clear keranjang (jika focus bukan di input)
 *  0-9 (numpad)    → preset nominal uang (lihat AMOUNT_PRESETS)
 *  + / =           → qty +1 item terakhir di cart
 *  - / _           → qty -1 item terakhir di cart
 *  Delete          → hapus item terakhir di cart
 *
 * Semua shortcut DINONAKTIFKAN saat focus ada di dalam input/textarea.
 * ─────────────────────────────────────────────────────────────────────── */

// Preset nominal tombol angka 1-9 (dalam Rupiah)
const AMOUNT_PRESETS = {
    '1': 5000,
    '2': 10000,
    '3': 20000,
    '4': 50000,
    '5': 100000,
    '6': 50000,  // duplikat aman
    '7': 200000,
    '8': 500000,
    '9': 1000000,
};

function isTyping() {
    const tag = document.activeElement?.tagName?.toLowerCase();
    return tag === 'input' || tag === 'textarea' || tag === 'select';
}

function getLastCartId() {
    const ids = Object.keys(cart);
    return ids.length ? ids[ids.length - 1] : null;
}

document.addEventListener('keydown', function(e) {
    // ── / → fokus search ──────────────────────────────────────────
    if (e.key === '/' && !isTyping()) {
        e.preventDefault();
        const s = document.getElementById('product-search');
        s.focus(); s.select();
        return;
    }

    // ── Escape → clear search atau tutup shortcut modal ───────────
    if (e.key === 'Escape') {
        const modal = document.getElementById('shortcut-modal');
        if (modal && modal.style.display !== 'none') {
            modal.style.display = 'none';
            return;
        }
        const s = document.getElementById('product-search');
        if (document.activeElement === s) {
            s.value = ''; filterProducts(); s.blur();
        }
        return;
    }

    // Shortcut di bawah ini tidak aktif saat sedang mengetik
    if (isTyping()) return;

    // ── F1 atau ? → tampilkan shortcut cheatsheet ────────────────
    if (e.key === 'F1' || e.key === '?') {
        e.preventDefault();
        document.getElementById('shortcut-modal').style.display = 'flex';
        return;
    }

    // ── Enter → submit order ──────────────────────────────────────
    if (e.key === 'Enter') {
        e.preventDefault();
        if (Object.keys(cart).length > 0) {
            const amountEl = document.getElementById('amount-paid');
            if (!amountEl.value || parseInt(amountEl.value) === 0) {
                // Kalau nominal belum diisi, fokus ke field nominal dulu
                amountEl.focus();
                showToast('Isi nominal uang diterima dulu, lalu Enter lagi.', 'warning');
            } else {
                submitOrder();
            }
        } else {
            showToast('Keranjang masih kosong!', 'warning');
        }
        return;
    }

    // ── Backspace → kosongkan cart ────────────────────────────────
    if (e.key === 'Backspace') {
        e.preventDefault();
        if (Object.keys(cart).length > 0) {
            clearCart();
            showToast('Keranjang dikosongkan.', 'info');
        }
        return;
    }

    // ── Delete → hapus item terakhir di cart ─────────────────────
    if (e.key === 'Delete') {
        e.preventDefault();
        const id = getLastCartId();
        if (id) {
            const name = cart[id].name;
            changeQty(id, -cart[id].quantity); // hapus semua qty-nya
            showToast(`"${name}" dihapus dari keranjang.`, 'info');
        }
        return;
    }

    // ── + / = → tambah qty item terakhir ─────────────────────────
    if (e.key === '+' || e.key === '=') {
        e.preventDefault();
        const id = getLastCartId();
        if (id) { changeQty(id, 1); showToast(`+1 ${cart[id]?.name || ''}`, 'success'); }
        return;
    }

    // ── - / _ → kurangi qty item terakhir ────────────────────────
    if (e.key === '-' || e.key === '_') {
        e.preventDefault();
        const id = getLastCartId();
        if (id) {
            const name = cart[id].name;
            changeQty(id, -1);
            showToast(`-1 ${name}`, 'info');
        }
        return;
    }

    // ── Angka 1-9 → preset nominal uang ──────────────────────────
    if (AMOUNT_PRESETS[e.key]) {
        e.preventDefault();
        // Hitung total dulu dari cart
        let total = 0;
        Object.values(cart).forEach(item => total += item.price * item.quantity);
        if (total === 0) { showToast('Tambahkan produk dulu.', 'warning'); return; }

        const preset = AMOUNT_PRESETS[e.key];
        // Kalau preset < total, pakai yang lebih besar (uang pas)
        const amount = preset >= total ? preset : Math.ceil(total / preset) * preset;
        setAmount(amount);
        showToast(`Nominal: Rp ${amount.toLocaleString('id-ID')}`, 'success');
        return;
    }
});

/* ─── SHORTCUT CHEATSHEET MODAL ──────────────────────────────────────── */
(function buildShortcutModal() {
    const shortcuts = [
        { key: '/',         desc: 'Fokus ke pencarian produk' },
        { key: 'Enter',     desc: 'Proses pembayaran' },
        { key: 'Backspace', desc: 'Kosongkan keranjang' },
        { key: 'Delete',    desc: 'Hapus item terakhir di keranjang' },
        { key: '+ / =',     desc: 'Tambah qty item terakhir' },
        { key: '- / _',     desc: 'Kurangi qty item terakhir' },
        { key: 'Escape',    desc: 'Tutup pencarian / modal ini' },
        { key: '1',         desc: 'Nominal Rp 5.000' },
        { key: '2',         desc: 'Nominal Rp 10.000' },
        { key: '3',         desc: 'Nominal Rp 20.000' },
        { key: '4',         desc: 'Nominal Rp 50.000' },
        { key: '5 / 6',     desc: 'Nominal Rp 100.000' },
        { key: '7',         desc: 'Nominal Rp 200.000' },
        { key: '8',         desc: 'Nominal Rp 500.000' },
        { key: '9',         desc: 'Nominal Rp 1.000.000' },
        { key: 'F1 / ?',    desc: 'Tampilkan shortcut ini' },
    ];

    const rows = shortcuts.map(s => `
        <div style="display:flex; align-items:center; gap:.75rem; padding:.45rem 0;
                    border-bottom:1px solid rgba(0,0,0,.04);">
            <kbd style="display:inline-flex; align-items:center; justify-content:center;
                        background:var(--coffee-50); border:1.5px solid var(--coffee-100);
                        border-radius:6px; padding:.2rem .55rem; font-size:.75rem;
                        font-weight:800; color:var(--coffee-700); font-family:'DM Mono',monospace;
                        min-width:72px; text-align:center; white-space:nowrap;
                        box-shadow:0 2px 0 var(--coffee-200);">
                ${s.key}
            </kbd>
            <span style="font-size:.83rem; color:var(--coffee-600); font-weight:500;">
                ${s.desc}
            </span>
        </div>`).join('');

    const modal = document.createElement('div');
    modal.id = 'shortcut-modal';
    modal.style.cssText = `
        display:none; position:fixed; inset:0; z-index:9999;
        background:rgba(0,0,0,.5); backdrop-filter:blur(4px);
        align-items:center; justify-content:center; padding:1rem;`;

    modal.innerHTML = `
        <div style="background:#fff; border-radius:1rem; padding:1.75rem;
                    max-width:460px; width:100%; max-height:90vh; overflow-y:auto;
                    box-shadow:0 24px 60px rgba(0,0,0,.18);
                    animation:slideUp .25s ease both;">
            <div style="display:flex; align-items:center; justify-content:space-between;
                        margin-bottom:1.25rem;">
                <div>
                    <h3 style="font-size:1rem; font-weight:800; color:var(--coffee-950);">
                        ⌨️ Keyboard Shortcuts
                    </h3>
                    <p style="font-size:.75rem; color:var(--coffee-400); margin-top:2px;">
                        Aktif saat tidak sedang mengetik di input
                    </p>
                </div>
                <button onclick="document.getElementById('shortcut-modal').style.display='none'"
                        style="background:var(--coffee-50); border:1.5px solid var(--coffee-100);
                               border-radius:8px; padding:.35rem .65rem; cursor:pointer;
                               font-size:.75rem; font-weight:700; color:var(--coffee-600);
                               font-family:'Poppins',sans-serif;">
                    Tutup
                </button>
            </div>
            ${rows}
            <div style="margin-top:1rem; padding:.65rem .85rem; background:var(--coffee-50);
                        border-radius:8px; font-size:.75rem; color:var(--coffee-500); font-weight:500;">
                💡 Tekan <kbd style="background:var(--coffee-100); border-radius:4px; padding:.1rem .4rem;
                                     font-size:.72rem; font-weight:800;">F1</kbd> atau
                <kbd style="background:var(--coffee-100); border-radius:4px; padding:.1rem .4rem;
                             font-size:.72rem; font-weight:800;">?</kbd>
                kapan saja untuk membuka cheatsheet ini.
            </div>
        </div>`;

    modal.addEventListener('click', e => { if (e.target === modal) modal.style.display = 'none'; });
    document.body.appendChild(modal);
})();

/* ─── MOBILE TAB SWITCHING ───────────────────────────────────────────── */
function switchTab(tab) {
    const productsPanel = document.querySelector('.pos-products');
    const cartPanel     = document.querySelector('.cart-panel');
    const tabProducts   = document.getElementById('tab-products');
    const tabCart       = document.getElementById('tab-cart');

    if (tab === 'products') {
        productsPanel.style.display = '';
        cartPanel.style.display     = 'none';
        tabProducts.classList.add('active');
        tabCart.classList.remove('active');
    } else {
        productsPanel.style.display = 'none';
        cartPanel.style.display     = '';
        tabProducts.classList.remove('active');
        tabCart.classList.add('active');
    }
}

// Update badge di tab cart setiap kali renderCart dipanggil
const _origRenderCart = renderCart;
renderCart = function() {
    _origRenderCart();
    const totalQty = Object.values(cart).reduce((s, i) => s + i.quantity, 0);
    const badge    = document.getElementById('tab-cart-badge');
    if (badge) {
        badge.textContent   = totalQty;
        badge.style.display = totalQty > 0 ? 'inline-flex' : 'none';
    }
};

// Init: di desktop semua tampil, di mobile hanya produk
function initTabs() {
    const products = document.querySelector('.pos-products');
    const cart_    = document.querySelector('.cart-panel');
    if (window.innerWidth <= 768) {
        products.style.display = '';
        cart_.style.display    = 'none';
        document.getElementById('tab-products').classList.add('active');
        document.getElementById('tab-cart').classList.remove('active');
    } else {
        products.style.display = '';
        cart_.style.display    = '';
    }
}
window.addEventListener('resize', initTabs);
initTabs();
</script>

<style>
/* ─── POS TAB SWITCHER (mobile only) ────────────────────────────────── */
.pos-tabs { display: none; }

.pos-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .45rem;
    padding: .65rem 1rem;
    border: none;
    border-radius: 9px;
    font-size: .875rem;
    font-weight: 700;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    background: transparent;
    color: var(--coffee-400);
    transition: var(--transition);
}
.pos-tab.active {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    box-shadow: 0 4px 12px rgba(245,158,11,.3);
}
.pos-tab-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--danger);
    color: #fff;
    font-size: .65rem;
    font-weight: 900;
    min-width: 18px;
    height: 18px;
    border-radius: 999px;
    padding: 0 4px;
}

@media (max-width: 768px) {
    .pos-tabs {
        display: flex;
        background: #fff;
        border: 1px solid var(--coffee-100);
        border-radius: 12px;
        padding: .35rem;
        margin-bottom: 1rem;
        gap: .35rem;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
    }
    .pos-layout {
        grid-template-columns: 1fr !important;
        height: auto !important;
    }
}
</style>