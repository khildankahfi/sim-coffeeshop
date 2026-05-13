<x-app-layout>
    <x-slot name="title">Kategori Menu</x-slot>
    <x-slot name="subtitle">Kelola kategori menu coffeeshop</x-slot>
    <x-slot name="actions">
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            ➕ Tambah Kategori
        </a>
    </x-slot>

    <div class="card">
        <div class="table-wrapper">
            @if($categories->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">🏷️</div>
                    <h3>Belum ada kategori</h3>
                    <p>Tambahkan kategori pertama untuk mengelompokkan menu.</p>
                    <a href="{{ route('categories.create') }}" class="btn btn-primary mt-3">Tambah Kategori</a>
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Nama Kategori</th>
                            <th>Slug</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Produk</th>
                            <th style="width:140px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $i => $category)
                        <tr>
                            <td class="text-muted">{{ $categories->firstItem() + $i }}</td>
                            <td class="fw-semibold">{{ $category->name }}</td>
                            <td><code style="font-size:.78rem;color:var(--coffee-500);background:var(--coffee-50);padding:.2rem .4rem;border-radius:.3rem">{{ $category->slug }}</code></td>
                            <td class="text-muted" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                {{ $category->description ?? '—' }}
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $category->products_count }} produk</span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-secondary btn-sm">✏️</a>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                          onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="pagination-wrapper">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
