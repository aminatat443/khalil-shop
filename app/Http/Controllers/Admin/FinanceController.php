<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\View\View;

class FinanceController extends Controller
{
    /**
     * Vue d'ensemble financière — même définition du chiffre d'affaires que le tableau de bord
     * (statuts "confirmée" et suivants, une commande "reçue" n'étant pas encore acquise).
     */
    private const CONFIRMED_STATUSES = ['confirmee', 'en_preparation', 'expediee', 'livree'];

    public function index(): View
    {
        $this->authorize('viewAny', Order::class);

        $confirmed = Order::whereIn('status', self::CONFIRMED_STATUSES);

        $revenue = (clone $confirmed)->sum('total');
        $ordersCount = (clone $confirmed)->count();

        $thisMonth = (clone $confirmed)->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('total');
        $lastMonth = (clone $confirmed)
            ->whereYear('created_at', now()->subMonthNoOverflow()->year)
            ->whereMonth('created_at', now()->subMonthNoOverflow()->month)
            ->sum('total');
        $monthTrend = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100) : ($thisMonth > 0 ? 100 : 0);

        $last6Months = collect(range(5, 0))->map(function (int $monthsAgo) {
            $month = now()->subMonthsNoOverflow($monthsAgo);

            return [
                'label' => $month->translatedFormat('M Y'),
                'revenue' => Order::whereIn('status', self::CONFIRMED_STATUSES)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total'),
            ];
        });

        $byPaymentMethod = (clone $confirmed)
            ->selectRaw('payment_method, count(*) as orders_count, sum(total) as total')
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        return view('admin.finances.index', [
            'revenue' => $revenue,
            'ordersCount' => $ordersCount,
            'averageOrder' => $ordersCount > 0 ? (int) round($revenue / $ordersCount) : 0,
            'totalDiscounts' => (clone $confirmed)->sum('discount'),
            'thisMonth' => $thisMonth,
            'monthTrend' => $monthTrend,
            'last6Months' => $last6Months,
            'byPaymentMethod' => $byPaymentMethod,
        ]);
    }
}
