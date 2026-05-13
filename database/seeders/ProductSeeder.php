<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Buat produk sample untuk setiap kategori.
     */
    public function run(): void
    {
        $categoryKopi    = Category::where('slug', 'kopi')->first();
        $categoryNonKopi = Category::where('slug', 'non-kopi')->first();
        $categoryMakanan = Category::where('slug', 'makanan')->first();
        $categorySnack   = Category::where('slug', 'snack')->first();

        $products = [
            // ── Kopi ──────────────────────────────────────
            ['category_id' => $categoryKopi->id,    'name' => 'Espresso',         'slug' => 'espresso',         'price' => 18000,  'stock' => 50, 'description' => 'Kopi espresso murni, shot tunggal.'],
            ['category_id' => $categoryKopi->id,    'name' => 'Americano',        'slug' => 'americano',        'price' => 20000,  'stock' => 50, 'description' => 'Espresso yang diencerkan dengan air panas.'],
            ['category_id' => $categoryKopi->id,    'name' => 'Cappuccino',       'slug' => 'cappuccino',       'price' => 25000,  'stock' => 40, 'description' => 'Espresso dengan susu steam dan foam susu.'],
            ['category_id' => $categoryKopi->id,    'name' => 'Caffe Latte',      'slug' => 'caffe-latte',      'price' => 27000,  'stock' => 40, 'description' => 'Espresso dengan banyak susu steam dan sedikit foam.'],
            ['category_id' => $categoryKopi->id,    'name' => 'V60 Manual Brew',  'slug' => 'v60-manual-brew',  'price' => 32000,  'stock' => 30, 'description' => 'Kopi filter manual dengan metode pour over V60.'],
            ['category_id' => $categoryKopi->id,    'name' => 'Cold Brew',        'slug' => 'cold-brew',        'price' => 30000,  'stock' => 20, 'description' => 'Kopi diseduh dingin selama 12 jam, rasa halus.'],

            // ── Non-Kopi ──────────────────────────────────
            ['category_id' => $categoryNonKopi->id, 'name' => 'Matcha Latte',     'slug' => 'matcha-latte',     'price' => 28000,  'stock' => 35, 'description' => 'Teh matcha Jepang dengan susu steam.'],
            ['category_id' => $categoryNonKopi->id, 'name' => 'Cokelat Panas',    'slug' => 'cokelat-panas',    'price' => 22000,  'stock' => 35, 'description' => 'Minuman cokelat hangat yang creamy.'],
            ['category_id' => $categoryNonKopi->id, 'name' => 'Teh Tarik',        'slug' => 'teh-tarik',        'price' => 15000,  'stock' => 50, 'description' => 'Teh susu khas Asia Tenggara.'],
            ['category_id' => $categoryNonKopi->id, 'name' => 'Lemon Tea',        'slug' => 'lemon-tea',        'price' => 18000,  'stock' => 40, 'description' => 'Teh dingin dengan perasan lemon segar.'],

            // ── Makanan ───────────────────────────────────
            ['category_id' => $categoryMakanan->id, 'name' => 'Nasi Goreng',      'slug' => 'nasi-goreng',      'price' => 35000,  'stock' => 20, 'description' => 'Nasi goreng spesial dengan telur dan ayam.'],
            ['category_id' => $categoryMakanan->id, 'name' => 'Club Sandwich',    'slug' => 'club-sandwich',    'price' => 38000,  'stock' => 15, 'description' => 'Sandwich tiga lapis dengan ayam dan sayur segar.'],
            ['category_id' => $categoryMakanan->id, 'name' => 'Pasta Carbonara',  'slug' => 'pasta-carbonara',  'price' => 45000,  'stock' => 15, 'description' => 'Pasta dengan saus carbonara creamy.'],

            // ── Snack ─────────────────────────────────────
            ['category_id' => $categorySnack->id,   'name' => 'Croissant',        'slug' => 'croissant',        'price' => 18000,  'stock' => 25, 'description' => 'Croissant butter renyah khas Prancis.'],
            ['category_id' => $categorySnack->id,   'name' => 'Banana Bread',     'slug' => 'banana-bread',     'price' => 20000,  'stock' => 20, 'description' => 'Roti pisang lembut, cocok untuk teman kopi.'],
            ['category_id' => $categorySnack->id,   'name' => 'Cheesecake',       'slug' => 'cheesecake',       'price' => 28000,  'stock' => 10, 'description' => 'Cheesecake klasik New York style.'],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['slug' => $product['slug']],
                array_merge($product, ['is_active' => true])
            );
        }

        $this->command->info('✓ ProductSeeder: ' . count($products) . ' produk berhasil dibuat.');
    }
}
