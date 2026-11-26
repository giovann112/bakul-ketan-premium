<?php

namespace App\Http\Controllers;

use App\Support\StockPool;
use Illuminate\Http\Request;

class LocationSwitchController extends Controller
{
    public function switch(Request $request)
    {
        $data = $request->validate(['location_id' => 'required|exists:locations,id']);
        StockPool::selectLocation((int) $data['location_id']);

        return back()->with('success', 'Lokasi pick up diperbarui.');
    }
}
