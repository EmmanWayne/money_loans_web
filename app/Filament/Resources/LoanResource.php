<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->label('Cliente')
                    ->relationship('client', 'name')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Monto')
                    ->required()
                    ->numeric()
                    ->prefix('L'),
                Forms\Components\TextInput::make('interest_rate')
                    ->label('Tasa de Interés')
                    ->required()
                    ->numeric()
                    ->suffix('%'),
                Forms\Components\TextInput::make('term_months')
                    ->label('Plazo (Meses)')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('payment_frequency')
                    ->label('Frecuencia de Pago')
                    ->options([
                        'WEEKLY' => 'Semanal',
                        'BIWEEKLY' => 'Quincenal',
                        'MONTHLY' => 'Mensual',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Estado')
                    ->options([
                        'PENDING' => 'Pendiente',
                        'APPROVED' => 'Aprobado',
                        'REJECTED' => 'Rechazado',
                        'ACTIVE' => 'Activo',
                        'COMPLETED' => 'Completado',
                        'DEFAULTED' => 'En mora',
                    ])
                    ->default('PENDING')
                    ->required(),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->money('HNL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('interest_rate')
                    ->label('Interés')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('term_months')
                    ->label('Plazo')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto Principal')
                    ->money('HNL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Monto Total')
                    ->money('HNL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'APPROVED' => 'success',
                        'REJECTED' => 'danger',
                        'ACTIVE' => 'primary',
                        'COMPLETED' => 'success',
                        'DEFAULTED' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'PENDING' => 'Pendiente',
                        'APPROVED' => 'Aprobado',
                        'REJECTED' => 'Rechazado',
                        'ACTIVE' => 'Activo',
                        'COMPLETED' => 'Completado',
                        'DEFAULTED' => 'En mora',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListLoans::route('/'),
            'create' => Pages\CreateLoan::route('/create'),
            'edit' => Pages\EditLoan::route('/{record}/edit'),
        ];
    }
}
