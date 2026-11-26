<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topping extends Model
{
    protected $fillable = ['name', 'price', 'is_active'];

    protected $casts = [
        'price'     => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getPriceRpAttribute(): string
    {
        return '+Rp '.number_format($this->price, 0, ',', '.');
    }
}
