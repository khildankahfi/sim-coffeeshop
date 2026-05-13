<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /**
     * Kolom yang boleh diisi secara mass assignment.
     *
     * @var array<string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',  // Snapshot nama produk saat transaksi
        'price',         // Snapshot harga saat transaksi
        'quantity',
        'subtotal',      // price × quantity
    ];

    /**
     * Cast tipe data.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price'    => 'decimal:2',
            'subtotal' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    // ─────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────

    /**
     * Setiap item dimiliki oleh satu order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Setiap item merujuk ke satu produk.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
