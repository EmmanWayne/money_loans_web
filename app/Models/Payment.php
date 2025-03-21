<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'payment_schedule_id',
        'amount',
        'payment_method',
        'reference_number',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(PaymentSchedule::class, 'payment_schedule_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if ($payment->amount > $payment->schedule->remaining_amount) {
                throw new \Exception('El monto del pago excede el saldo pendiente.');
            }
            
            DB::transaction(function () use ($payment) {
                $schedule = $payment->schedule;
                $schedule->paid_amount += $payment->amount;
                $schedule->remaining_amount -= $payment->amount;
                $schedule->status = $schedule->remaining_amount > 0 ? 'PARTIAL' : 'PAID';
                $schedule->save();
            });
        });
    }
}
