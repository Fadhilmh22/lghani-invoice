<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = ['hotel_code','hotel_name','region','address','phone','fax'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function detail()
    {
        return $this->hasMany(Invoice_detail::class);
    }
}
