<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    // Baris ini WAJIB ada agar data mau tersimpan
    protected $guarded = []; 

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