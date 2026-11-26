<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('id')->get()->map(function (Location $loc) {
            $loc->setRelation('today_stock', $loc->todayStock());

            return $loc;
        });

        return view('admin.stocks.index', [
            'locations' => $locations,
            'isWeekend' => now()->isWeekend(),
        ]);
    }

    public function updateLocation(Request $request, Location $location)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'address'         => 'nullable|string|max:255',
            'hours'           => 'nullable|string|max:100',
            'weekday_kg'      => 'required|numeric|min:0.5|max:200',
            'weekend_kg'      => 'required|numeric|min:0.5|max:200',
            'portions_per_kg' => 'required|integer|min:1|max:100',
            'is_active'       => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $location->update($data);

        // Otomatis perbarui kuota stok hari ini di database
        $today = $location->todayStock();
        $usedPortions = max(0, $today->total_portions - $today->remaining_portions);
        $newKg = now()->isWeekend() ? $location->weekend_kg : $location->weekday_kg;
        $newTotalPortions = (int) round($newKg * $location->portions_per_kg);

        $today->update([
            'kg_allocated'       => $newKg,
            'total_portions'     => $newTotalPortions,
            'remaining_portions' => max(0, $newTotalPortions - $usedPortions),
        ]);

        return back()->with('success', "Pengaturan & kuota stok hari ini untuk {$location->name} otomatis diperbarui ke database.");
    }

    public function updateToday(Request $request, Location $location)
    {
        $data = $request->validate([
            'kg_allocated'       => 'required|numeric|min:0|max:200',
            'remaining_portions' => 'required|integer|min:0|max:5000',
        ]);

        $today = $location->todayStock();
        $totalPortions = (int) round($data['kg_allocated'] * $location->portions_per_kg);

        $today->update([
            'kg_allocated'       => $data['kg_allocated'],
            'total_portions'     => max($totalPortions, (int) $data['remaining_portions']),
            'remaining_portions' => (int) $data['remaining_portions'],
        ]);

        return back()->with('success', "Stok ketan hari ini untuk {$location->name} diperbarui.");
    }

    public function resetToday(Location $location)
    {
        $today = $location->todayStock();
        $kg = now()->isWeekend() ? $location->weekend_kg : $location->weekday_kg;
        $portions = (int) round($kg * $location->portions_per_kg);

        $today->update([
            'kg_allocated'       => $kg,
            'total_portions'     => $portions,
            'remaining_portions' => $portions,
        ]);

        return back()->with('success', "Stok {$location->name} di-reset ke kuota standar hari ini ({$kg} kg = {$portions} porsi).");
    }
}
