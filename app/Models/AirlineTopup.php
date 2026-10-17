<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AirlineTopup extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Relationships
     */
    public function airline()
    {
        return $this->belongsTo(Airlines::class, 'airline_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
