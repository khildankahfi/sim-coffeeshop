<x-app-layout>
    <x-slot name="title">Tambah Produk</x-slot>
    <x-slot name="subtitle">Tambahkan menu baru ke daftar produk</x-slot>

    <div class="card" style="max-width:700px">
        <div class="card-header">
            <h3>☕ Form Produk Baru</h3>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Produk <span class="required">*</span></label>
                        <input id="name" type="text" name="name"
                               class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name') }}" placeholder="Contoh: Cappuccino" autofocus required>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category_id">Kategori <span class="required">*</span></label>
                        <select id="category_id" name="category_id"
                                class="form-control {{ $errors->has('category_id') ? 'is-invalid' : '' }}" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="price">Harga (Rp) <span class="required">*</span></label>
                        <input id="price" type="number" name="price" min="0" step="500"
                               class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}"
                               value="{{ old('price') }}" placeholder="25000" required>
                        @error('price') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="stock">Stok Awal <span class="required">*</span></label>
                        <input id="stock" type="number" name="stock" min="0"
                               class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}"
                               value="{{ old('stock', 0) }}" required>
                        @error('stock') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi</label>
                    <textarea id="description" name="description" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="image">Foto Produk</label>
                    <input id="image" type="file" name="image" accept="image/*"
                           class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}"
                           onchange="previewImage(this)">
                    <div class="form-hint">Format: JPG, PNG, WebP. Maks 2MB.</div>
                    @error('image') <div class="form-error">{{ $message }}</div> @enderror
                    <img id="img-preview" src="#" alt="Preview" class="image-preview mt-2" style="display:none">
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="toggle-wrapper">
                        <label class="toggle">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="form-hint" style="margin:0">Produk aktif / tampil di kasir</span>
                    </div>
                </div>

                <div class="d-flex gap-3" style="margin-top:1.5rem">
                    <button type="submit" class="btn btn-primary">💾 Simpan Produk</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('img-preview');
    if (input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
        preview.style.display = 'block';
    }
}
document.getElementById('name').addEventListener('input', function() {
    const slugField = document.querySelector('[name="slug"]');
    if (slugField) {
        slugField.value = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-');
    }
});
</script>
@endpush
