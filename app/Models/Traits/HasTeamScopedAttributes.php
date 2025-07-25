<?php

namespace App\Models\Traits;

use Filament\Facades\Filament;

trait HasTeamScopedAttributes
{
    public function getBankNameAttribute()
    {
        $team = Filament::getTenant();
        if (!$team) {
            return null;
        }

        return $this->bankDetails()
            ->where('team_id', $team->id)
            ->value('bank_name');
    }
    public function getProbationSalaryAttribute()
    {
        $team = Filament::getTenant();
        if (!$team) {
            return null;
        }

        return $this->bankDetails()
            ->where('team_id', $team->id)
            ->value('probation_salary');
    }

    public function getAccountHolderNameAttribute()
    {
        $team = Filament::getTenant();
        if (!$team) {
            return null;
        }

        return $this->bankDetails()
            ->where('team_id', $team->id)
            ->value('account_holder_name');
    }
    public function getBaseSalaryAttribute()
    {
        $team = Filament::getTenant();
        if (!$team) {
            return null;
        }

        return $this->bankDetails()
            ->where('team_id', $team->id)
            ->value('base_salary');
    }

    public function getAccountNumberAttribute()
    {
        $team = Filament::getTenant();
        if (!$team) {
            return null;
        }

        return $this->bankDetails()
            ->where('team_id', $team->id)
            ->value('account_number');
    }

    public function getPaymentMethodAttribute()
    {
        $team = Filament::getTenant();
        if (!$team) {
            return null;
        }

        return $this->bankDetails()
            ->where('team_id', $team->id)
            ->value('payment_method');
    }

    public function getFundsArrayAttribute()
    {
        $team = Filament::getTenant();
        if (!$team) {
            return [];
        }

        return $this->funds()
            ->wherePivot('team_id', $team->id)
            ->pluck('id')
            ->toArray();
    }
}
