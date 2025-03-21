<?php

namespace App\Filament\Resources\PaymentScheduleResource\Actions;

use Filament\Tables\Actions\Action as TableAction;
use Filament\Forms;

class RegisterPaymentAction extends TableAction
{
    public static function getDefaultName(): ?string
    {
        return 'registerPayment';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Registrar Pago')
            ->icon('heroicon-o-banknotes')
            ->form([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->label('Monto'),
                Forms\Components\Select::make('payment_method')
                    ->required()
                    ->options([
                        'CASH' => 'Efectivo',
                        'TRANSFER' => 'Transferencia',
                        'CARD' => 'Tarjeta',
                        'OTHER' => 'Otro',
                    ])
                    ->label('Método de pago'),
                Forms\Components\TextInput::make('reference_number')
                    ->label('Número de referencia'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->maxLength(255),
            ])
            ->action(function (array $data, $record): void {
                $payment = $record->payments()->create([
                    'loan_id' => $record->loan_id,
                    'payment_schedule_id' => $record->id,
                    'amount' => $data['amount'],
                    'payment_method' => $data['payment_method'],
                    'reference_number' => $data['reference_number'] ?? null,
                    'payment_date' => now(),
                    'notes' => $data['notes'] ?? null,
                ]);

                $record->paid_amount += $data['amount'];
                $record->remaining_amount = $record->amount - $record->paid_amount;
                $record->status = $record->remaining_amount <= 0 ? 'PAID' : 'PARTIAL';
                $record->save();
            });
    }
}