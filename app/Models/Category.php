<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    /**
     * Kolom yang boleh diisi secara mass assignment.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    // ─────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────

    /**
     * Satu kategori bisa memiliki banyak produk.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // ─────────────────────────────────────────────
    // Accessors & Mutators
    // ─────────────────────────────────────────────

    /**
     * Otomatis buat slug dari nama saat menyimpan.
     * Contoh: "Kopi Susu" → "kopi-susu"
     */
    public function setNameAttribute(string $value): void
    {
        $this->attributes['name'] = $value;
        // Hanya auto-generate slug jika belum diset
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }
}
