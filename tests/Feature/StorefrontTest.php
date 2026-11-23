<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\FurnitureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\BuildsShop;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase, BuildsShop;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_home_has_room_hero_collection_and_chat_widget(): void
    {
        $this->seed(FurnitureSeeder::class);

        $this->get('/')->assertOk()
            ->assertSee('img/hero-room.jpg', false)
            ->assertSee('Sofa Chesterfield Kulit')
            ->assertSee('id="vs-qv"', false)      // dialog lihat-cepat
            ->assertSee('vs-chat', false);        // chatbot tetap terpasang
    }

    public function test_seeder_is_idempotent_and_images_exist(): void
    {
        $this->seed(FurnitureSeeder::class);
        $this->seed(FurnitureSeeder::class);

        $this->assertSame(4, Product::count());

        foreach (Product::all() as $product) {
            $this->assertFileExists(public_path('img/products/'.$product->image));
        }
    }

    public function test_quick_view_data_exposes_3d_model_only_when_ready(): void
    {
        $plain = $this->makeProduct(['title' => 'Kursi Biasa']);
        $with3d = $this->makeProduct(['title' => 'Kursi Putar', 'model_status' => 'ready', 'model_path' => 'models/x.glb']);

        $html = $this->get(route('home'))->assertOk()->getContent();

       $this->assertStringContainsString('media\/models\/x.glb', html_entity_decode($html));
        $this->assertSame(1, substr_count(html_entity_decode($html), 'media\/models\/x.glb'));
        $this->assertStringContainsString('Putar 360°', $html);
    }

    public function test_chat_routes_are_still_registered(): void
    {
        $this->assertTrue(route('chat.send') !== '' && route('chat.history') !== '');
        $this->getJson(route('chat.history'))->assertSuccessful();
    }
}
