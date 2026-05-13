<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan semua seeder secara berurutan.
     * Urutan penting: User → Category → Product (karena ada foreign key).
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,     // 1. Buat user admin & kasir dulu
            CategorySeeder::class, // 2. Buat kategori menu
            ProductSeeder::class,  // 3. Buat produk (butuh category_id)
        ]);
    }
}
