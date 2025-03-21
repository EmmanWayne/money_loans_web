<?php

namespace App\Services;

use App\Models\Loan;
use Carbon\Carbon;

class LoanCalculatorService
{
    public function calculateLoanSchedule(Loan $loan): array
    {
        $amount = $loan->amount;
        $rate = $loan->interest_rate / 100 / 12; // Tasa mensual
        $term = $loan->term_months;

        // Cálculo de pago mensual (Fórmula PMT)
        $monthlyPayment = $amount * ($rate * pow(1 + $rate, $term)) / (pow(1 + $rate, $term) - 1);
        
        $schedules = [];
        $balance = $amount;
        $startDate = Carbon::now();

        for ($i = 1; $i <= $term; $i++) {
            $interest = $balance * $rate;
            $principal = $monthlyPayment - $interest;
            $balance -= $principal;

            // Ajustar fecha según frecuencia de pago
            $dueDate = match($loan->payment_frequency) {
                'WEEKLY' => $startDate->copy()->addWeeks($i),
                'BIWEEKLY' => $startDate->copy()->addWeeks($i * 2),
                'MONTHLY' => $startDate->copy()->addMonths($i),
            };

            $schedules[] = [
                'installment_number' => $i,
                'due_date' => $dueDate,
                'amount' => round($monthlyPayment, 2),
                'principal_amount' => round($principal, 2),
                'interest_amount' => round($interest, 2),
                'status' => 'PENDING',
                'paid_amount' => 0,
                'remaining_amount' => round($monthlyPayment, 2),
            ];
        }

        return $schedules;
    }
}