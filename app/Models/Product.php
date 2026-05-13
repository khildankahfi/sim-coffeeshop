<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    /**
     * Kolom yang boleh diisi secara mass assignment.
     *
     * @var array<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'stock',
        'is_active',
    ];

    /**
     * Cast tipe data agar lebih mudah digunakan di view.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price'     => 'decimal:2',
            'is_active' => 'boolean',
            'stock'     => 'integer',
        ];
    }

    // ─────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────

    /**
     * Setiap produk dimiliki oleh satu kategori.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Satu produk bisa muncul di banyak order_items.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─────────────────────────────────────────────
    // Accessors & Mutators
    // ─────────────────────────────────────────────

    /**
     * Otomatis buat slug dari nama saat menyimpan.
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    /**
     * Format harga menjadi format Rupiah.
     * Penggunaan: $product->formatted_price → "Rp 25.000"
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * URL gambar produk. Kembalikan placeholder jika tidak ada foto.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && \Storage::disk('public')->exists($this->image)) {
            return \Storage::url($this->image);
        }
        return asset('images/placeholder.png');
    }

    // ─────────────────────────────────────────────
    // Scopes (Query Filters)
    // ─────────────────────────────────────────────

    /**
     * Filter hanya produk yang aktif.
     * Penggunaan: Product::active()->get()
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Filter produk yang masih ada stoknya.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}
