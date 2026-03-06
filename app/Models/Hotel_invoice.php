<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel_invoice extends Model
{
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function hoteldetail()
    {
        // Hotel_voucher_room is linked via Hotel_voucher where booking_id = hotel_invoice.id
        return $this->hasOne(Hotel_voucher::class, 'booking_id');
    }

    public function hotelguest()
    {
        return $this->hasMany(Hotel_voucher_guest::class);
    }
}
