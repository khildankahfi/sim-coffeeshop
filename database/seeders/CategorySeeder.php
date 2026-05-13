<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Buat kategori default untuk menu coffeeshop.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Kopi',
                'slug'        => 'kopi',
                'description' => 'Minuman berbasis kopi seperti espresso, americano, latte, dan cappuccino.',
            ],
            [
                'name'        => 'Non-Kopi',
                'slug'        => 'non-kopi',
                'description' => 'Minuman tanpa kopi seperti teh, cokelat, matcha, dan jus.',
            ],
            [
                'name'        => 'Makanan',
                'slug'        => 'makanan',
                'description' => 'Menu makanan berat seperti nasi, pasta, dan sandwich.',
            ],
            [
                'name'        => 'Snack',
                'slug'        => 'snack',
                'description' => 'Camilan ringan seperti roti, kue, dan croissant.',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('✓ CategorySeeder: 4 kategori berhasil dibuat.');
    }
}
