<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Support\StockPool;

class DashboardController extends Controller
{
    public function index()
    {
        $valid = fn () => Order::where('status', '!=', 'cancelled');

        $locations = StockPool::locationsWithTodayStock();
        $totalRemainingPortions = $locations->sum(fn ($l) => $l->today_stock->remaining_portions);

        $stats = [
            'orders_total'       => Order::count(),
            'orders_pending'     => Order::where('status', 'pending')->count(),
            'revenue_month'      => (int) $valid()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('total'),
            'remaining_portions' => $totalRemainingPortions,
        ];

        $topProducts = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->selectRaw('order_items.product_name, SUM(order_items.qty) as total_qty')
            ->groupBy('order_items.product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $revenue = $valid()
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as d, SUM(total) as revenue')
            ->groupBy('d')
            ->pluck('revenue', 'd');

        $series = collect(range(29, 0))->map(function ($i) use ($revenue) {
            $date = now()->subDays($i);

            return ['date' => $date, 'revenue' => (int) ($revenue[$date->toDateString()] ?? 0)];
        });

        return view('admin.dashboard', [
            'stats'       => $stats,
            'locations'   => $locations,
            'topProducts' => $topProducts,
            'series'      => $series,
            'maxRevenue'  => max(1, $series->max('revenue')),
            'recent'      => Order::latest()->limit(5)->get(),
        ]);
    }
}
