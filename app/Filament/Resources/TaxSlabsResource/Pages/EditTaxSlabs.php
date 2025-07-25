<?php

namespace App\Filament\Resources\TaxSlabsResource\Pages;

use App\Filament\Resources\TaxSlabsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;

class EditTaxSlabs extends EditRecord
{
    protected static string $resource = TaxSlabsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function beforeSave(): void
    {
        $slabs = $this->form->getState()['slabs_data'] ?? [];

        if (!is_array($slabs)) {
            return;
        }

        $lastMax = null;
        $total = count($slabs);

        foreach ($slabs as $index => $bracket) {
            $min = (float) ($bracket['min_annual_salary'] ?? 0);
            $maxRaw = $bracket['max_annual_salary'] ?? null;
            $max = $maxRaw !== null && $maxRaw !== '' ? (float) $maxRaw : null;

            $isLast = $index === $total - 1;

            if ($max !== null) {
                if ($min >= $max) {
                    Notification::make()
                        ->title("Bracket " . ($index + 1) . ": Minimum must be less than Maximum.")
                        ->danger()
                        ->send();

                    throw ValidationException::withMessages([
                        "slabs_data.{$index}.min_annual_salary_start" =>
                        "Bracket " . ($index + 1) . ": Minimum must be less than Maximum.",
                    ]);
                }

                if ($lastMax !== null && $min <= $lastMax) {
                    Notification::make()
                        ->title("Bracket " . ($index + 1) . ": Minimum must be greater than previous maximum ({$lastMax}).")
                        ->danger()
                        ->send();

                    throw ValidationException::withMessages([
                        "slabs_data.{$index}.min_annual_salary_start" =>
                        "Bracket " . ($index + 1) . ": Minimum must be greater than previous maximum ({$lastMax}).",
                    ]);
                }
            } else {
                if (!$isLast) {
                    Notification::make()
                        ->title("Bracket " . ($index + 1) . ": Maximum is required except for the last bracket.")
                        ->danger()
                        ->send();

                    throw ValidationException::withMessages([
                        "slabs_data.{$index}.max_annual_salary" =>
                        "Bracket " . ($index + 1) . ": Maximum salary is required except in the last bracket.",
                    ]);
                }

                if ($lastMax !== null && $min < $lastMax) {
                    Notification::make()
                        ->title("Bracket " . ($index + 1) . ": Minimum must not be less than previous maximum ({$lastMax}).")
                        ->danger()
                        ->send();

                    throw ValidationException::withMessages([
                        "slabs_data.{$index}.min_annual_salary_start" =>
                        "Bracket " . ($index + 1) . ": Minimum must not be less than previous maximum ({$lastMax}).",
                    ]);
                }
            }

            if ($max !== null) {
                $lastMax = $max;
            }
        }
    }
}
