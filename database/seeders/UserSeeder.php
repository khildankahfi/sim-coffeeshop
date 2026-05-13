<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Buat akun default: 1 Admin dan 1 Kasir untuk testing.
     */
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@coffeeshop.com'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@coffeeshop.com',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // ── Kasir ─────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'kasir@coffeeshop.com'],
            [
                'name'     => 'Budi Kasir',
                'email'    => 'kasir@coffeeshop.com',
                'password' => Hash::make('password'),
                'role'     => 'kasir',
            ]
        );

        $this->command->info('✓ UserSeeder: 2 user berhasil dibuat (admin & kasir)');
    }
}
