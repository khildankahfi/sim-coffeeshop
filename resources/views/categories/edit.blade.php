<x-app-layout>
    <x-slot name="title">Edit Kategori</x-slot>
    <x-slot name="subtitle">Ubah data kategori: {{ $category->name }}</x-slot>

    <div class="card" style="max-width:600px">
        <div class="card-header">
            <h3>✏️ Edit Kategori</h3>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary btn-sm">← Kembali</a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('categories.update', $category) }}">
                @csrf @method('PATCH')

                <div class="form-group">
                    <label class="form-label" for="name">
                        Nama Kategori <span class="required">*</span>
                    </label>
                    <input id="name" type="text" name="name"
                           class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name', $category->name) }}" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="slug">Slug (URL)</label>
                    <input id="slug" type="text" name="slug"
                           class="form-control {{ $errors->has('slug') ? 'is-invalid' : '' }}"
                           value="{{ old('slug', $category->slug) }}">
                    @error('slug')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Deskripsi</label>
                    <textarea id="description" name="description" class="form-control">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="d-flex gap-3" style="margin-top:1.5rem">
                    <button type="submit" class="btn btn-primary">💾 Update Kategori</button>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
