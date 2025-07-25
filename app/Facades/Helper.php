<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void team(Team |array $tenant)
 * @method static void team(User |array $users)
 * @method static void assignUsers(User |array $users)
 */
class Helper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'helper';
    }
}
