<x-app-layout>
    <x-slot name="title">Produk / Menu</x-slot>
    <x-slot name="subtitle">Kelola semua menu coffeeshop</x-slot>
    <x-slot name="actions">
        <a href="{{ route('products.create') }}" class="btn btn-primary">➕ Tambah Produk</a>
    </x-slot>

    {{-- Filter Bar --}}
    <form method="GET" class="card" style="margin-bottom:1rem">
        <div class="card-body" style="padding:.75rem 1rem">
            <div class="d-flex gap-3 align-center" style="flex-wrap:wrap">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" style="width:220px"
                       placeholder="🔍 Cari nama produk...">
                <select name="category_id" class="form-control" style="width:180px">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
                @if(request('search') || request('category_id'))
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-wrapper">
            @if($products->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon">☕</div>
                    <h3>Belum ada produk</h3>
                    <p>Tambahkan produk/menu untuk mulai berjualan.</p>
                    <a href="{{ route('products.create') }}" class="btn btn-primary mt-3">Tambah Produk</a>
                </div>
            @else
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width:50px">#</th>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th style="width:120px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $i => $product)
                        <tr>
                            <td class="text-muted">{{ $products->firstItem() + $i }}</td>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                @if($product->description)
                                    <div class="text-muted" style="font-size:.75rem;margin-top:.15rem">
                                        {{ Str::limit($product->description, 50) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $product->category->name }}</span>
                            </td>
                            <td class="fw-semibold" style="color:var(--amber-500)">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </td>
                            <td>
                                @if($product->stock == 0)
                                    <span class="badge badge-danger">Habis</span>
                                @elseif($product->stock < 5)
                                    <span class="badge badge-warning">{{ $product->stock }} sisa</span>
                                @else
                                    <span class="badge badge-success">{{ $product->stock }}</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_active)
                                    <span class="badge badge-success">● Aktif</span>
                                @else
                                    <span class="badge badge-danger">● Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-secondary btn-sm">✏️</a>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}"
                                          onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination-wrapper">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
