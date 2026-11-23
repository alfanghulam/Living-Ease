<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedAdmin();

        // Pembeli demo hanya untuk lingkungan lokal; tidak pernah dibuat di server.
        if (app()->environment('local')) {
            $demo = User::firstOrNew(['email' => 'user@gmail.com']);

            if (! $demo->exists) {
                $demo->forceFill([
                    'name' => 'Pembeli Demo',
                    'password' => Hash::make('user12345'),
                    'is_admin' => false,
                ])->save();
            }
        }

        // Produk perabot contoh (foto ada di public/img/products). Ganti lewat panel admin.
        $this->call(FurnitureSeeder::class);
    }

    /**
     * Akun admin dibuat dari ADMIN_EMAIL dan ADMIN_PASSWORD di .env.
     * Tidak ada kata sandi bawaan: kalau keduanya kosong, tidak ada admin yang dibuat.
     */
    protected function seedAdmin(): void
    {
        $email = config('shop.admin_email');
        $password = config('shop.admin_password');

        if (blank($email) || blank($password)) {
            $this->command?->warn('ADMIN_EMAIL / ADMIN_PASSWORD belum diisi di .env, jadi akun admin tidak dibuat.');
            $this->command?->warn('Buat akun admin dengan: php artisan shop:admin emailkamu@contoh.com');

            return;
        }

        if (mb_strlen((string) $password) < 10) {
            $this->command?->warn('ADMIN_PASSWORD minimal 10 karakter, jadi akun admin tidak dibuat.');

            return;
        }

        $admin = User::firstOrNew(['email' => strtolower((string) $email)]);

        $admin->forceFill([
            'name' => $admin->name ?: 'Admin',
            'password' => Hash::make((string) $password),
            'is_admin' => true,
        ])->save();
    }
}