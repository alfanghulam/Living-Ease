<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Data contoh toko perabot klasik (opsional):
 *   php artisan db:seed --class=FurnitureSeeder
 * Nama, harga, dan deskripsi hanya contoh; ganti lewat panel admin.
 */
class FurnitureSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::firstOrCreate(['title' => 'Vanz Living']);

        $items = [
            ['Meja', 'Meja Samping Mahoni', 1850000, 12, 'meja-samping-mahoni.webp',
                'Meja samping bundar berkaki lengkung dengan rak di bagian bawah. Permukaan kayu berpelitur halus, pas di samping sofa atau kursi baca.'],
            ['Meja Makan', 'Set Meja Makan Bundar', 6400000, 5, 'set-meja-makan-bundar.webp',
                'Satu meja bundar berkaki tunggal dengan empat kursi bersandaran bergelombang. Cocok untuk ruang makan keluarga.'],
            ['Lampu', 'Lampu Dinding Lentera', 890000, 20, 'lampu-dinding-lentera.webp',
                'Lampu dinding bergaya antik dengan rangka besi dan panel kaca. Cahayanya hangat dan memberi suasana klasik pada lorong atau teras.'],
            ['Sofa', 'Sofa Chesterfield Kulit', 12900000, 3, 'sofa-chesterfield-kulit.webp',
                'Sofa tiga dudukan dengan sandaran tufting dan lengan melengkung khas Chesterfield. Berkaki kayu berukir.'],
        ];

        foreach ($items as $i => [$category, $title, $price, $stock, $image, $description]) {
            $cat = Category::firstOrCreate(['title' => $category]);

            Product::updateOrCreate(['title' => $title], [
                'category_id' => $cat->id,
                'brand_id' => $brand->id,
                'price' => $price,
                'stock' => $stock,
                'description' => $description,
                'image' => $image,
                'keywords' => strtolower("$category $title perabot klasik"),
                'is_active' => true,
                'sku' => 'VL-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
