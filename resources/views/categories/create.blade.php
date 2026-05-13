<x-app-layout>
    <x-slot name="title">Tambah Kategori</x-slot>
    <x-slot name="subtitle">Buat kategori menu baru</x-slot>

    <div class="card" style="max-width:600px">
        <div class="card-header">
            <h3>📁 Form Kategori Baru</h3>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">
                        Nama Kategori <span class="required">*</span>
                    </label>
                    <input id="name" type="text" name="name"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name') }}"
                           placeholder="Contoh: Kopi, Non-Kopi, Makanan..."
                           autofocus required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="slug">Slug (URL)</label>
                    <input id="slug" type="text" name="slug"
                           class="form-control {{ $errors->has('slug') ? 'is-invalid' : '' }}"
                           value="{{ old('slug') }}"
                           placeholder="Dikosongkan = otomatis dari nama">
                    <div class="form-hint">Slug adalah URL-friendly version dari nama. Kosongkan untuk generate otomatis.</div>
                    @error('slug')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi</label>
                    <textarea id="description" name="description"
                              class="form-control"
                              placeholder="Deskripsi singkat kategori ini...">{{ old('description') }}</textarea>
                </div>

                <div class="d-flex gap-3" style="margin-top:1.5rem">
                    <button type="submit" class="btn btn-primary">💾 Simpan Kategori</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    // Auto-generate slug dari nama
    document.getElementById('name').addEventListener('input', function() {
        const slugField = document.getElementById('slug');
        if (!slugField.dataset.manual) {
            slugField.value = this.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }
    });
    document.getElementById('slug').addEventListener('input', function() {
        this.dataset.manual = this.value ? '1' : '';
    });
</script>
@endpush
