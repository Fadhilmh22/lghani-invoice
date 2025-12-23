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
        return $this->belongsTo(Hotel_voucher_room::class);
    }

    public function hotelguest()
    {
        return $this->hasMany(Hotel_voucher_guest::class);
    }
}
