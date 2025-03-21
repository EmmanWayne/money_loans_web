<?php

namespace App\Console\Commands;

use App\Models\PaymentSchedule;
use App\Models\Loan;
use Illuminate\Console\Command;

class UpdateLatePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:check-late';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar y actualizar pagos atrasados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $updatedSchedules = PaymentSchedule::query()
            ->whereDate('due_date', '<', now())
            ->whereNotIn('status', ['PAID'])
            ->update(['status' => 'LATE']);

        $updatedLoans = Loan::updateDefaultedLoans();

        $this->info("Se actualizaron {$updatedSchedules} pagos atrasados.");
        $this->info("Se actualizaron {$updatedLoans} préstamos a estado de mora.");

        return Command::SUCCESS;
    }
}
