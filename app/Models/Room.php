<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    public const TYPES = ['Standard', 'Deluxe', 'Suite', 'Family', 'Villa'];

    public const STATUSES = [
        'available'   => 'Tersedia',
        'maintenance' => 'Perbaikan',
    ];

    protected $fillable = ['room_number', 'type', 'capacity', 'price_per_night', 'status', 'description'];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
