<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Tentukan apakah user berhak membuat request ini.
     * Hanya admin yang boleh mengelola kategori.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Aturan validasi untuk form kategori.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Ambil ID kategori (untuk update, slug harus unik kecuali milik sendiri)
        $categoryId = $this->route('category')?->id;

        return [
            'name'        => ['required', 'string', 'max:100'],
            'slug'        => ['nullable', 'string', 'max:100', 'unique:categories,slug,' . $categoryId],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Pesan error kustom dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.max'      => 'Nama kategori maksimal 100 karakter.',
            'slug.unique'   => 'Slug sudah digunakan, pilih slug lain.',
        ];
    }
}
