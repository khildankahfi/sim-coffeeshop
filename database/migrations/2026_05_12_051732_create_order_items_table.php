<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel order_items sebagai detail item dalam setiap transaksi.
     * Menyimpan snapshot nama & harga saat transaksi agar laporan tetap akurat
     * meskipun harga produk berubah di kemudian hari.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')            // Relasi ke header transaksi
                  ->constrained()
                  ->onDelete('cascade');             // Hapus item jika ordernya dihapus
            $table->foreignId('product_id')          // Relasi ke produk
                  ->constrained()
                  ->onDelete('restrict');
            $table->string('product_name', 150);     // Snapshot nama produk saat transaksi
            $table->decimal('price', 10, 2);         // Snapshot harga saat transaksi
            $table->integer('quantity');             // Jumlah item yang dibeli
            $table->decimal('subtotal', 10, 2);      // price × quantity
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
