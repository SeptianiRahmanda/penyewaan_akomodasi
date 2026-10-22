<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    public const METHODS = [
        'cash'     => 'Tunai',
        'transfer' => 'Transfer Bank',
        'qris'     => 'QRIS',
        'card'     => 'Kartu Debit/Kredit',
    ];

    /** Saran nama biaya lain (muncul sebagai autocomplete di form). */
    public const FEE_SUGGESTIONS = [
        'Sarapan', 'Extra bed', 'Laundry', 'Antar-jemput bandara',
        'Sewa motor', 'Late check-out', 'Pajak & service', 'Denda kerusakan',
    ];

    protected $fillable = ['invoice_number', 'booking_id', 'payment_date', 'method', 'amount_paid', 'notes'];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(PaymentFee::class);
    }

    /** Total sewa kamar (dari booking). */
    public function getRoomTotalAttribute(): int
    {
        return (int) $this->booking->room_total;
    }

    /** Total semua biaya lain-lain. */
    public function getFeesTotalAttribute(): int
    {
        return (int) $this->fees->sum('amount');
    }

    public function getGrandTotalAttribute(): int
    {
        return $this->room_total + $this->fees_total;
    }

    /** Sisa tagihan. */
    public function getBalanceAttribute(): int
    {
        return max(0, $this->grand_total - (int) $this->amount_paid);
    }

    /** lunas | sebagian | belum */
    public function getStatusAttribute(): string
    {
        if ($this->amount_paid >= $this->grand_total && $this->grand_total > 0) {
            return 'lunas';
        }

        return $this->amount_paid > 0 ? 'sebagian' : 'belum';
    }

    public function getStatusLabelAttribute(): string
    {
        return ['lunas' => 'Lunas', 'sebagian' => 'Dibayar Sebagian', 'belum' => 'Belum Dibayar'][$this->status];
    }

    public function getStatusColorAttribute(): string
    {
        return ['lunas' => 'success', 'sebagian' => 'warning', 'belum' => 'danger'][$this->status];
    }

    public function getMethodLabelAttribute(): string
    {
        return self::METHODS[$this->method] ?? $this->method;
    }

    /** Contoh: INV-202610-0001 */
    public static function nextInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';
        $last = static::where('invoice_number', 'like', $prefix . '%')->orderByDesc('invoice_number')->value('invoice_number');
        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $seq, 4, '0', STR_PAD_LEFT);
    }
}
