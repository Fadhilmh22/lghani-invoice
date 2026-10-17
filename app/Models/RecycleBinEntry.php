<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecycleBinEntry extends Model
{
    protected $fillable = [
        'resource_type',
        'record_id',
        'label',
        'description',
        'related_records',
        'deleted_by',
        'deleted_by_name',
        'deleted_by_email',
    ];

    protected $casts = [
        'related_records' => 'array',
    ];
}
