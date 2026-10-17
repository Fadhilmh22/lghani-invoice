<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Hotel_voucher_guest extends Model
{
    use SoftDeletes;

    protected $fillable = ['hotel_voucher_id', 'hotel_voucher_room_id', 'voucher_id','room_id', 'guest_gender', 'guest_type', 'guest_first_name', 'guest_last_name', 'guest_age'];
}
