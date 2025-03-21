<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource\RelationManagers;
use App\Models\Loan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    ->relationship('client', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0),
                Forms\Components\TextInput::make('interest_rate')
                    ->required()
                    ->numeric()
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\TextInput::make('term_months')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(60),
                Forms\Components\Select::make('payment_frequency')
                    ->required()
                    ->options([
                        'WEEKLY' => 'Semanal',
                        'BIWEEKLY' => 'Quincenal',
                        'MONTHLY' => 'Mensual',
                    ]),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'PENDING' => 'Pendiente',
                        'APPROVED' => 'Aprobado',
                        'REJECTED' => 'Rechazado',
                        'ACTIVE' => 'Activo',
                        'COMPLETED' => 'Completado',
                        'DEFAULTED' => 'En mora',
                    ])
                    ->default('PENDING'),
                Forms\Components\Textarea::make('rejection_reason')
                    ->visible(fn (Forms\Get $get) => $get('status') === 'REJECTED')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('interest_rate')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('term_months')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'PENDING' => 'warning',
                        'APPROVED' => 'success',
                        'REJECTED' => 'danger',
                        'ACTIVE' => 'primary',
                        'COMPLETED' => 'success',
                        'DEFAULTED' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
