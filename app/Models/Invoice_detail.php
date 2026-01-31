<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice_detail extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'invoice_id','ticket_id', 'genre', 'name', 'booking_code', 
        'airline_id', 'airlines_no', 'class', 'ticket_no', 
        'route', 'depart_date', 'return_date', 'pax_paid', 
        'price', 'discount', 'nta', 'profit'
    ];

    // === TAMBAHKAN INI (Relasi ke Invoice Induk) ===
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    // === TAMBAHKAN INI (Relasi ke Maskapai) ===
    public function airline()
    {
        // Pastikan nama modelnya 'Airlines' sesuai file abang
        return $this->belongsTo(Airlines::class, 'airline_id');
    }
}