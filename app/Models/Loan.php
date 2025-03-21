<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Loan extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'client_id',
        'amount',
        'total_amount',
        'monthly_payment',
        'interest_rate',
        'term_months',
        'payment_frequency',
        'status',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'monthly_payment' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($loan) {
            // Validar que el cliente no tenga préstamos activos
            if ($loan->client->hasActiveLoans()) {
                throw new \Exception('El cliente ya tiene préstamos activos.');
            }

            // Calcular montos
            $loan->calculateAmounts();
            
            // Establecer estado inicial
            $loan->status = 'PENDING';
        });

        static::created(function ($loan) {
            // Crear calendario de pagos
            $loan->createPaymentSchedule();
        });

        static::updating(function ($loan) {
            // Actualiza approved_at cuando el estado cambia a APPROVED
            if ($loan->isDirty('status') && $loan->status === 'APPROVED') {
                $loan->approved_at = now();
            }
        });
    }

    protected function calculateAmounts()
    {
        $this->total_amount = $this->amount + ($this->amount * ($this->interest_rate / 100));
        
        $divisor = match($this->payment_frequency) {
            'WEEKLY' => $this->term_months * 4,
            'BIWEEKLY' => $this->term_months * 2,
            'MONTHLY' => $this->term_months,
        };
        
        $this->monthly_payment = $this->total_amount / $divisor;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(PaymentSchedule::class);
    }

    public function getRemainingBalanceAttribute(): float
    {
        return $this->schedules()
            ->sum('remaining_amount');
    }

    public function getPaymentProgressAttribute(): float
    {
        $totalPaid = $this->schedules()->sum('paid_amount');
        return ($totalPaid / $this->total_amount) * 100;
    }

    public static function updateDefaultedLoans()
    {
        return self::query()
            ->where('status', 'ACTIVE')
            ->whereHas('schedules', function ($query) {
                $query->where('status', 'LATE');
            })
            ->update(['status' => 'DEFAULTED']);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['APPROVED', 'ACTIVE']);
    }

    public function scopeDefaulted($query)
    {
        return $query->where('status', 'DEFAULTED');
    }

    public function scopeDueThisMonth($query)
    {
        return $query->whereHas('schedules', function ($q) {
            $q->whereMonth('due_date', now()->month)
              ->whereYear('due_date', now()->year)
              ->where('status', '!=', 'PAID');
        });
    }
}
