<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'identification_type',
        'identification_number',
        'employment_status',
        'monthly_income',
    ];

    protected $casts = [
        'monthly_income' => 'decimal:2',
    ];

    public static $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:clients',
        'phone' => 'required|string|max:20',
        'identification_number' => 'required|string|unique:clients',
        'monthly_income' => 'required|numeric|min:0',
    ];

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function getActiveLoansTotalAttribute(): float
    {
        return $this->loans()
            ->whereIn('status', ['APPROVED', 'ACTIVE'])
            ->sum('amount');
    }

    public function hasActiveLoans(): bool
    {
        return $this->loans()
            ->whereIn('status', ['APPROVED', 'ACTIVE'])
            ->exists();
    }

    public function getLoanHistoryAttribute()
    {
        return $this->loans()
            ->whereIn('status', ['COMPLETED', 'DEFAULTED'])
            ->get();
    }
}
