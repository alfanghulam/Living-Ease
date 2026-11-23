<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\Support\BuildsShop;
use Tests\TestCase;

class MidtransTest extends TestCase
{
    use RefreshDatabase, BuildsShop;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    protected function configure(): void
    {
        config(['midtrans.server_key' => 'SB-Mid-server-test', 'midtrans.client_key' => 'SB-Mid-client-test', 'midtrans.is_production' => false]);
    }

    protected function payload(string $orderId, int $amount, string $status = 'settlement', array $extra = []): array
    {
        $payload = array_merge([
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => number_format($amount, 2, '.', ''),
            'transaction_status' => $status,
            'payment_type' => 'qris',
            'transaction_id' => 'trx-123',
        ], $extra);

        $payload['signature_key'] = hash('sha512', $payload['order_id'].$payload['status_code'].$payload['gross_amount'].config('midtrans.server_key'));

        return $payload;
    }

    public function test_snap_token_is_created_and_reused(): void
    {
        $this->configure();
        Http::fake(['app.sandbox.midtrans.com/*' => Http::response(['token' => 'tok-abc', 'redirect_url' => 'x'], 201)]);

        $user = User::factory()->create();
        $order = $this->makeOrder($user, $this->makeProduct());

        $token = app(MidtransService::class)->createSnapToken($order);
        $this->assertSame('tok-abc', $token);

        $order->refresh();
        $this->assertStringStartsWith('VS-'.$order->id.'-', $order->midtrans_order_id);
        $this->assertSame('tok-abc', $order->snap_token);

        app(MidtransService::class)->createSnapToken($order);   // dipakai ulang, tanpa request kedua
        Http::assertSentCount(1);
        Http::assertSent(fn ($r) => $r->hasHeader('Authorization') && $r['transaction_details']['gross_amount'] === 1000000);
    }

    public function test_checkout_goes_to_payment_page_when_midtrans_is_configured(): void
    {
        $this->configure();
        Http::fake(['app.sandbox.midtrans.com/*' => Http::response(['token' => 'tok-xyz'], 201)]);

        $user = User::factory()->create();
        $order = $this->makeOrder($user, $this->makeProduct());

        $this->actingAs($user)->get(route('payment.show', $order->id))
            ->assertOk()->assertSee('snap.js', false)->assertSee('tok-xyz', false);
    }

    public function test_old_flow_is_kept_when_midtrans_is_not_configured(): void
    {
        config(['midtrans.server_key' => null, 'midtrans.client_key' => null]);

        $user = User::factory()->create();
        $order = $this->makeOrder($user, $this->makeProduct());

        $this->actingAs($user)->get(route('payment.show', $order->id))->assertRedirect(route('checkout.success', $order->id));
        $this->actingAs($user)->get(route('checkout.success', $order->id))->assertOk()->assertSee('konfirmasi pembayaran lewat WhatsApp');
    }

    public function test_other_user_cannot_open_payment_page(): void
    {
        $this->configure();
        $order = $this->makeOrder(User::factory()->create(), $this->makeProduct());

        $this->actingAs(User::factory()->create())->get(route('payment.show', $order->id))->assertForbidden();
    }

    public function test_valid_settlement_marks_paid_and_reduces_stock_once(): void
    {
        $this->configure();
        $product = $this->makeProduct(['stock' => 10]);
        $order = $this->makeOrder(User::factory()->create(), $product, qty: 2);
        $order->forceFill(['midtrans_order_id' => 'VS-'.$order->id.'-111'])->save();

        $payload = $this->payload($order->midtrans_order_id, 1000000);

        $this->postJson(route('midtrans.notification'), $payload)->assertOk();
        $this->postJson(route('midtrans.notification'), $payload)->assertOk();   // notifikasi dobel

        $order->refresh();
        $this->assertSame('paid', $order->status);
        $this->assertSame('qris', $order->payment_type);
        $this->assertNotNull($order->paid_at);
        $this->assertSame(8, $product->fresh()->stock);
    }

    public function test_invalid_signature_is_rejected(): void
    {
        $this->configure();
        $order = $this->makeOrder(User::factory()->create(), $this->makeProduct());
        $order->forceFill(['midtrans_order_id' => 'VS-'.$order->id.'-111'])->save();

        $payload = $this->payload($order->midtrans_order_id, 1000000);
        $payload['signature_key'] = 'palsu';

        $this->postJson(route('midtrans.notification'), $payload)->assertForbidden();
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_amount_mismatch_does_not_mark_paid(): void
    {
        $this->configure();
        $order = $this->makeOrder(User::factory()->create(), $this->makeProduct());
        $order->forceFill(['midtrans_order_id' => 'VS-'.$order->id.'-111'])->save();

        $this->postJson(route('midtrans.notification'), $this->payload($order->midtrans_order_id, 1000))->assertOk();

        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_expire_cancels_pending_order_and_pending_status_changes_nothing(): void
    {
        $this->configure();
        $order = $this->makeOrder(User::factory()->create(), $this->makeProduct());
        $order->forceFill(['midtrans_order_id' => 'VS-'.$order->id.'-111'])->save();

        $this->postJson(route('midtrans.notification'), $this->payload($order->midtrans_order_id, 1000000, 'pending'))->assertOk();
        $this->assertSame('pending', $order->fresh()->status);

        $this->postJson(route('midtrans.notification'), $this->payload($order->midtrans_order_id, 1000000, 'expire'))->assertOk();
        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_cancelled_order_is_not_resurrected_by_late_settlement(): void
    {
        $this->configure();
        $order = $this->makeOrder(User::factory()->create(), $this->makeProduct(), 'cancelled');
        $order->forceFill(['midtrans_order_id' => 'VS-'.$order->id.'-111'])->save();

        $this->postJson(route('midtrans.notification'), $this->payload($order->midtrans_order_id, 1000000))->assertOk();

        $this->assertSame('cancelled', $order->fresh()->status);
    }

    public function test_success_page_pulls_latest_status_from_midtrans(): void
    {
        $this->configure();
        $user = User::factory()->create();
        $order = $this->makeOrder($user, $this->makeProduct());
        $order->forceFill(['midtrans_order_id' => 'VS-'.$order->id.'-111'])->save();

        Http::fake(['api.sandbox.midtrans.com/*' => Http::response($this->payload($order->midtrans_order_id, 1000000))]);

        $this->actingAs($user)->get(route('checkout.success', $order->id))->assertOk()->assertSee('Pembayaran diterima');
        $this->assertSame('paid', $order->fresh()->status);
    }
}
