<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\BuildsShop;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase, BuildsShop;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_guest_cannot_post_review(): void
    {
        $product = $this->makeProduct();

        $this->post(route('reviews.store', $product), ['rating' => 5])->assertRedirect(route('login'));
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_buyer_without_completed_order_is_rejected(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct();
        $this->makeOrder($user, $product, 'paid');

        $this->actingAs($user)->post(route('reviews.store', $product), ['rating' => 5, 'comment' => 'Bagus'])
            ->assertSessionHas('error');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_buyer_with_completed_order_can_review_and_update(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct();
        $order = $this->makeOrder($user, $product, 'completed');

        $this->actingAs($user)->post(route('reviews.store', $product), ['rating' => 4, 'comment' => 'Suaranya jernih'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'product_id' => $product->id, 'order_id' => $order->id, 'rating' => 4]);

        $this->actingAs($user)->post(route('reviews.store', $product), ['rating' => 5])->assertSessionHas('success');

        $this->assertDatabaseCount('reviews', 1);
        $this->assertSame(5, Review::first()->rating);
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        $user = User::factory()->create();
        $product = $this->makeProduct();
        $this->makeOrder($user, $product, 'completed');

        $this->actingAs($user)->post(route('reviews.store', $product), ['rating' => 6])->assertSessionHasErrors('rating');
        $this->actingAs($user)->post(route('reviews.store', $product), [])->assertSessionHasErrors('rating');
    }

    public function test_user_can_only_delete_own_review(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $product = $this->makeProduct();
        $review = Review::create(['user_id' => $owner->id, 'product_id' => $product->id, 'rating' => 3]);

        $this->actingAs($other)->delete(route('reviews.destroy', $review))->assertForbidden();
        $this->actingAs($owner)->delete(route('reviews.destroy', $review))->assertRedirect();
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_product_page_shows_real_average_and_no_fake_numbers(): void
    {
        $product = $this->makeProduct();
        foreach ([5, 4, 3] as $i => $rating) {
            Review::create(['user_id' => User::factory()->create(['name' => "Pembeli Satu$i"])->id, 'product_id' => $product->id, 'rating' => $rating, 'comment' => "Komentar $i"]);
        }

        $html = $this->get(route('products.show', $product))->assertOk()->getContent();

        $this->assertStringContainsString('4,0', $html);
        $this->assertStringContainsString('3 ulasan', $html);
        $this->assertStringContainsString('Pembeli S.', $html);   // nama disamarkan
        $this->assertStringNotContainsString('1.2rb', $html);
        $this->assertStringNotContainsString('Pembeli Satu0', $html);
    }

    public function test_listing_shows_review_summary_only_when_reviews_exist(): void
    {
        $product = $this->makeProduct();
        $this->get(route('home'))->assertOk()->assertSee('Belum ada ulasan');

        Review::create(['user_id' => User::factory()->create()->id, 'product_id' => $product->id, 'rating' => 5]);
        $this->get(route('home'))->assertOk()->assertSee('5,0 (1)');
    }
}
