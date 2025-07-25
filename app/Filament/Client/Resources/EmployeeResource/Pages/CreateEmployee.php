<?php

namespace App\Filament\Client\Resources\EmployeeResource\Pages;

use App\Filament\Client\Resources\EmployeeResource;
use App\Models\DepartmentUser;
use App\Models\Role;
use App\Models\ShiftUser;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;
    protected static bool $canCreateAnother = false;

    protected function afterCreate(): void
    {
        $role = Role::find($this->data["role"]);
        $this->record->syncRoles($role);

        $tenant = Filament::getTenant();

        if ($this->data["shift_id"]) {
            ShiftUser::updateOrCreate(
                [
                    'team_id' => $tenant->id,
                    'user_id' => $this->record->id,
                ],
                [
                    'shift_id' => $this->data["shift_id"],
                ]
            );
        }
        if ($this->data["department_id"]) {
            DepartmentUser::updateOrCreate(
                [
                    'team_id' => $tenant->id,
                    'user_id' => $this->record->id,
                ],
                [
                    'department_id' => $this->data["department_id"],
                ]
            );
        }

        $bankDetails = $this->data['bank_details'] ?? [];
        $paymentMethod = $bankDetails['payment_method'] ?? null;
        $bankDetails['base_salary'] = $bankDetails['base_salary'] !== '' ? $bankDetails['base_salary'] : null;
        $bankDetails['probation_salary'] = $bankDetails['probation_salary'] !== '' ? $bankDetails['probation_salary'] : null;

        if ($paymentMethod !== 'bank_transfer') {
            $bankDetails['account_holder_name'] = null;
            $bankDetails['account_number'] = null;
            $bankDetails['bank_name'] = null;
            
        }

        $this->record->bankDetails()->updateOrCreate(
            ['team_id' => $tenant->id],
            $bankDetails
        );

        $fundIds = $this->data['funds_ids'] ?? [];
        foreach ($fundIds as $fundId) {
            $this->record->funds()->attach($fundId, ['team_id' => $tenant->id]);
        }
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
