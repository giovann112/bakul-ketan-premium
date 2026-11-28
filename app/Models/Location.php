<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'address', 'hours',
        'weekday_kg', 'weekend_kg', 'portions_per_kg', 'is_active',
    ];

    protected $casts = [
        'weekday_kg'      => 'float',
        'weekend_kg'      => 'float',
        'portions_per_kg' => 'integer',
        'is_active'       => 'boolean',
    ];

    public function stocks()
    {
        return $this->hasMany(DailyStock::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function todayStock(?CarbonInterface $date = null): DailyStock
{
    $date = ($date ?? now())->toDateString();

    $isWeekend = \Illuminate\Support\Carbon::parse($date)->isWeekend();

    $kg = $isWeekend ? $this->weekend_kg : $this->weekday_kg;

    $portions = (int) round($kg * $this->portions_per_kg);

    return DailyStock::firstOrCreate(
        [
            'location_id' => $this->id,
            'stock_date' => $date,
        ],
        [
            'kg_allocated' => $kg,
            'total_portions' => $portions,
            'remaining_portions' => $portions,
        ]
    );
}
    }
}
