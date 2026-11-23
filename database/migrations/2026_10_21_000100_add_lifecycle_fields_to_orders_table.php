<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->timestamp('shipped_at')->nullable()->after('paid_at');
            $table->timestamp('completed_at')->nullable()->after('shipped_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
            $table->string('tracking_number')->nullable()->after('cancelled_at');
            $table->text('note')->nullable()->after('tracking_number');
            $table->boolean('stock_applied')->default(false)->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'paid_at',
                'shipped_at',
                'completed_at',
                'cancelled_at',
                'tracking_number',
                'note',
                'stock_applied',
            ]);
        });
    }
};
