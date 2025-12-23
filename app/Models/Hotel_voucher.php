<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel_voucher extends Model
{
    // protected $fillable = ['booking_id','nationality', 'attention', 'issued_date', 'booker', 'agency_reference_number'];
    protected $fillable = ['voucher_no', 'booking_id', 'booking_no', 'currency', 'booker', 'booker_agent', 'no_booker_agent', 'nationality', 'attention', 'hotel_id', 'check_in', 'check_out', 'confirm_by', 'remark', 'rsvn_and_payment_by', 'count_type_room', 'issued_date', 
    ];

    public function hoteldetail()
    {
        return $this->belongsTo(Hotel_voucher_room::class);
    }

    public function hotelguest()
    {
        return $this->hasMany(Hotel_voucher_guest::class);
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
