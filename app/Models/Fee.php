<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $fillable = [
        'type',
        'value',
        'tag',
        'active'
    ];
}
