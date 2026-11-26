<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'base_price', 'price',
        'toppings', 'unit_price', 'qty', 'subtotal',
    ];

    protected $casts = [
        'toppings' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getToppingsSummaryAttribute(): string
    {
        if (empty($this->toppings)) {
            return '';
        }

        return collect($this->toppings)->pluck('name')->implode(', ');
    }
}
