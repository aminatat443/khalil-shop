<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tableau de bord (section 40 du cahier des charges).
     */
    public function index(): View
    {
        $confirmedStatuses = ['confirmee', 'en_preparation', 'expediee', 'livree'];

        return view('admin.dashboard', [
            'revenue' => Order::whereIn('status', $confirmedStatuses)->sum('total'),
            'ordersCount' => Order::count(),
            'clientsCount' => User::where('role', 'client')->count(),
            'productsCount' => Product::count(),
            'lowStockVariants' => \App\Models\ProductVariant::where('stock', '>', 0)->where('stock', '<=', 5)->with('product')->take(5)->get(),
            'outOfStockCount' => \App\Models\ProductVariant::where('stock', 0)->count() + Product::where('stock', 0)->whereDoesntHave('variants')->count(),
            'popularProducts' => Product::withCount(['images'])
                ->withSum('orderItems as total_sold', 'quantity')
                ->orderByDesc('total_sold')
                ->take(5)
                ->get(),
            'recentOrders' => Order::latest()->take(8)->get(),
        ]);
    }
}
