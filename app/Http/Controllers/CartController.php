<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use App\Support\StockPool;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index', [
            'items'            => Cart::items(),
            'total'            => Cart::total(),
            'totalPortions'    => Cart::count(),
            'locations'        => StockPool::locationsWithTodayStock(),
            'selectedLocation' => StockPool::selectedLocation(),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $data = $request->validate([
            'qty'         => 'nullable|integer|min:1|max:99',
            'toppings'    => 'nullable|array',
            'toppings.*'  => 'integer|exists:toppings,id',
            'location_id' => 'nullable|integer|exists:locations,id',
        ]);

        if (! empty($data['location_id'])) {
            StockPool::selectLocation((int) $data['location_id']);
        }

        $error = Cart::add(
            $product,
            (int) ($data['qty'] ?? 1),
            $data['toppings'] ?? [],
            StockPool::selectedLocationId()
        );

        return $error
            ? back()->with('error', $error)
            : back()->with('success', $product->name.' masuk ke keranjang.');
    }

    public function update(Request $request, string $key)
    {
        $data = $request->validate(['qty' => 'required|integer|min:0|max:99']);

        $error = Cart::setQty($key, (int) $data['qty'], StockPool::selectedLocationId());

        return $error ? back()->with('error', $error) : back();
    }

    public function remove(string $key)
    {
        Cart::remove($key);

        return back();
    }
}
