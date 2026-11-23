<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeAdmin extends Command
{
    protected $signature = 'shop:admin {email : Email akun admin} {--name=Admin : Nama tampilan (dipakai kalau akunnya baru)}';

    protected $description = 'Buat akun admin, atau jadikan akun yang sudah ada sebagai admin dan ganti kata sandinya';

    private const MIN_PASSWORD = 10;

    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Format email tidak valid.');

            return self::FAILURE;
        }

        // secret() tidak menampilkan ketikan di layar dan tidak masuk riwayat terminal.
        $password = (string) $this->secret('Kata sandi (minimal '.self::MIN_PASSWORD.' karakter)');
        $confirm = (string) $this->secret('Ulangi kata sandi');

        if ($password !== $confirm) {
            $this->error('Kata sandi tidak sama.');

            return self::FAILURE;
        }

        if (mb_strlen($password) < self::MIN_PASSWORD) {
            $this->error('Kata sandi minimal '.self::MIN_PASSWORD.' karakter.');

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $email]);
        $isNew = ! $user->exists;

        $user->forceFill([
            'name' => $user->name ?: $this->option('name'),
            'password' => Hash::make($password),
            'is_admin' => true,
        ])->save();

        $this->info($isNew
            ? "Akun admin {$email} dibuat."
            : "Akun {$email} sekarang admin, dan kata sandinya diperbarui.");

        return self::SUCCESS;
    }
}