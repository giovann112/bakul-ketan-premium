<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\StockPool;

class PageController extends Controller
{
    public function home()
    {
        $featured = Product::with('category')->active()->take(6)->get();
        $locations = StockPool::locationsWithTodayStock();
        $selectedLocation = StockPool::selectedLocation();

        return view('home', compact('featured', 'locations', 'selectedLocation'));
    }

    public function about()
    {
        $locations = StockPool::locationsWithTodayStock();

        return view('about', compact('locations'));
    }
}
