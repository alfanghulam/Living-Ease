<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Support\Phone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\Support\BuildsShop;
use Tests\TestCase;

class ShippingFoundationTest extends TestCase
{
    use RefreshDatabase, BuildsShop;

    protected function setUp(): void
    {
        parent::setUp();

        config(['midtrans.server_key' => '', 'midtrans.client_key' => '']);
    }

    private function checkout(User $user, int $productId, array $extra = [])
    {
        return $this->actingAs($user)
            ->withSession(['buy_now' => ['product_id' => $productId, 'qty' => 1]])
            ->post(route('checkout.store'), array_merge([
                'full_name' => 'Budi', 'email' => 'budi@example.com', 'phone' => '081234567890',
                'address' => 'Jl. Malioboro 1', 'city' => 'Yogyakarta', 'state' => 'DIY', 'zip' => '55271',
            ], $extra));
    }

    private function tinyPng(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'png');
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));

        return new UploadedFile($path, 'meja.png', 'image/png', null, true);
    }

    public function test_phone_helper_normalizes_and_validates(): void
    {
        $this->assertSame('081234567890', Phone::normalize('+62 812-3456-7890'));
        $this->assertSame('081234567890', Phone::normalize('0812 3456 7890'));
        $this->assertSame('081234567890', Phone::normalize('6281234567890'));
        $this->assertTrue(Phone::isValid('081234567890'));
        $this->assertFalse(Phone::isValid('12345'));
        $this->assertFalse(Phone::isValid('0211234567'));
        $this->assertFalse(Phone::isValid(null));
    }

    public function test_checkout_requires_a_valid_phone_and_stores_it_normalized(): void
    {
        $product = $this->makeProduct();
        $user = User::factory()->create();

        $this->checkout($user, $product->id, ['phone' => ''])->assertSessionHasErrors('phone');
        $this->checkout($user, $product->id, ['phone' => '12345'])->assertSessionHasErrors('phone');
        $this->assertSame(0, Order::count());

        $this->checkout($user, $product->id, ['phone' => '+62 812-3456-7890'])->assertSessionHasNoErrors();
        $this->assertSame('081234567890', Order::first()->phone);
    }

    public function test_checkout_form_prefills_last_used_phone(): void
    {
        $product = $this->makeProduct(['stock' => 5]);
        $user = User::factory()->create();

        $this->checkout($user, $product->id, ['phone' => '085711112222']);

        $this->actingAs($user)->withSession(['buy_now' => ['product_id' => $product->id, 'qty' => 1]])
            ->get(route('checkout.show'))
            ->assertOk()
            ->assertSee('Nomor HP penerima', false)
            ->assertSee('value="085711112222"', false);
    }

    public function test_admin_order_page_shows_phone(): void
    {
        $order = $this->makeOrder(User::factory()->create(), $this->makeProduct());
        $order->update(['phone' => '081234567890']);

        $this->actingAs($this->admin())->get(route('admin.orders.show', $order))
            ->assertOk()->assertSee('081234567890', false);
    }

    public function test_admin_saves_shipping_data_on_product_and_it_is_optional(): void
    {
        $product = $this->makeProduct();
        $payload = fn (array $extra = []) => array_merge([
            'category_id' => $product->category_id, 'brand_id' => $product->brand_id,
            'title' => 'Sofa Uji', 'price' => 1000000, 'stock' => 3, 'description' => 'Sofa.',
        ], $extra);

        $this->actingAs($this->admin())->put(route('admin.products.update', $product), $payload([
            'weight_grams' => 25000, 'length_cm' => 200, 'width_cm' => 90, 'height_cm' => 80,
        ]))->assertSessionHasNoErrors();

        $product = $product->fresh();
        $this->assertSame(25000, $product->weight_grams);
        $this->assertSame(200, $product->length_cm);
        $this->assertTrue($product->hasShippingData());

        $this->actingAs($this->admin())->put(route('admin.products.update', $product), $payload())
            ->assertSessionHasNoErrors();

        $this->assertNull($product->fresh()->weight_grams);
        $this->assertFalse($product->fresh()->hasShippingData());
    }

    public function test_shipping_data_rejects_zero_negative_and_absurd_values(): void
    {
        $product = $this->makeProduct();
        $base = [
            'category_id' => $product->category_id, 'brand_id' => $product->brand_id,
            'title' => 'X', 'price' => 1000, 'stock' => 1, 'description' => 'X',
        ];

        foreach ([['weight_grams' => 0], ['weight_grams' => -5], ['length_cm' => 0], ['height_cm' => 5000], ['weight_grams' => 9999999]] as $bad) {
            $this->actingAs($this->admin())->put(route('admin.products.update', $product), $base + $bad)
                ->assertSessionHasErrors(array_key_first($bad));
        }
    }

    public function test_new_product_form_accepts_shipping_data(): void
    {
        $product = $this->makeProduct();

        $this->actingAs($this->admin())->post(route('admin.products.store'), [
            'category_id' => $product->category_id, 'brand_id' => $product->brand_id,
            'title' => 'Meja Baru', 'price' => 750000, 'stock' => 2, 'description' => 'Meja.',
            'image' => $this->tinyPng(),
            'weight_grams' => 15000, 'length_cm' => 120, 'width_cm' => 60, 'height_cm' => 75,
        ])->assertSessionHasNoErrors();

        $this->assertSame(15000, \App\Models\Product::where('title', 'Meja Baru')->first()->weight_grams);
    }

    public function test_product_list_warns_about_missing_shipping_data(): void
    {
        $this->makeProduct();

        $this->actingAs($this->admin())->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('1 produk belum punya data berat', false);

        \App\Models\Product::query()->update(['weight_grams' => 1000, 'length_cm' => 10, 'width_cm' => 10, 'height_cm' => 10]);

        $this->actingAs($this->admin())->get(route('admin.products.index'))
            ->assertOk()
            ->assertDontSee('produk belum punya data berat', false);
    }

    public function test_origin_address_settings_are_validated_and_reported(): void
    {
        $base = [
            'store_name' => 'LivingEase', 'promo_percent' => 40,
            'origin_name' => 'Gudang Vanz', 'origin_phone' => '+62 812-3456-7890',
            'origin_address' => 'Jl. Kaliurang Km 5', 'origin_city' => 'Sleman',
            'origin_province' => 'DI Yogyakarta', 'origin_postal_code' => '55281',
        ];

        $this->assertFalse(Setting::originReady());

        $this->actingAs($this->admin())->put(route('admin.settings.update'), $base)->assertSessionHasNoErrors();

        $this->assertSame('081234567890', Setting::get('origin_phone'));
        $this->assertTrue(Setting::originReady());
        $this->actingAs($this->admin())->get(route('admin.settings.index'))->assertSee('lengkap ✓', false);

        $this->actingAs($this->admin())->put(route('admin.settings.update'), array_merge($base, ['origin_postal_code' => '5528']))
            ->assertSessionHasErrors('origin_postal_code');
        $this->actingAs($this->admin())->put(route('admin.settings.update'), array_merge($base, ['origin_phone' => '12345']))
            ->assertSessionHasErrors('origin_phone');
         
        $this->actingAs($this->admin())->put(route('admin.settings.update'), array_merge($base, ['origin_postal_code' => '']))
            ->assertSessionHasNoErrors();
        $this->assertFalse(Setting::originReady());
    }
}