<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaasFeature extends Model
{
    protected $fillable = [
        'icon',
        'title',
        'description',
    ];
}
