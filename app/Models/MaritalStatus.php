<?php

namespace App\Models;

use App\Traits\ScopeFilterByColumn;
use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model
{
    use ScopeFilterByColumn;

    protected $table = "marital_status";

    protected $fillable = [
        'name',
        'active'
    ];

    public function searchableColumns(): array
    {
        return  [
            'active' => ['condition' => '='],
            'search' => function ($query, $value) {
                $words = explode(" ", $value);

                foreach ($words as $word) {
                    $query->where(function ($querySearch) use ($word) {
                        if ($word != '') {
                            $querySearch->where('name', 'like', '%' . $word . '%');
                        }
                    });
                }

                return $query;
            }
        ];
    }
}
