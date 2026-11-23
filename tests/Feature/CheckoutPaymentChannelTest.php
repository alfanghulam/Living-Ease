<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Support\BuildsShop;
use Tests\TestCase;

class CheckoutPaymentChannelTest extends TestCase
{
    use RefreshDatabase, BuildsShop;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        config([
            'midtrans.server_key' => 'SB-Mid-server-test',
            'midtrans.client_key' => 'SB-Mid-client-test',
            'midtrans.is_production' => false,
            'midtrans.enabled_payments' => [],
        ]);
    }
    

    private function address(array $extra = []): array
    {
        return array_merge([
            'full_name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890', // ← baru
            'address' => 'Jl. Malioboro 1',
            'city' => 'Yogyakarta',
            'state' => 'DIY',
            'zip' => '55271',
        ], $extra);
    }

    private function buyNowSession(int $productId): array
    {
        return ['buy_now' => ['product_id' => $productId, 'qty' => 1]];
    }

    public function test_checkout_page_lists_payment_methods_when_midtrans_is_configured(): void
    {
        $product = $this->makeProduct();

        $this->actingAs(User::factory()->create())
            ->withSession($this->buyNowSession($product->id))
            ->get(route('checkout.show'))
            ->assertOk()
            ->assertSee('BCA Virtual Account')
            ->assertSee('GoPay')
            ->assertSee('ShopeePay')
            ->assertSee('QRIS');
    }

    public function test_enabled_payments_env_limits_what_buyers_can_choose(): void
    {
        config(['midtrans.enabled_payments' => ['gopay', 'other_qris']]);
        $product = $this->makeProduct();

        $this->actingAs(User::factory()->create())
            ->withSession($this->buyNowSession($product->id))
            ->get(route('checkout.show'))
            ->assertOk()
            ->assertSee('GoPay')
            ->assertDontSee('BCA Virtual Account');
    }

    public function test_payment_method_is_required_when_midtrans_is_configured(): void
    {
        $product = $this->makeProduct();

        $this->actingAs(User::factory()->create())
            ->withSession($this->buyNowSession($product->id))
            ->post(route('checkout.store'), $this->address())
            ->assertSessionHasErrors('payment_channel');

        $this->assertSame(0, Order::count());
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_unknown_payment_method_is_rejected(): void
    {
        $product = $this->makeProduct();

        $this->actingAs(User::factory()->create())
            ->withSession($this->buyNowSession($product->id))
            ->post(route('checkout.store'), $this->address(['payment_channel' => 'bitcoin']))
            ->assertSessionHasErrors('payment_channel');

        $this->assertSame(0, Order::count());
    }

    public function test_chosen_method_is_saved_and_shown_on_payment_page(): void
    {
        Http::fake(['app.sandbox.midtrans.com/*' => Http::response(['token' => 'tok-abc'], 201)]);

        $user = User::factory()->create();
        $product = $this->makeProduct();

        $response = $this->actingAs($user)
            ->withSession($this->buyNowSession($product->id))
            ->post(route('checkout.store'), $this->address(['payment_channel' => 'gopay']));

        $order = Order::firstOrFail();
        $this->assertSame('gopay', $order->payment_channel);
        $response->assertRedirect(route('payment.show', $order->id));

        $this->actingAs($user)
            ->get(route('payment.show', $order->id))
            ->assertOk()
            ->assertSee('GoPay');
    }

    public function test_snap_is_limited_to_the_chosen_method(): void
    {
        config(['midtrans.enabled_payments' => ['bca_va', 'gopay', 'other_qris']]);
        Http::fake(['app.sandbox.midtrans.com/*' => Http::response(['token' => 'tok-abc'], 201)]);

        $order = $this->makeOrder(User::factory()->create(), $this->makeProduct());
        $order->forceFill(['payment_channel' => 'bca_va'])->save();

        app(MidtransService::class)->createSnapToken($order);

        Http::assertSent(fn ($r) => $r['enabled_payments'] === ['bca_va']);
    }

    public function test_checkout_works_without_a_choice_when_midtrans_is_not_configured(): void
    {
        config(['midtrans.server_key' => '', 'midtrans.client_key' => '']);
        $product = $this->makeProduct();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession($this->buyNowSession($product->id))
            ->get(route('checkout.show'))
            ->assertOk()
            ->assertDontSee('name="payment_channel"', false);

        $this->actingAs($user)
            ->withSession($this->buyNowSession($product->id))
            ->post(route('checkout.store'), $this->address(['payment_channel' => 'gopay']))
            ->assertRedirect();

        $this->assertNull(Order::firstOrFail()->payment_channel);
    }
}