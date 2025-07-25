<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Pages\PaymentGateway;
use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('payment')
                ->url(PaymentGateway::getUrl())
                ->label('Payment Gateways')
                ->icon('heroicon-o-cog'),
        ];
    }
}
