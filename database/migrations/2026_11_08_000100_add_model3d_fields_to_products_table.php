<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('model_path')->nullable();
            // null | queued | processing | ready | failed
            $table->string('model_status', 20)->nullable();
            $table->string('model_error', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['model_path', 'model_status', 'model_error']);
        });
    }
};
