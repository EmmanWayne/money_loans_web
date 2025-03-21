<?php

namespace App\Filament\Resources\PaymentScheduleResource\Pages;

use App\Filament\Resources\PaymentScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentSchedule extends ViewRecord
{
    protected static string $resource = PaymentScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
