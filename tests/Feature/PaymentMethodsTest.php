<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Support\BuildsShop;
use Tests\TestCase;

class PaymentMethodsTest extends TestCase
{
    use RefreshDatabase, BuildsShop;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'midtrans.server_key' => 'SB-Mid-server-test',
            'midtrans.client_key' => 'SB-Mid-client-test',
            'midtrans.is_production' => false,
        ]);
    }

    public function test_enabled_payments_are_sent_to_midtrans_when_configured(): void
    {
        config(['midtrans.enabled_payments' => ['bca_va', 'gopay', 'other_qris']]);
        Http::fake(['app.sandbox.midtrans.com/*' => Http::response(['token' => 'tok-abc'], 201)]);

        $order = $this->makeOrder(User::factory()->create(), $this->makeProduct());

        app(MidtransService::class)->createSnapToken($order);

        Http::assertSent(fn ($r) => $r['enabled_payments'] === ['bca_va', 'gopay', 'other_qris']);
    }

    public function test_enabled_payments_is_omitted_when_not_configured(): void
    {
        config(['midtrans.enabled_payments' => []]);
        Http::fake(['app.sandbox.midtrans.com/*' => Http::response(['token' => 'tok-abc'], 201)]);

        $order = $this->makeOrder(User::factory()->create(), $this->makeProduct());

        app(MidtransService::class)->createSnapToken($order);

        Http::assertSent(fn ($r) => ! array_key_exists('enabled_payments', $r->data()));
    }

    public function test_payment_label_is_human_readable(): void
    {
        $order = new Order();

        $order->forceFill(['payment_type' => null]);
        $this->assertNull($order->paymentLabel());

        $order->forceFill(['payment_type' => 'qris']);
        $this->assertSame('QRIS', $order->paymentLabel());

        $order->forceFill(['payment_type' => 'bank_transfer']);
        $this->assertSame('Transfer bank (Virtual Account)', $order->paymentLabel());

        $order->forceFill(['payment_type' => 'gopay']);
        $this->assertSame('GoPay', $order->paymentLabel());

        $order->forceFill(['payment_type' => 'metode_baru']);
        $this->assertSame('Metode Baru', $order->paymentLabel());
    }
}