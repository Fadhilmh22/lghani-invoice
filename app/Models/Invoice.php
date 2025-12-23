<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function airlines()
    {
        return $this->belongsTo(Airlines::class);
    }

    public function detail()
    {
        return $this->hasMany(Invoice_detail::class);
    }
}
