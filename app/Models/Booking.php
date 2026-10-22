<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    public const STATUSES = [
        'pending'     => 'Menunggu',
        'confirmed'   => 'Dikonfirmasi',
        'checked_in'  => 'Check-in',
        'checked_out' => 'Check-out',
        'cancelled'   => 'Dibatalkan',
    ];

    public const STATUS_COLORS = [
        'pending'     => 'warning',
        'confirmed'   => 'primary',
        'checked_in'  => 'success',
        'checked_out' => 'secondary',
        'cancelled'   => 'danger',
    ];

    protected $fillable = [
        'code', 'customer_id', 'room_id', 'check_in', 'check_out', 'guests',
        'nights', 'price_per_night', 'room_total', 'status', 'notes',
    ];

    protected $casts = [
        'check_in'  => 'date',
        'check_out' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    /** Booking lain di kamar yang sama dengan tanggal bertabrakan (booking batal diabaikan). */
    public function scopeOverlapping(Builder $query, int $roomId, string $checkIn, string $checkOut, ?int $ignoreId = null): Builder
    {
        return $query->where('room_id', $roomId)
            ->where('status', '!=', 'cancelled')
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId));
    }

    /** Contoh: BK-202610-0001 */
    public static function nextCode(): string
    {
        $prefix = 'BK-' . now()->format('Ym') . '-';
        $last = static::where('code', 'like', $prefix . '%')->orderByDesc('code')->value('code');
        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
