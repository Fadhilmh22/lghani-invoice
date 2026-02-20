<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airlines extends Model
{
    // allow mass assignment on these fields
    protected $guarded = [];

    protected $casts = [
        'balance' => 'integer',
    ];

    /**
     * helper for formatted balance (IDR)
     */
    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance ?? 0);
    }
}
