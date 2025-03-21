<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('loan_id')
                    ->relationship('loan', 'id')
                    ->label('Préstamo')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn($state, callable $set) => $set('payment_schedule_id', null)),
                Forms\Components\Select::make('payment_schedule_id')
                    ->relationship('schedule', 'id')
                    ->label('Cuota')
                    ->required()
                    ->options(function (callable $get) {
                        $loanId = $get('loan_id');
                        if (!$loanId) return [];

                        return \App\Models\PaymentSchedule::where('loan_id', $loanId)
                            ->where('status', '!=', 'PAID')
                            ->get()
                            ->mapWithKeys(function ($schedule) {
                                return [$schedule->id => "Cuota #{$schedule->installment_number} - Monto: {$schedule->remaining_amount}"];
                            });
                    }),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\Select::make('payment_method')
                    ->required()
                    ->options([
                        'CASH' => 'Efectivo',
                        'TRANSFER' => 'Transferencia',
                        'CARD' => 'Tarjeta',
                        'OTHER' => 'Otro',
                    ]),
                Forms\Components\TextInput::make('reference_number')
                    ->label('Número de referencia'),
                Forms\Components\DateTimePicker::make('payment_date')
                    ->required()
                    ->default(now()),
                Forms\Components\Textarea::make('notes')
                    ->label('Notas')
                    ->maxLength(255),
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
                Tables\Columns\TextColumn::make('schedule.installment_number')
                    ->label('# Cuota')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Método de pago')
                    ->badge(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Fecha de pago')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'CASH' => 'Efectivo',
                        'TRANSFER' => 'Transferencia',
                        'CARD' => 'Tarjeta',
                        'OTHER' => 'Otro',
                    ]),
            ])
            ->actions([
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'view' => Pages\ViewPayment::route('/{record}'),
        ];
    }
}
