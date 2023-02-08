<?php

namespace App\Models;

use App\Traits\ScopeFilterByColumn;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use ScopeFilterByColumn;

    protected $fillable = [
        'ruc',
        'name',
        'reliability',
        'active'
    ];

    public function fees_ranges() {
        return $this->hasMany(FeesRange::class);
    }

    public function searchableColumns(): array
    {
        return  [
            'active' => ['condition' => '='],
            'search' => function ($query, $value) {
                $words = explode(" ", $value);

                foreach ($words as $word) {
                    $query->where(function ($querySearch) use ($word) {
                        if ($word != '') {
                            $querySearch->where('name', 'like', '%' . $word . '%')
                            ->orWhere('ruc', 'like', '%' . $word . '%');
                        }
                    });
                }

                return $query;
            }
        ];
    }
}
