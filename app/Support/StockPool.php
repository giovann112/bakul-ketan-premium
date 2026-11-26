<?php

namespace App\Support;

use App\Models\Location;
use Illuminate\Support\Collection;

class StockPool
{
    public static function locationsWithTodayStock(): Collection
    {
        return Location::active()->orderBy('id')->get()->map(function (Location $loc) {
            $loc->setRelation('today_stock', $loc->todayStock());

            return $loc;
        });
    }

    public static function selectedLocationId(): ?int
    {
        $id = session('selected_location_id');
        if ($id && Location::active()->whereKey($id)->exists()) {
            return (int) $id;
        }

        $first = Location::active()->orderBy('id')->first();
        if ($first) {
            session(['selected_location_id' => $first->id]);

            return $first->id;
        }

        return null;
    }

    public static function selectLocation(int $locationId): void
    {
        if (Location::active()->whereKey($locationId)->exists()) {
            session(['selected_location_id' => $locationId]);
        }
    }

    public static function selectedLocation(): ?Location
    {
        $id = self::selectedLocationId();
        if (! $id) {
            return null;
        }

        $loc = Location::find($id);
        if ($loc) {
            $loc->setRelation('today_stock', $loc->todayStock());
        }

        return $loc;
    }

    public static function remainingPortionsFor(?int $locationId = null): int
    {
        $id = $locationId ?? self::selectedLocationId();
        if (! $id) {
            return 0;
        }

        $loc = Location::find($id);

        return $loc ? $loc->todayStock()->remaining_portions : 0;
    }
}
