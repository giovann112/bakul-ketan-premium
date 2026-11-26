<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id', 'name', 'slug', 'description', 'price', 'image', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public const PALETTE = [
        ['#6A1B9A', '#ffffff'],
        ['#FDB813', '#2A0B47'],
        ['#C2185B', '#ffffff'],
        ['#2A0B47', '#FDB813'],
        ['#4A148C', '#ffffff'],
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getPanelAttribute(): array
    {
        return self::PALETTE[$this->id % count(self::PALETTE)];
    }

    public function getFlavorAttribute(): string
    {
        return trim(preg_replace('/^ketan\s+/i', '', $this->name));
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }

    public function getPriceRpAttribute(): string
    {
        return 'Rp '.number_format($this->price, 0, ',', '.');
    }
}
