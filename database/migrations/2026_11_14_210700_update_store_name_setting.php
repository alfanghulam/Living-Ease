<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->where('key', 'store_name')->update(['value' => 'LivingEase']);
        Cache::forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'store_name')->update(['value' => 'Vanz Living']);
        Cache::forget('settings.all');
    }
};
