<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // total = jumlah yang ditagih (sudah dipotong). Kode & potongan disimpan sebagai catatan.
            $table->string('promo_code', 40)->nullable()->after('total');
            $table->unsignedBigInteger('discount_amount')->default(0)->after('promo_code');
            $table->index(['user_id', 'promo_code']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'promo_code']);
            $table->dropColumn(['promo_code', 'discount_amount']);
        });
    }
};