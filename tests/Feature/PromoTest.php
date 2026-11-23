<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Support\BuildsShop;
use Tests\TestCase;

class PromoTest extends TestCase
{
    use RefreshDatabase, BuildsShop;

    protected function setUp(): void
    {
        parent::setUp();

        config(['midtrans.server_key' => '', 'midtrans.client_key' => '']);

        Setting::put('promo_code', 'HEMAT20');
        Setting::put('promo_percent', '20');
        Setting::put('promo_active', '1');
    }

    private function checkout(User $user, int $productId, int $qty = 1, ?string $code = null)
    {
        return $this->actingAs($user)
            ->withSession(['buy_now' => ['product_id' => $productId, 'qty' => $qty]])
            ->post(route('checkout.store'), array_filter([
                'full_name' => 'Budi', 'email' => 'budi@example.com', 'phone' => '081234567890', 'address' => 'Jl. Malioboro 1', // ← phone baru
                'city' => 'Yogyakarta', 'state' => 'DIY', 'zip' => '55271', 'promo_code' => $code,
            ], fn ($v) => $v !== null));
    }

    public function test_valid_code_discounts_total_case_insensitive(): void
    {
        $product = $this->makeProduct(['price' => 1000000]);

        $this->checkout(User::factory()->create(), $product->id, 2, ' hemat20 ')->assertSessionHasNoErrors();

        $order = Order::first();
        $this->assertSame(1600000, (int) $order->total);
        $this->assertSame(400000, (int) $order->discount_amount);
        $this->assertSame('HEMAT20', $order->promo_code);
    }

    public function test_order_without_code_is_unchanged(): void
    {
        $product = $this->makeProduct(['price' => 1000000]);

        $this->checkout(User::factory()->create(), $product->id, 1);

        $order = Order::first();
        $this->assertSame(1000000, (int) $order->total);
        $this->assertSame(0, (int) $order->discount_amount);
        $this->assertNull($order->promo_code);
    }

    public function test_wrong_inactive_or_zero_percent_code_is_rejected_and_nothing_is_created(): void
    {
        $product = $this->makeProduct(['stock' => 5]);
        $user = User::factory()->create();

        $this->checkout($user, $product->id, 1, 'NGASAL')->assertSessionHasErrors('promo_code');

        Setting::put('promo_active', '0');
        $this->checkout($user, $product->id, 1, 'HEMAT20')->assertSessionHasErrors('promo_code');

        Setting::put('promo_active', '1');
        Setting::put('promo_percent', '0');
        $this->checkout($user, $product->id, 1, 'HEMAT20')->assertSessionHasErrors('promo_code');

        $this->assertSame(0, Order::count());
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_code_is_single_use_per_user_but_freed_when_order_is_cancelled(): void
    {
        $product = $this->makeProduct(['stock' => 10]);
        $user = User::factory()->create();

        $this->checkout($user, $product->id, 1, 'HEMAT20')->assertSessionHasNoErrors();
        $this->checkout($user, $product->id, 1, 'HEMAT20')->assertSessionHasErrors('promo_code');
        $this->assertSame(1, Order::count());

        $this->checkout(User::factory()->create(), $product->id, 1, 'HEMAT20')->assertSessionHasNoErrors();
        $this->assertSame(2, Order::count());

        $first = Order::where('user_id', $user->id)->first();
        $first->markCancelled();
        $first->save();

        $this->checkout($user, $product->id, 1, 'HEMAT20')->assertSessionHasNoErrors();
        $this->assertSame(3, Order::count());
    }

    public function test_percent_is_capped_so_total_is_never_zero(): void
    {
        Setting::put('promo_percent', '100');
        $product = $this->makeProduct(['price' => 100000]);

        $this->checkout(User::factory()->create(), $product->id, 1, 'HEMAT20');

        $this->assertGreaterThan(0, (int) Order::first()->total);
    }

    public function test_midtrans_items_sum_equals_gross_amount_with_discount_line(): void
    {
        config(['midtrans.server_key' => 'SB-Mid-server-test', 'midtrans.client_key' => 'SB-Mid-client-test', 'midtrans.is_production' => false]);
        Http::fake(['app.sandbox.midtrans.com/*' => Http::response(['token' => 'tok-promo'], 201)]);

        $user = User::factory()->create();
        $order = $this->makeOrder($user, $this->makeProduct(['price' => 500000]), 'pending', 2);
        $order->update(['total' => 800000, 'promo_code' => 'HEMAT20', 'discount_amount' => 200000]);

        app(MidtransService::class)->createSnapToken($order->fresh());

        Http::assertSent(function ($r) {
            $sum = collect($r['item_details'])->sum(fn ($i) => $i['price'] * $i['quantity']);

            return $r['transaction_details']['gross_amount'] === 800000 && $sum === 800000;
        });
    }
}