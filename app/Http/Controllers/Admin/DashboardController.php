<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Total penjualan 7 hari terakhir, per tanggal
        $salesRaw = Order::selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->translatedFormat('d M');
            $chartValues[] = $salesRaw->has($date) ? (int) $salesRaw[$date]->total : 0;
        }

        return view('admin.dashboard', [
            'totalProducts'   => Product::count(),
            'totalCategories' => Category::count(),
            'totalBrands'     => Brand::count(),
            'totalUsers'      => User::where('is_admin', false)->count(),
            'totalSales'      => Order::sum('total'),
            'totalOrders'     => Order::count(),
            'ordersThisWeek'  => Order::where('created_at', '>=', now()->subDays(6)->startOfDay())->count(),
            'pendingOrders'   => Order::where('status', 'pending')->count(),
            'lowStock'        => Product::where('stock', '<=', 3)->count(),
            'recentOrders'    => Order::latest()->take(5)->get(),
            'chartLabels'     => $chartLabels,
            'chartValues'     => $chartValues,
        ]);
    }
}