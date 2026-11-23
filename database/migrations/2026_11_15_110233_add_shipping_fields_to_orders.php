<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('courier')->nullable();          // JNE, JNT, dll
            $table->string('service_code')->nullable();    // contoh: JNE_REG, JNT_2DAY
            $table->decimal('shipping_fee', 12, 2)->nullable(); // tarif dalam rupiah
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['courier', 'service_code', 'shipping_fee']);
        });
    }
};