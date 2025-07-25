<?php

namespace App\Filament\Client\Pages\Auth;

use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use Filament\Pages\Page;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    use HasCustomLayout;
    protected static string $view = 'filament.client.pages.auth.login';
}
