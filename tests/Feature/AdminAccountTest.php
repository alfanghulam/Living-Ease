<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_command_creates_an_admin(): void
    {
        $this->artisan('shop:admin', ['email' => 'Owner@Example.com', '--name' => 'Pemilik'])
            ->expectsQuestion('Kata sandi (minimal 10 karakter)', 'rahasia-panjang-123')
            ->expectsQuestion('Ulangi kata sandi', 'rahasia-panjang-123')
            ->assertSuccessful();

        $user = User::where('email', 'owner@example.com')->firstOrFail();

        $this->assertTrue((bool) $user->is_admin);
        $this->assertSame('Pemilik', $user->name);
        $this->assertTrue(Hash::check('rahasia-panjang-123', $user->password));
    }

    public function test_command_rejects_a_short_password(): void
    {
        $this->artisan('shop:admin', ['email' => 'owner@example.com'])
            ->expectsQuestion('Kata sandi (minimal 10 karakter)', 'pendek')
            ->expectsQuestion('Ulangi kata sandi', 'pendek')
            ->assertFailed();

        $this->assertSame(0, User::count());
    }

    public function test_command_rejects_mismatched_passwords(): void
    {
        $this->artisan('shop:admin', ['email' => 'owner@example.com'])
            ->expectsQuestion('Kata sandi (minimal 10 karakter)', 'rahasia-panjang-123')
            ->expectsQuestion('Ulangi kata sandi', 'rahasia-panjang-124')
            ->assertFailed();

        $this->assertSame(0, User::count());
    }

    public function test_command_promotes_an_existing_customer_and_changes_the_password(): void
    {
        $customer = User::factory()->create(['email' => 'budi@example.com', 'name' => 'Budi']);

        $this->artisan('shop:admin', ['email' => 'budi@example.com'])
            ->expectsQuestion('Kata sandi (minimal 10 karakter)', 'rahasia-panjang-123')
            ->expectsQuestion('Ulangi kata sandi', 'rahasia-panjang-123')
            ->assertSuccessful();

        $customer->refresh();

        $this->assertTrue((bool) $customer->is_admin);
        $this->assertSame('Budi', $customer->name);
        $this->assertTrue(Hash::check('rahasia-panjang-123', $customer->password));
        $this->assertSame(1, User::count());
    }

    public function test_seeder_creates_no_admin_without_env_credentials(): void
    {
        config(['shop.admin_email' => null, 'shop.admin_password' => null]);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame(0, User::where('is_admin', true)->count());
        $this->assertNull(User::where('email', 'admin@gmail.com')->first());
    }

    public function test_seeder_creates_the_admin_from_env_credentials_once(): void
    {
        config(['shop.admin_email' => 'Owner@Example.com', 'shop.admin_password' => 'rahasia-panjang-123']);

        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $admins = User::where('is_admin', true)->get();

        $this->assertCount(1, $admins);
        $this->assertSame('owner@example.com', $admins->first()->email);
        $this->assertTrue(Hash::check('rahasia-panjang-123', $admins->first()->password));
    }

    public function test_admin_login_page_does_not_reveal_demo_credentials(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertDontSee('admin123')
            ->assertDontSee('Akun demo');
    }
}