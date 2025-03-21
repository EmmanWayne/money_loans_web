<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentScheduleResource\Pages;
use App\Models\PaymentSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;

class PaymentScheduleResource extends Resource
{
    protected static ?string $model = PaymentSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    
    protected static ?string $navigationLabel = 'Calendario de Pagos';

    protected static ?string $modelLabel = 'Calendario de Pago';

    protected static ?string $pluralModelLabel = 'Calendario de Pagos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('loan_id')
                    ->relationship('loan', 'id')
                    ->label('Préstamo')
                    ->required(),
                Forms\Components\TextInput::make('installment_number')
                    ->label('Número de Cuota')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Fecha de Vencimiento')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->prefix('L'),
                Forms\Components\TextInput::make('remaining_amount')
                    ->label('Monto Pendiente')
                    ->required()
                    ->numeric()
                    ->prefix('L'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('loan.client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('installment_number')
                    ->label('# Cuota')
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
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'PARTIAL' => 'info',
                        'PAID' => 'success',
                        'LATE' => 'danger',
                    })
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'PENDING' => 'Pendiente',
                        'PARTIAL' => 'Parcial',
                        'PAID' => 'Pagado',
                        'LATE' => 'Atrasado',
                    ]),
                Filter::make('due_date')
                    ->label('Fecha de Vencimiento')
                    ->form([
                        Forms\Components\DatePicker::make('due_date_from')
                            ->label('Desde'),
                        Forms\Components\DatePicker::make('due_date_until')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['due_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('due_date', '>=', $date),
                            )
                            ->when(
                                $data['due_date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('due_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('register_payment')
                    ->label('Registrar Pago')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount')
                            ->label('Monto a Pagar')
                            ->required()
                            ->numeric()
                            ->prefix('L')
                            ->maxValue(function (PaymentSchedule $record): float {
                                return $record->remaining_amount;
                            }),
                        Forms\Components\Select::make('payment_method')
                            ->label('Método de Pago')
                            ->required()
                            ->options([
                                'CASH' => 'Efectivo',
                                'TRANSFER' => 'Transferencia',
                                'CARD' => 'Tarjeta',
                                'OTHER' => 'Otro',
                            ]),
                        Forms\Components\TextInput::make('reference_number')
                            ->label('Número de Referencia'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->maxLength(255),
                    ])
                    ->action(function (PaymentSchedule $record, array $data): void {
                        // Crear el pago
                        $record->payments()->create([
                            'loan_id' => $record->loan_id,
                            'amount' => $data['amount'],
                            'payment_method' => $data['payment_method'],
                            'reference_number' => $data['reference_number'] ?? null,
                            'payment_date' => now(),
                            'notes' => $data['notes'] ?? null,
                        ]);

                        // Actualizar el schedule
                        $record->paid_amount += $data['amount'];
                        $record->remaining_amount = $record->amount - $record->paid_amount;
                        $record->status = $record->remaining_amount <= 0 ? 'PAID' : 'PARTIAL';
                        $record->save();

                        // Verificar si el préstamo está pagado
                        if ($record->loan->schedules()->where('status', '!=', 'PAID')->count() === 0) {
                            $record->loan->update(['status' => 'COMPLETED']);
                        }
                    })
                    ->visible(fn (PaymentSchedule $record): bool => $record->status !== 'PAID'),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentSchedules::route('/'),
            'create' => Pages\CreatePaymentSchedule::route('/create'),
            'edit' => Pages\EditPaymentSchedule::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['loan.client'])
            ->orderBy('due_date');
    }
}