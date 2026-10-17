<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel_rate extends Model
{
    use SoftDeletes;

    protected $fillable = ['hotel_id', 'room_code', 'room_name', 'room_type', 'bed_type', 'weekday_price', 'weekday_nta', 'weekend_price', 'weekend_nta'];
    
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}