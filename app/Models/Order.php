<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * Kolom yang boleh diisi secara mass assignment.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'invoice_number',
        'total_amount',
        'amount_paid',
        'change_amount',
        'status',
        'notes',
    ];

    /**
     * Cast tipe data.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_amount'  => 'decimal:2',
            'amount_paid'   => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    // ─────────────────────────────────────────────
    // Relationships
    // ─────────────────────────────────────────────

    /**
     * Setiap order dibuat oleh satu kasir (user).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Satu order memiliki banyak item.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // ─────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────

    /**
     * Format total_amount menjadi Rupiah.
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    // ─────────────────────────────────────────────
    // Static Methods
    // ─────────────────────────────────────────────

    /**
     * Generate nomor invoice unik.
     * Format: INV-YYYYMMDD-XXX (contoh: INV-20250512-001)
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . date('Ymd') . '-';
        // Cari nomor terakhir hari ini lalu tambah 1
        $lastOrder = self::where('invoice_number', 'like', $prefix . '%')
                         ->orderByDesc('id')
                         ->first();

        $nextNumber = $lastOrder
            ? (int) substr($lastOrder->invoice_number, -3) + 1
            : 1;

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
}
