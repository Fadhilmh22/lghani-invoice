<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel_voucher_room extends Model
{
    protected $fillable = ['hotel_voucher_id','voucher_no', 'hotel_id', 'room_id', 'room_no', 'meal_type', 'check_in', 'check_out', 'booking_status', 'use_allotment', 'no_of_extrabed', 'remark'];

    public function hotelguest()
    {
        return $this->hasMany(Hotel_voucher_guest::class);
    }

    public function hotelVoucher()
    {
        return $this->belongsTo(Hotel_voucher::class, 'hotel_voucher_id');
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function room()
    {
        return $this->belongsTo(Hotel_rate::class, 'room_id');
    }
}
