<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Email extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'email-service';
    }
}