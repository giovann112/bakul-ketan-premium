<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DailyStock;
use App\Models\Location;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::withCount('items')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->location_id, fn ($q, $l) => $q->where('location_id', $l))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', [
            'orders'    => $orders,
            'locations' => Location::orderBy('id')->get(),
        ]);
    }

    public function show(Order $order)
    {
        $order->load('items');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:'.implode(',', array_keys(Order::STATUSES)),
        ]);

        DB::transaction(function () use ($order, $data) {
            $order = Order::with('items')->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status === 'cancelled') {
                return;
            }

            if ($data['status'] === 'cancelled' && $order->location_id) {
                $totalPortions = (int) $order->items->sum('qty');
                $stock = DailyStock::where('location_id', $order->location_id)
                    ->where('stock_date', $order->created_at->toDateString())
                    ->lockForUpdate()
                    ->first();

                if ($stock && $totalPortions > 0) {
                    $stock->increment('remaining_portions', $totalPortions);
                }
            }

            $order->update(['status' => $data['status']]);
        });

        return back()->with('success', 'Status pesanan diperbarui.');
    }
}
