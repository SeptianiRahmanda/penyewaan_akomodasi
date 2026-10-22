<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentFee extends Model
{
    protected $fillable = ['payment_id', 'description', 'amount'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
