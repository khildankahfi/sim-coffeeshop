<x-app-layout>
    <x-slot name="title">Edit Produk</x-slot>
    <x-slot name="subtitle">Ubah data produk: {{ $product->name }}</x-slot>

    <div class="card" style="max-width:700px">
        <div class="card-header">
            <h3>✏️ Edit Produk</h3>
            <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
                @csrf @method('PATCH')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Produk <span class="required">*</span></label>
                        <input id="name" type="text" name="name"
                               class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                               value="{{ old('name', $product->name) }}" required>
                        @error('name') <div class="form-error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category_id">Kategori <span class="required">*</span></label>
                        <select id="category_id" name="category_id" class="form-control" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="price">Harga (Rp) <span class="required">*</span></label>
                        <input id="price" type="number" name="price" min="0" step="500"
                               class="form-control"
                               value="{{ old('price', $product->price) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="stock">Stok <span class="required">*</span></label>
                        <input id="stock" type="number" name="stock" min="0"
                               class="form-control"
                               value="{{ old('stock', $product->stock) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi</label>
                    <textarea id="description" name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Foto Produk</label>
                    @if($product->image)
                        <div style="margin-bottom:.75rem">
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                 class="image-preview" style="max-height:150px;width:auto">
                            <div class="form-hint">Foto saat ini. Upload baru untuk mengganti.</div>
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*"
                           class="form-control {{ $errors->has('image') ? 'is-invalid' : '' }}"
                           onchange="previewImage(this)">
                    <img id="img-preview" src="#" class="image-preview mt-2" style="display:none">
                    @error('image') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <div class="toggle-wrapper">
                        <label class="toggle">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <span class="form-hint" style="margin:0">Produk aktif / tampil di kasir</span>
                    </div>
                </div>

                <div class="d-flex gap-3" style="margin-top:1.5rem">
                    <button type="submit" class="btn btn-primary">💾 Update Produk</button>
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
</script>
@endpush
