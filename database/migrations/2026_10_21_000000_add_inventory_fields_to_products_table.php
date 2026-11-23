<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->unique()->after('title');
            $table->unsignedInteger('stock')->default(0)->after('price');
            $table->unsignedInteger('discount_price')->nullable()->after('price');
        });

        // Backfill data lama supaya katalog tidak tiba-tiba "habis".
        foreach (DB::table('products')->get() as $product) {
            DB::table('products')->where('id', $product->id)->update([
                'sku' => 'VS-'.str_pad((string) $product->id, 4, '0', STR_PAD_LEFT),
                'stock' => 12,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['sku', 'stock', 'discount_price']);
        });
    }
};
