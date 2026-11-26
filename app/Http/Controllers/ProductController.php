<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Topping;
use App\Support\StockPool;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->active()
            ->when($request->q, fn ($q, $s) => $q->where('name', 'like', '%'.$s.'%'))
            ->when($request->kategori, fn ($q, $c) => $q->whereHas('category', fn ($x) => $x->where('slug', $c)))
            ->orderBy('category_id')
            ->orderBy('id')
            ->paginate(12)
            ->withQueryString();

        return view('products.index', [
            'products'         => $products,
            'categories'       => Category::orderBy('id')->get(),
            'toppings'         => Topping::active()->orderBy('id')->get(),
            'locations'        => StockPool::locationsWithTodayStock(),
            'selectedLocation' => StockPool::selectedLocation(),
        ]);
    }

    public function show(string $slug)
    {
        $product = Product::with('category')->active()->where('slug', $slug)->firstOrFail();

        return view('products.show', [
            'product'          => $product,
            'toppings'         => Topping::active()->orderBy('id')->get(),
            'locations'        => StockPool::locationsWithTodayStock(),
            'selectedLocation' => StockPool::selectedLocation(),
        ]);
    }
}
