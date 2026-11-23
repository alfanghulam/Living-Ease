<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('q');

        $customers = User::query()
            ->where('is_admin', false)
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->when($search, function ($query) use ($search) {
                $like = '%'.trim($search).'%';
                $query->where(fn ($sub) => $sub->where('name', 'like', $like)->orWhere('email', 'like', $like));
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $lastOrders = Order::query()
            ->selectRaw('user_id, MAX(created_at) as last_at')
            ->whereIn('user_id', $customers->pluck('id'))
            ->groupBy('user_id')
            ->pluck('last_at', 'user_id');

        $totalCustomers = User::where('is_admin', false)->count();

        return view('admin.customers.index', compact('customers', 'lastOrders', 'search', 'totalCustomers'));
    }

    public function show(User $user)
    {
        abort_if($user->is_admin, 404);

        $orders = $user->orders()
            ->withSum('items', 'qty')
            ->latest()
            ->get();

        $stats = [
            'count' => $orders->count(),
            'spent' => $orders->sum('total'),
            'last' => $orders->first()?->created_at,
        ];

        $chats = ChatMessage::where('user_id', $user->id)
            ->latest('id')
            ->take(8)
            ->get()
            ->reverse();

        return view('admin.customers.show', compact('user', 'orders', 'stats', 'chats'));
    }
}
