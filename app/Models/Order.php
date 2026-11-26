<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_code', 'location_id', 'pickup_location_name',
        'customer_name', 'phone', 'notes', 'total', 'status',
    ];

    public const STATUSES = [
        'pending'    => 'Menunggu Konfirmasi',
        'confirmed'  => 'Dikonfirmasi',
        'processing' => 'Diproses',
        'ready'      => 'Siap Diambil',
        'completed'  => 'Selesai',
        'cancelled'  => 'Dibatalkan',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getTotalRpAttribute(): string
    {
        return 'Rp '.number_format($this->total, 0, ',', '.');
    }

    public function getWaPhoneAttribute(): string
    {
        return str_starts_with($this->phone, '0') ? '62'.substr($this->phone, 1) : $this->phone;
    }

    public static function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        return str_starts_with($digits, '62') ? '0'.substr($digits, 2) : $digits;
    }
}
