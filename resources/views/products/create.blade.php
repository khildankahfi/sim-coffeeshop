<x-app-layout>
    <x-slot name="title">Tambah Produk</x-slot>
    <x-slot name="subtitle">Tambahkan menu baru ke daftar produk</x-slot>

    <div style="max-width:720px; margin:0 auto;">
        <div class="card">

            {{-- Header --}}
            <div class="card-header">
                <h3>Form Produk Baru</h3>
                <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data"
                      id="product-form">
                    @csrf

                    {{-- ── BARIS 1: Nama + Kategori ──────────────────────── --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label class="form-label" for="name">
                                Nama Produk <span class="required">*</span>
                            </label>
                            <input id="name" type="text" name="name"
                                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                   value="{{ old('name') }}"
                                   placeholder="Contoh: Cappuccino" autofocus required>
                            @error('name')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="category_id">
                                Kategori <span class="required">*</span>
                            </label>
                            <select id="category_id" name="category_id"
                                    class="form-control {{ $errors->has('category_id') ? 'is-invalid' : '' }}"
                                    required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- ── BARIS 2: Harga + Stok ─────────────────────────── --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label class="form-label" for="price">
                                Harga <span class="required">*</span>
                            </label>
                            {{-- Wrapper dengan prefix Rp --}}
                            <div style="position:relative;">
                                <span style="position:absolute; left:.9rem; top:50%; transform:translateY(-50%);
                                             font-size:.82rem; font-weight:700; color:var(--coffee-400);
                                             pointer-events:none;">Rp</span>
                                <input id="price" type="number" name="price" min="0" step="500"
                                       class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}"
                                       style="padding-left:2.5rem;"
                                       value="{{ old('price') }}"
                                       placeholder="25000" required
                                       oninput="updatePricePreview()">
                            </div>
                            {{-- Live preview format Rupiah --}}
                            <div id="price-preview" class="form-hint" style="min-height:1.2em;"></div>
                            @error('price')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="stock">
                                Stok Awal <span class="required">*</span>
                            </label>
                            <input id="stock" type="number" name="stock" min="0"
                                   class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}"
                                   value="{{ old('stock', 0) }}" required>
                            <div class="form-hint">Isi 0 jika stok belum tersedia.</div>
                            @error('stock')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- ── Deskripsi ──────────────────────────────────────── --}}
                    <div class="form-group">
                        <label class="form-label" for="description">Deskripsi</label>
                        <textarea id="description" name="description" rows="3"
                                  class="form-control" style="resize:vertical;"
                                  placeholder="Deskripsi singkat produk (opsional)...">{{ old('description') }}</textarea>
                    </div>

                    {{-- ── Upload Foto ────────────────────────────────────── --}}
                    <div class="form-group">
                        <label class="form-label">Foto Produk</label>
                        <div id="upload-area"
                             style="border:2px dashed var(--coffee-200); border-radius:10px;
                                    padding:1.5rem; text-align:center; cursor:pointer;
                                    background:var(--coffee-50); transition:var(--transition);"
                             onclick="document.getElementById('image').click()"
                             ondragover="event.preventDefault(); this.style.borderColor='var(--amber-400)'"
                             ondragleave="this.style.borderColor='var(--coffee-200)'"
                             ondrop="handleDrop(event)">
                            <div id="upload-placeholder">
                                <div style="font-size:2rem; margin-bottom:.5rem; opacity:.4;">🖼️</div>
                                <div style="font-size:.85rem; font-weight:700; color:var(--coffee-500);">
                                    Klik atau drag foto ke sini
                                </div>
                                <div style="font-size:.75rem; color:var(--coffee-400); margin-top:.25rem;">
                                    JPG, PNG, WebP — Maks 2MB
                                </div>
                            </div>
                            <img id="img-preview" src="#" alt="Preview"
                                 style="display:none; max-height:180px; max-width:100%;
                                        border-radius:8px; object-fit:cover; margin:0 auto;">
                        </div>
                        <input id="image" type="file" name="image" accept="image/*"
                               style="display:none;"
                               class="{{ $errors->has('image') ? 'is-invalid' : '' }}"
                               onchange="previewImage(this)">
                        @error('image')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- ── Status Toggle ──────────────────────────────────── --}}
                    <div class="form-group">
                        <label class="form-label">Status Produk</label>
                        <div class="toggle-wrapper">
                            <label class="toggle">
                                <input type="checkbox" name="is_active" value="1"
                                       id="toggle-active"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       onchange="updateToggleLabel()">
                                <span class="toggle-slider"></span>
                            </label>
                            <span id="toggle-label" style="font-size:.875rem; font-weight:600;
                                  color:var(--coffee-700);">
                                {{ old('is_active', true) ? 'Aktif — tampil di kasir' : 'Nonaktif — disembunyikan' }}
                            </span>
                        </div>
                    </div>

                    {{-- ── Tombol Aksi ────────────────────────────────────── --}}
                    <div style="border-top:1px solid var(--coffee-100); margin-top:1.5rem;
                                padding-top:1.5rem; display:flex; gap:.75rem;">
                        <button type="submit" class="btn btn-primary">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Produk
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
/* ── Live preview harga ──────────────────────────────────────────────── */
function updatePricePreview() {
    const val = parseInt(document.getElementById('price').value) || 0;
    const el  = document.getElementById('price-preview');
    el.textContent = val > 0 ? '= Rp ' + val.toLocaleString('id-ID') : '';
    el.style.color = 'var(--amber-500)';
    el.style.fontWeight = '700';
}

/* ── Preview gambar dari file input ─────────────────────────────────── */
function previewImage(input) {
    if (!input.files || !input.files[0]) return;
    showPreview(URL.createObjectURL(input.files[0]));
}

/* ── Drag & drop ─────────────────────────────────────────────────────── */
function handleDrop(event) {
    event.preventDefault();
    document.getElementById('upload-area').style.borderColor = 'var(--coffee-200)';
    const file = event.dataTransfer.files[0];
    if (!file || !file.type.startsWith('image/')) return;

    // Inject file ke input supaya form bisa submit
    const dt  = new DataTransfer();
    dt.items.add(file);
    document.getElementById('image').files = dt.files;
    showPreview(URL.createObjectURL(file));
}

function showPreview(url) {
    document.getElementById('upload-placeholder').style.display = 'none';
    const img = document.getElementById('img-preview');
    img.src   = url;
    img.style.display = 'block';
}

/* ── Toggle label aktif/nonaktif ─────────────────────────────────────── */
function updateToggleLabel() {
    const checked = document.getElementById('toggle-active').checked;
    document.getElementById('toggle-label').textContent = checked
        ? 'Aktif — tampil di kasir'
        : 'Nonaktif — disembunyikan';
}

/* ── Init: tampilkan preview harga jika ada old value ────────────────── */
updatePricePreview();
</script>