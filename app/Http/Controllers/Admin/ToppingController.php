<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topping;
use Illuminate\Http\Request;

class ToppingController extends Controller
{
    public function index()
    {
        return view('admin.toppings.index', [
            'toppings' => Topping::orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'price' => 'required|integer|min:0|max:1000000',
        ]);
        $data['is_active'] = true;

        Topping::create($data);

        return back()->with('success', 'Topping ditambahkan.');
    }

    public function update(Request $request, Topping $topping)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'price'     => 'required|integer|min:0|max:1000000',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $topping->update($data);

        return back()->with('success', 'Topping diperbarui.');
    }

    public function destroy(Topping $topping)
    {
        $topping->delete();

        return back()->with('success', 'Topping dihapus.');
    }
}
