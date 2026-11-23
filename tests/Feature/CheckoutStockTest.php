<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\BuildsShop;
use Tests\TestCase;

class CheckoutStockTest extends TestCase
{
    use RefreshDatabase, BuildsShop;

    protected function setUp(): void
    {
        parent::setUp();

        // Tes stok tidak peduli metode bayar: jangan bergantung pada key Midtrans di .env.
        config(['midtrans.server_key' => '', 'midtrans.client_key' => '']);
    }

    private function address(): array
    {
        return [
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890', // ← baru
            'address' => 'Jl. Malioboro 1',
            'city' => 'Yogyakarta',
            'state' => 'DIY',
            'zip' => '55271',
        ];
    }

    private function buyNow(User $user, int $productId, int $qty)
    {
        return $this->actingAs($user)
            ->withSession(['buy_now' => ['product_id' => $productId, 'qty' => $qty]])
            ->post(route('checkout.store'), $this->address());
    }

    public function test_checkout_reserves_stock_and_cancel_restores_it(): void
    {
        $product = $this->makeProduct(['stock' => 5]);

        $this->buyNow(User::factory()->create(), $product->id, 2)->assertRedirect();

        $this->assertSame(3, $product->fresh()->stock);

        $order = Order::firstOrFail();
        $this->assertTrue($order->stock_applied);
        $this->assertSame(1000000, (int) $order->total);

        $order->markCancelled();
        $order->save();

        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_paying_a_reserved_order_does_not_reduce_stock_twice(): void
    {
        $product = $this->makeProduct(['stock' => 5]);

        $this->buyNow(User::factory()->create(), $product->id, 2);

        $order = Order::firstOrFail();
        $order->markPaid();
        $order->save();

        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_second_buyer_is_rejected_when_stock_is_gone(): void
    {
        $product = $this->makeProduct(['stock' => 1]);

        $this->buyNow(User::factory()->create(), $product->id, 1)->assertRedirect();
        $this->buyNow(User::factory()->create(), $product->id, 1)->assertSessionHasErrors('stock');

        $this->assertSame(1, Order::count());
        $this->assertSame(0, $product->fresh()->stock);
    }

    public function test_expire_command_cancels_old_pending_orders_and_restores_stock(): void
    {
        config(['shop.order_expire_minutes' => 60]);

        $product = $this->makeProduct(['stock' => 5]);
        $user = User::factory()->create();

        $this->buyNow($user, $product->id, 2);
        $this->buyNow($user, $product->id, 1);

        $old = Order::oldest('id')->first();
        $old->forceFill(['created_at' => now()->subMinutes(120)])->save();

        $this->artisan('orders:expire')->assertSuccessful();

        $this->assertSame('cancelled', $old->fresh()->status);
        $this->assertSame('pending', Order::latest('id')->first()->status);
        // 5 - 2 - 1 = 2, lalu pesanan lama (2) dikembalikan -> 4
        $this->assertSame(4, $product->fresh()->stock);
    }
}