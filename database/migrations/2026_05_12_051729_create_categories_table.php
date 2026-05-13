<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel categories untuk mengelompokkan menu coffeeshop.
     * Contoh: Kopi, Non-Kopi, Makanan, Snack.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);           // Nama kategori
            $table->string('slug', 100)->unique();  // URL-friendly name
            $table->text('description')->nullable(); // Deskripsi (opsional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
