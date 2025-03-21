<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\PaymentSchedule;

class PaymentObserver
{
    public function created(Payment $payment)
    {
        $schedule = $payment->schedule;
        
        // Actualizar montos pagados y restantes
        $schedule->paid_amount += $payment->amount;
        $schedule->remaining_amount = $schedule->amount - $schedule->paid_amount;

        // Actualizar estado de la cuota
        if ($schedule->remaining_amount <= 0) {
            $schedule->status = 'PAID';
        } elseif ($schedule->paid_amount > 0) {
            $schedule->status = 'PARTIAL';
        }

        $schedule->save();

        // Verificar si el préstamo está completamente pagado
        $this->checkLoanCompletion($payment->loan);
    }

    private function checkLoanCompletion($loan)
    {
        $pendingSchedules = $loan->schedules()
            ->whereNotIn('status', ['PAID'])
            ->count();

        if ($pendingSchedules === 0) {
            $loan->update(['status' => 'COMPLETED']);
        }
    }
}