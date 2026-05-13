<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel orders sebagai header transaksi penjualan.
     * Setiap order bisa memiliki banyak item (order_items).
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')             // Kasir yang melayani
                  ->constrained()
                  ->onDelete('restrict');
            $table->string('invoice_number', 50)->unique(); // Nomor invoice: INV-20250512-001
            $table->decimal('total_amount', 10, 2);  // Total harga semua item
            $table->decimal('amount_paid', 10, 2);   // Uang yang dibayarkan pelanggan
            $table->decimal('change_amount', 10, 2); // Kembalian (amount_paid - total_amount)
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('paid');
            $table->text('notes')->nullable();        // Catatan tambahan (opsional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
