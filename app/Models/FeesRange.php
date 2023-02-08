<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeesRange extends Model
{
    protected $table = "fees_ranges";

    protected $fillable = [
        'business_id',
        'min',
        'max',
        'fee'
    ];
}
