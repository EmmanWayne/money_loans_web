<?php

namespace App\Observers;  // Asegúrate que este sea el namespace correcto

use App\Models\Loan;
use App\Services\LoanCalculatorService;

class LoanObserver
{
    protected $calculator;

    public function __construct(LoanCalculatorService $calculator)
    {
        $this->calculator = $calculator;
    }

    public function created(Loan $loan): void
    {
        if ($loan->status === 'APPROVED') {
            $this->generatePaymentSchedule($loan);
        }
    }

    public function updated(Loan $loan): void
    {
        if ($loan->status === 'APPROVED' && $loan->wasChanged('status')) {
            $this->generatePaymentSchedule($loan);
        }
    }

    protected function generatePaymentSchedule(Loan $loan): void
    {
        $schedules = $this->calculator->calculateLoanSchedule($loan);
        
        foreach ($schedules as $schedule) {
            $loan->schedules()->create($schedule);
        }
    }
}