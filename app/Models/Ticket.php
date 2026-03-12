<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    // Baris ini WAJIB ada agar data mau tersimpan
protected $fillable = [
    'invoice_id', 'airline_id', 'booking_code', 'flight_out', 'flight_in', 'route_out', 'route_in',
    'dep_time_out', 'dep_time_in', 'arr_time_out', 'arr_time_in',
    'stop_flight_leg1_out', 'stop_flight_leg1_in',
    'stop_time_out', 'stop_time_in', 'stop_time_out_arrival', 'stop_time_out_depart', 
    'stop_time_in_arrival', 'stop_time_in_depart',
    'stop_flight_leg2_out', 'stop_flight_leg2_in',
    'stop_airline_out', 'stop_airline_in',
    'class', 'basic_fare', 'total_tax', 'fee', 'baggage_kg', 'baggage_price', 'free_baggage',
    'total_publish', 'total_profit', 'nta_total',
    'stop_out_depart_code', 'stop_out_arrival_code',
    'stop_in_depart_code', 'stop_in_arrival_code'
]; 

    protected $casts = [
        'nta_total' => 'integer',
    ];

    public function invoice() {
        return $this->belongsTo(Invoice::class);
    }
    public function airline() {
        return $this->belongsTo(Airlines::class, 'airline_id');
    }
    public function passengers() { 
        return $this->hasMany(Passenger::class); 
    }
    public function details()
    {
        return $this->hasMany(Invoice_detail::class, 'ticket_id', 'id');
    }
}