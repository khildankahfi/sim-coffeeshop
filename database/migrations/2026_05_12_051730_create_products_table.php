<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel products untuk menu coffeeshop.
     * Setiap produk terhubung ke kategori dan memiliki stok yang bisa berkurang otomatis.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')        // Relasi ke tabel categories
                  ->constrained()
                  ->onDelete('restrict');            // Tidak bisa hapus kategori yang ada produknya
            $table->string('name', 150);            // Nama produk/menu
            $table->string('slug', 150)->unique();  // URL-friendly name
            $table->text('description')->nullable(); // Deskripsi produk
            $table->decimal('price', 10, 2);        // Harga jual (maks 99,999,999.99)
            $table->string('image')->nullable();    // Path foto produk di storage
            $table->integer('stock')->default(0);   // Stok saat ini
            $table->boolean('is_active')->default(true); // Aktif/tidak tampil di kasir
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
