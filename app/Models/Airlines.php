<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airlines extends Model
{
    use SoftDeletes;

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
