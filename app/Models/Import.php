<?php

namespace App\Models;

use App\Events\ImportCreated;
use App\Events\ImportUpdated;
use App\Traits\ScopeFilterByColumn;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    use ScopeFilterByColumn;

    protected $fillable = [
        'model',
        'code',
        'rows',
        'advance',
        'admin_id',
        'status'
    ];

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    public function searchableColumns(): array
    {
        return  [
            'status',
            'date' => function ($query, $value) {
                return $query->whereDate('created_at', $value);
            }
        ];
    }
}
