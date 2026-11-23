<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\BuildsShop;
use Tests\TestCase;

class CartActiveProductTest extends TestCase
{
    use RefreshDatabase, BuildsShop;

    public function test_inactive_product_cannot_be_added_to_cart(): void
    {
        $product = $this->makeProduct(['is_active' => false]);

        $this->actingAs(User::factory()->create())
            ->post(route('cart.store', $product), ['qty' => 1])
            ->assertSessionHas('error');

        $this->assertSame(0, CartItem::count());
    }

    public function test_inactive_product_returns_422_for_ajax_requests(): void
    {
        $product = $this->makeProduct(['is_active' => false]);

        $this->actingAs(User::factory()->create())
            ->postJson(route('cart.store', $product), ['qty' => 1])
            ->assertStatus(422)
            ->assertJson(['ok' => false]);

        $this->assertSame(0, CartItem::count());
    }

    public function test_active_product_can_still_be_added_to_cart(): void
    {
        $product = $this->makeProduct(['is_active' => true, 'stock' => 5]);

        $this->actingAs(User::factory()->create())
            ->post(route('cart.store', $product), ['qty' => 2])
            ->assertSessionHas('success');

        $this->assertSame(2, (int) CartItem::value('qty'));
    }
}