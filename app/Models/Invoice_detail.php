<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice_detail extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_id', 'genre', 'name', 'booking_code', 'airline_id', 'airlines_no', 'class', 'ticket_no', 'route', 'depart_date', 'return_date', 'pax_paid', 'price', 'discount', 'nta', 'profit'];
}
