<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('*', function ($view) {
        $request = request();
        $shared = $request->attributes->get('view.shared');

        if ($shared === null) {
            $shared = [
                'navCategories' => Category::orderBy('title')->get(),
                'navBrands' => Brand::orderBy('title')->get(),
                'cartCount' => Auth::check() ? (int) CartItem::where('user_id', Auth::id())->sum('qty') : 0,
                'storeSettings' => Schema::hasTable('settings') ? Setting::allCached() : Setting::DEFAULTS,
            ];
            $request->attributes->set('view.shared', $shared);
        }

        $view->with($shared);
    });
    }
}