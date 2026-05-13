<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Tampilkan daftar semua produk dengan filter dan pencarian.
     */
    public function index(): View
    {
        $query = Product::with('category');

        // Filter berdasarkan kategori (dari query string ?category_id=X)
        if (request('category_id')) {
            $query->where('category_id', request('category_id'));
        }

        // Pencarian berdasarkan nama produk
        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        $products   = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Tampilkan form tambah produk baru.
     */
    public function create(): View
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Simpan produk baru ke database.
     * Termasuk upload foto jika ada.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Auto-generate slug dari nama jika tidak diisi
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        // Proses upload foto jika ada
        if ($request->hasFile('image')) {
            // Simpan ke storage/app/public/products/
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk "' . $data['name'] . '" berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit produk.
     */
    public function edit(Product $product): View
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update data produk di database.
     */
    public function update(StoreProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        // Jika ada foto baru, hapus foto lama dan upload yang baru
        if ($request->hasFile('image')) {
            // Hapus foto lama dari storage
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk "' . $product->name . '" berhasil diperbarui!');
    }

    /**
     * Hapus produk dari database beserta fotonya.
     */
    public function destroy(Product $product): RedirectResponse
    {
        // Hapus foto dari storage jika ada
        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
        }

        $name = $product->name;
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produk "' . $name . '" berhasil dihapus!');
    }
}
