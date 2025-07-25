<?php

namespace App\Filament\Client\Pages\Auth;

use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Pages\Auth\Register as BaseRegister;

class Registration extends BaseRegister
{
    use HasCustomLayout;
    protected static string $view = 'filament.client.pages.auth.registration';
}
