<?php

namespace Tests\Support;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;

trait BuildsShop
{
    protected function makeProduct(array $overrides = []): Product
    {
        $category = Category::firstOrCreate(['title' => 'Elektronik']);
        $brand = Brand::firstOrCreate(['title' => 'Vanz']);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'title' => 'Speaker Kubus',
            'price' => 500000,
            'stock' => 10,
            'description' => 'Speaker bluetooth.',
            'image' => 'speaker.png',
            'keywords' => 'speaker',
            'is_active' => true,
        ], $overrides));
    }

    protected function makeOrder(User $user, Product $product, string $status = 'pending', int $qty = 2): Order
    {
        $order = Order::create([
            'user_id' => $user->id,
            'full_name' => 'Budi Santoso',
            'email' => $user->email,
            'address' => 'Jl. Malioboro 1',
            'city' => 'Yogyakarta',
            'state' => 'DIY',
            'zip' => '55271',
            'total' => $product->price * $qty,
            'status' => $status,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'qty' => $qty,
            'price' => $product->price,
        ]);

        return $order;
    }

    protected function admin(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['is_admin' => true])->save();

        return $user;
    }
}
