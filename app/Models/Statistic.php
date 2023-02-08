<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    protected $fillable = [
        'type',
        'date',
        'value',
        'tag'
    ];
}
