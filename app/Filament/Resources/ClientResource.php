<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Teléfono')
                    ->required()
                    ->tel()
                    ->maxLength(255),
                Forms\Components\Textarea::make('address')
                    ->label('Dirección')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\Select::make('identification_type')
                    ->label('Tipo de Identificación')
                    ->required()
                    ->options([
                        'DNI' => 'Identidad',
                        'PASSPORT' => 'Pasaporte',
                        'OTHER' => 'Otro',
                    ]),
                Forms\Components\TextInput::make('identification_number')
                    ->label('Número de Identificación')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('employment_status')
                    ->label('Estado Laboral')
                    ->required()
                    ->options([
                        'EMPLOYED' => 'Empleado',
                        'SELF_EMPLOYED' => 'Independiente',
                        'UNEMPLOYED' => 'Desempleado',
                        'RETIRED' => 'Jubilado',
                    ]),
                Forms\Components\TextInput::make('monthly_income')
                    ->label('Ingreso Mensual')
                    ->required()
                    ->numeric()
                    ->prefix('L'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),
                Tables\Columns\TextColumn::make('identification_number')
                    ->label('No. Identidad')
                    ->searchable(),
                Tables\Columns\TextColumn::make('monthly_income')
                    ->label('Ingreso Mensual')
                    ->money('HNL')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
