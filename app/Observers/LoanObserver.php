<?php

namespace App\Observers;

use App\Models\Loan;
use Carbon\Carbon;

class LoanObserver
{
    public function updated(Loan $loan)
    {
        // Si el préstamo fue aprobado, generar calendario de pagos
        if ($loan->isDirty('status') && $loan->status === 'APPROVED') {
            $this->generatePaymentSchedule($loan);
        }
    }

    private function generatePaymentSchedule(Loan $loan)
    {
        $startDate = Carbon::now();
        $totalInterest = $loan->total_amount - $loan->amount;
        
        // Calcular el número de pagos según la frecuencia
        $numberOfPayments = match ($loan->payment_frequency) {
            'WEEKLY' => $loan->term_months * 4,
            'BIWEEKLY' => $loan->term_months * 2,
            'MONTHLY' => $loan->term_months,
        };

        // Calcular montos por cuota
        $installmentAmount = $loan->monthly_payment;
        $principalPerInstallment = $loan->amount / $numberOfPayments;
        $interestPerInstallment = $totalInterest / $numberOfPayments;

        // Generar cada cuota
        for ($i = 1; $i <= $numberOfPayments; $i++) {
            // Calcular fecha de vencimiento según frecuencia
            $dueDate = match ($loan->payment_frequency) {
                'WEEKLY' => $startDate->copy()->addWeeks($i),
                'BIWEEKLY' => $startDate->copy()->addWeeks($i * 2),
                'MONTHLY' => $startDate->copy()->addMonths($i),
            };

            $loan->schedules()->create([
                'installment_number' => $i,
                'due_date' => $dueDate,
                'amount' => $installmentAmount,
                'principal_amount' => $principalPerInstallment,
                'interest_amount' => $interestPerInstallment,
                'remaining_amount' => $installmentAmount,
            ]);
        }

        // Actualizar estado del préstamo a ACTIVE
        $loan->update(['status' => 'ACTIVE']);
    }
}