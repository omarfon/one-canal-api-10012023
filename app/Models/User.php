<?php

namespace App\Models;

use App\Models\Account;
use App\Models\Business;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\ScopeFilterByColumn;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, ScopeFilterByColumn;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'names',
        'surnames',
        'email',
        'phone',
        'document_type',
        'document_number',
        'active',
        'valid',
        'role',
        'attemps',
        'business_id',
        'password',
        'salary',
        'salary_view',
        "business_job",
        "address",
        "marital_status_id",
        'salary_updated'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function marital_status()
    {
        return $this->belongsTo(MaritalStatus::class, 'marital_status_id', 'id');
    }

    public function searchableColumns(): array
    {
        return  [
            'document_type' => ['condition' => '='],
            'active' => ['condition' => '='],
            'valid' => ['condition' => '='],
            'business_id' => ['condition' => '='],
            'search' => function ($query, $value) {
                $words = explode(" ", $value);

                foreach ($words as $word) {
                    $query->where(function ($querySearch) use ($word) {
                        if ($word != '') {
                            $querySearch->where('names', 'like', '%' . $word . '%')
                            ->orWhere('surnames', 'like', '%' . $word . '%')
                            ->orWhere('email', 'like', '%' . $word . '%')
                            ->orWhere('document_number', 'like', '%' . $word . '%');
                        }
                    });
                }

                return $query;
            }
        ];
    }
}
