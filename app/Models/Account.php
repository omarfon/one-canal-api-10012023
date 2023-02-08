<?php

namespace App\Models;

use App\Traits\ScopeFilterByColumn;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use ScopeFilterByColumn;

    protected $fillable = [
        'user_id',
        'type',
        'bank_id',
        'number',
        'cci',
        'active',
        'confirmed'
    ];

    protected $casts = [
        "active" => 'boolean',
        "confirmed" => 'boolean'
    ];

    public function bank() {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function searchableColumns(): array
    {
        return  [
            'active' => ['condition' => '='],
            'bank_id' => ['condition' => '='],
            'search' => function ($query, $value) {
                return $query->where('number', 'like', '%' . $value . '%');
            }
        ];
    }
}
