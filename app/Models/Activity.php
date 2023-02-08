<?php

namespace App\Models;

use App\Traits\ScopeFilterByColumn;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use ScopeFilterByColumn;

    protected $fillable = [
        'user_id',
        'model',
        'color',
        'bold',
        'text',
    ];

    public function user() {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function searchableColumns(): array
    {
        return  [
            'user_id' => ['condition' => '=']
        ];
    }
}
