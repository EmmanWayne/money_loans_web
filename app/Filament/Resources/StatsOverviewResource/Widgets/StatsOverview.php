<?php

namespace App\Filament\Widgets;

use App\Models\Loan;
use App\Models\Client;
use App\Models\PaymentSchedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $activeLoanCount = Loan::where('status', 'ACTIVE')->count();
        $totalPortfolio = Loan::where('status', 'ACTIVE')->sum('total_amount');
        $pendingPayments = PaymentSchedule::whereDate('due_date', now())
            ->whereNotIn('status', ['PAID'])
            ->count();
        $defaultedLoans = Loan::where('status', 'DEFAULTED')->count();

        return [
            Stat::make('Préstamos Activos', $activeLoanCount)
                ->description('Total de préstamos en curso')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('success'),
            
            Stat::make('Pagos Pendientes Hoy', $pendingPayments)
                ->description('Cuotas que vencen hoy')
                ->icon('heroicon-o-clock')
                ->color('warning'),
            
            Stat::make('Préstamos en Mora', $defaultedLoans)
                ->description('Préstamos con pagos atrasados')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('danger'),
            
            Stat::make('Total Cartera', 'L. ' . number_format($totalPortfolio, 2))
                ->description('Monto total en préstamos activos')
                ->icon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }
}