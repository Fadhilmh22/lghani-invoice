<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel_voucher_guest extends Model
{
    protected $fillable = ['hotel_voucher_id', 'hotel_voucher_room_id', 'voucher_id','room_id', 'guest_gender', 'guest_type', 'guest_first_name', 'guest_last_name', 'guest_age'];
}
