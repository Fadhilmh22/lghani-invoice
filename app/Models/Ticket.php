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
}