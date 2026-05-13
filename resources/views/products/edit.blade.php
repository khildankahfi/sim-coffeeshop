<x-app-layout>
    <x-slot name="title">Edit Produk</x-slot>
    <x-slot name="subtitle">{{ $product->name }}</x-slot>

    <div style="max-width:720px; margin:0 auto;">
        <div class="card">

            {{-- Header --}}
            <div class="card-header">
                <h3>Edit Produk</h3>
                <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('products.update', $product) }}"
                      enctype="multipart/form-data" id="product-form">
                    @csrf
                    @method('PATCH')

                    {{-- ── BARIS 1: Nama + Kategori ──────────────────────── --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div class="form-group">
                            <label class="form-label" for="name">
                                Nama Produk <span class="required">*</span>
                            </label>
                            <input id="name" type="text" name="name"
                                   class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                   value="{{ old('name', $product->name) }}" required autofocus>
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
                                        {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
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
                            <div style="position:relative;">
                                <span style="position:absolute; left:.9rem; top:50%; transform:translateY(-50%);
                                             font-size:.82rem; font-weight:700; color:var(--coffee-400);
                                             pointer-events:none;">Rp</span>
                                <input id="price" type="number" name="price" min="0" step="500"
                                       class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}"
                                       style="padding-left:2.5rem;"
                                       value="{{ old('price', (int) $product->price) }}"
                                       required oninput="updatePricePreview()">
                            </div>
                            <div id="price-preview" class="form-hint" style="min-height:1.2em;
                                 color:var(--amber-500); font-weight:700;"></div>
                            @error('price')<div class="form-error">{{ $message }}</div>@enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="stock">
                                Stok <span class="required">*</span>
                            </label>
                            <input id="stock" type="number" name="stock" min="0"
                                   class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}"
                                   value="{{ old('stock', $product->stock) }}" required>
                            {{-- Indikator stok --}}
                            @if($product->stock === 0)
                                <div class="form-hint" style="color:var(--danger); font-weight:700;">
                                    ⚠️ Stok habis — tidak tampil di kasir
                                </div>
                            @elseif($product->stock <= 5)
                                <div class="form-hint" style="color:#b45309; font-weight:700;">
                                    ⚠️ Stok menipis ({{ $product->stock }} tersisa)
                                </div>
                            @else
                                <div class="form-hint">Stok saat ini: {{ $product->stock }}</div>
                            @endif
                            @error('stock')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- ── Deskripsi ──────────────────────────────────────── --}}
                    <div class="form-group">
                        <label class="form-label" for="description">Deskripsi</label>
                        <textarea id="description" name="description" rows="3"
                                  class="form-control" style="resize:vertical;"
                                  placeholder="Deskripsi singkat produk (opsional)...">{{ old('description', $product->description) }}</textarea>
                    </div>

                    {{-- ── Upload Foto ────────────────────────────────────── --}}
                    <div class="form-group">
                        <label class="form-label">Foto Produk</label>

                        <div id="upload-area"
                             style="border:2px dashed var(--coffee-200); border-radius:10px;
                                    padding:1.25rem; text-align:center; cursor:pointer;
                                    background:var(--coffee-50); transition:var(--transition);"
                             onclick="document.getElementById('image').click()"
                             ondragover="event.preventDefault(); this.style.borderColor='var(--amber-400)'"
                             ondragleave="this.style.borderColor='var(--coffee-200)'"
                             ondrop="handleDrop(event)">

                            {{-- Foto yang sudah ada --}}
                            @if($product->image)
                                <div id="current-image" style="margin-bottom:.75rem;">
                                    <img src="{{ Storage::url($product->image) }}"
                                         alt="{{ $product->name }}"
                                         id="img-preview"
                                         style="max-height:180px; max-width:100%; border-radius:8px;
                                                object-fit:cover; margin:0 auto; display:block;">
                                    <div style="font-size:.75rem; color:var(--coffee-400);
                                                margin-top:.5rem; font-weight:600;">
                                        Foto saat ini — klik atau drag untuk mengganti
                                    </div>
                                </div>
                            @else
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
                            @endif
                        </div>

                        <input id="image" type="file" name="image" accept="image/*"
                               style="display:none;"
                               class="{{ $errors->has('image') ? 'is-invalid' : '' }}"
                               onchange="previewImage(this)">
                        <div class="form-hint">Biarkan kosong jika tidak ingin mengganti foto.</div>
                        @error('image')<div class="form-error">{{ $message }}</div>@enderror
                    </div>

                    {{-- ── Status Toggle ──────────────────────────────────── --}}
                    <div class="form-group">
                        <label class="form-label">Status Produk</label>
                        <div class="toggle-wrapper">
                            <label class="toggle">
                                <input type="checkbox" name="is_active" value="1"
                                       id="toggle-active"
                                       {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                       onchange="updateToggleLabel()">
                                <span class="toggle-slider"></span>
                            </label>
                            <span id="toggle-label" style="font-size:.875rem; font-weight:600;
                                  color:var(--coffee-700);">
                                {{ old('is_active', $product->is_active)
                                    ? 'Aktif — tampil di kasir'
                                    : 'Nonaktif — disembunyikan' }}
                            </span>
                        </div>
                    </div>

                    {{-- ── Meta info produk ───────────────────────────────── --}}
                    <div style="display:flex; gap:1.5rem; padding:.85rem 1rem;
                                background:var(--coffee-50); border-radius:10px;
                                border:1px solid var(--coffee-100); margin-top:.5rem; flex-wrap:wrap;">
                        <div>
                            <div style="font-size:.65rem; font-weight:700; color:var(--coffee-400);
                                        text-transform:uppercase; letter-spacing:.08em;">
                                Dibuat
                            </div>
                            <div style="font-size:.8rem; font-weight:600; color:var(--coffee-700); margin-top:2px;">
                                {{ $product->created_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size:.65rem; font-weight:700; color:var(--coffee-400);
                                        text-transform:uppercase; letter-spacing:.08em;">
                                Terakhir Diubah
                            </div>
                            <div style="font-size:.8rem; font-weight:600; color:var(--coffee-700); margin-top:2px;">
                                {{ $product->updated_at->format('d M Y, H:i') }}
                            </div>
                        </div>
                        <div>
                            <div style="font-size:.65rem; font-weight:700; color:var(--coffee-400);
                                        text-transform:uppercase; letter-spacing:.08em;">
                                Slug
                            </div>
                            <div class="font-mono" style="font-size:.8rem; color:var(--coffee-500); margin-top:2px;">
                                {{ $product->slug }}
                            </div>
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
                            Update Produk
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
function updatePricePreview() {
    const val = parseInt(document.getElementById('price').value) || 0;
    const el  = document.getElementById('price-preview');
    el.textContent = val > 0 ? '= Rp ' + val.toLocaleString('id-ID') : '';
}

function previewImage(input) {
    if (!input.files || !input.files[0]) return;
    showPreview(URL.createObjectURL(input.files[0]));
}

function handleDrop(event) {
    event.preventDefault();
    document.getElementById('upload-area').style.borderColor = 'var(--coffee-200)';
    const file = event.dataTransfer.files[0];
    if (!file || !file.type.startsWith('image/')) return;
    const dt = new DataTransfer();
    dt.items.add(file);
    document.getElementById('image').files = dt.files;
    showPreview(URL.createObjectURL(file));
}

function showPreview(url) {
    const placeholder = document.getElementById('upload-placeholder');
    const currentImg  = document.getElementById('current-image');
    if (placeholder) placeholder.style.display = 'none';
    if (currentImg)  currentImg.style.display  = 'none';
    const img = document.getElementById('img-preview');
    img.src   = url;
    img.style.display = 'block';
}

function updateToggleLabel() {
    const checked = document.getElementById('toggle-active').checked;
    document.getElementById('toggle-label').textContent = checked
        ? 'Aktif — tampil di kasir'
        : 'Nonaktif — disembunyikan';
}

updatePricePreview();
</script>