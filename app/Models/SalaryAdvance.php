<?php

namespace App\Models;

use App\Traits\ScopeFilterByColumn;
use Illuminate\Database\Eloquent\Model;
class SalaryAdvance extends Model
{
    use ScopeFilterByColumn;

    protected $fillable = [
        'account_id',
        'amount',
        'transfer_amount',
        'fees_amount',
        'user_id',
        'status',
        'period_name',
        'logs',
        'reason_id'
    ];

    protected $casts = [
        'logs' => 'array',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function account() {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function salary_advance_reason() {
        return $this->belongsTo(Reason::class, 'salary_advance_reason_id', 'id');
    }

    public function reason() {
        return $this->belongsTo(Reason::class, 'reason_id', 'id');
    }

    public function searchableColumns(): array
    {
        return  [
            'status' => ['condition' => '='],
            'date' => function ($query, $value) {
                return $query->whereDate('created_at', $value);
            },
            'start_date' => function ($query, $value) {
                return $query->whereDate('created_at', '>=', $value);
            },
            'end_date' => function ($query, $value) {
                return $query->whereDate('created_at', '<=', $value);
            },
            'search' => function ($query, $value) {
                $words = explode(" ", $value);

                foreach ($words as $word) {
                    $query->where(function ($querySearch) use ($word) {
                        if ($word != '') {
                            $querySearch->where('amount', 'like', '%' . $word . '%')
                            ->orWhere('ruc', 'transfer_amount', '%' . $word . '%');
                        }
                    });
                }

                return $query;
            },
            'reason_id' => ['condition' => '='],
            'business_id' => ['condition' => '=', 'relation' => 'user', 'column' => 'business_id'],
            'bank_id' => ['condition' => '=', 'relation' => 'account', 'column' => 'bank_id'],
        ];
    }
}
