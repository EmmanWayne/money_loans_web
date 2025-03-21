<?php

namespace App\Filament\Widgets;

use App\Models\PaymentSchedule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatePayments extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PaymentSchedule::query()
                    ->where('status', 'LATE')
                    ->latest('due_date')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('loan.client.name')
                    ->label('Cliente')
                    ->searchable(),
                Tables\Columns\TextColumn::make('installment_number')
                    ->label('Cuota #')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Vencimiento')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('HNL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_amount')
                    ->label('Pendiente')
                    ->money('HNL')
                    ->sortable(),
            ]);
    }
}