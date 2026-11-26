<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\SchemaGuard;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        SchemaGuard::ensureOrderTables();

        return view('tracking.index');
    }

    public function search(Request $request)
    {
        SchemaGuard::ensureOrderTables();

        $request->validate([
            'order_code' => 'required|string|max:40',
            'phone'      => 'required|string|max:20',
        ]);

        $order = Order::with('items')
            ->where('order_code', strtoupper(trim($request->order_code)))
            ->where('phone', Order::normalizePhone($request->phone))
            ->first();

        if (! $order) {
            return back()->withInput()->with('error', 'Pesanan tidak ditemukan. Periksa kode pesanan dan nomor WhatsApp.');
        }

        return view('tracking.index', compact('order'));
    }
}
