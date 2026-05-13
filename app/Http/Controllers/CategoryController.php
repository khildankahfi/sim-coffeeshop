<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Tampilkan daftar semua kategori.
     * Hanya admin yang bisa mengakses (dijaga di route).
     */
    public function index(): View
    {
        // withCount('products') → menambahkan kolom products_count secara otomatis
        $categories = Category::withCount('products')
                               ->latest()
                               ->paginate(10);

        return view('categories.index', compact('categories'));
    }

    /**
     * Tampilkan form untuk membuat kategori baru.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Simpan kategori baru ke database.
     * Validasi sudah ditangani oleh StoreCategoryRequest.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Jika slug tidak diisi, generate otomatis dari nama
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        Category::create($data);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori "' . $data['name'] . '" berhasil ditambahkan!');
    }

    /**
     * Tampilkan form edit kategori.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update data kategori di database.
     */
    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();

        // Jika slug tidak diisi, generate dari nama
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $category->update($data);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori "' . $category->name . '" berhasil diperbarui!');
    }

    /**
     * Hapus kategori dari database.
     * Akan gagal jika masih ada produk yang menggunakan kategori ini
     * (karena constraint onDelete('restrict') di migration).
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Cek apakah masih ada produk dalam kategori ini
        if ($category->products()->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk!');
        }

        $name = $category->name;
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori "' . $name . '" berhasil dihapus!');
    }
}
