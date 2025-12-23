<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel_rate extends Model
{
    protected $fillable = ['hotel_id', 'room_code', 'room_name', 'room_type', 'bed_type', 'weekday_price', 'weekday_nta', 'weekend_price', 'weekend_nta'];
    
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }
}