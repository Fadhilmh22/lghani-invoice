<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirlineTopup extends Model
{
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
