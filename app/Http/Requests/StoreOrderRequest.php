<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Admin dan kasir boleh membuat transaksi.
     */
    public function authorize(): bool
    {
        return in_array($this->user()->role, ['admin', 'kasir']);
    }

    /**
     * Aturan validasi untuk form transaksi POS.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Array items: [{product_id: 1, quantity: 2}, ...]
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.product_id'     => ['required', 'exists:products,id'],
            'items.*.quantity'       => ['required', 'integer', 'min:1'],
            'amount_paid'            => ['required', 'numeric', 'min:0'],
            'notes'                  => ['nullable', 'string', 'max:500'],
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
            'items.required'              => 'Pilih minimal satu produk.',
            'items.min'                   => 'Keranjang belanja tidak boleh kosong.',
            'items.*.product_id.required' => 'Produk tidak valid.',
            'items.*.product_id.exists'   => 'Produk tidak ditemukan.',
            'items.*.quantity.min'        => 'Jumlah item minimal 1.',
            'amount_paid.required'        => 'Jumlah bayar wajib diisi.',
            'amount_paid.min'             => 'Jumlah bayar tidak boleh negatif.',
        ];
    }
}
