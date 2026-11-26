<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyStock extends Model
{
    protected $fillable = [
        'location_id', 'stock_date', 'kg_allocated', 'total_portions', 'remaining_portions',
    ];

    protected $casts = [
        'stock_date'         => 'date',
        'kg_allocated'       => 'float',
        'total_portions'     => 'integer',
        'remaining_portions' => 'integer',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function getRemainingKgAttribute(): float
    {
        $ppk = max(1, $this->location->portions_per_kg ?? 15);

        return round($this->remaining_portions / $ppk, 2);
    }
}
