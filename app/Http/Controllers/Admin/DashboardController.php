<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tableau de bord (section 40 du cahier des charges).
     */
    public function index(): View
    {
        $confirmedStatuses = ['confirmee', 'en_preparation', 'expediee', 'livree'];

        $last7Days = collect(range(6, 0))->map(function (int $daysAgo) use ($confirmedStatuses) {
            $day = now()->subDays($daysAgo);

            return [
                'label' => $day->translatedFormat('D'),
                'revenue' => Order::whereIn('status', $confirmedStatuses)
                    ->whereDate('created_at', $day)
                    ->sum('total'),
            ];
        });

        $ordersThisWeek = Order::where('created_at', '>=', now()->subDays(7))->count();
        $ordersPrevWeek = Order::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->count();
        $ordersTrend = $ordersPrevWeek > 0
            ? round((($ordersThisWeek - $ordersPrevWeek) / $ordersPrevWeek) * 100)
            : ($ordersThisWeek > 0 ? 100 : 0);

        return view('admin.dashboard', [
            'revenue' => Order::whereIn('status', $confirmedStatuses)->sum('total'),
            'ordersCount' => Order::count(),
            'clientsCount' => User::where('role', 'client')->count(),
            'productsCount' => Product::count(),
            'ordersTrend' => $ordersTrend,
            'last7Days' => $last7Days,
            'lowStockVariants' => ProductVariant::where('stock', '>', 0)->where('stock', '<=', 5)->with('product')->take(5)->get(),
            'outOfStockCount' => ProductVariant::where('stock', 0)->count() + Product::where('stock', 0)->whereDoesntHave('variants')->count(),
            'popularProducts' => Product::withCount(['images'])
                ->with(['images' => fn ($q) => $q->orderBy('sort_order')])
                ->withSum('orderItems as total_sold', 'quantity')
                ->orderByDesc('total_sold')
                ->take(5)
                ->get(),
            'recentOrders' => Order::latest()->take(6)->get(),
            'pendingReturnsCount' => \App\Models\ProductReturn::where('status', 'demandee')->count(),
        ]);
    }
}
