<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = ['section_image', 'product_image', 'value'];

    protected $casts = [
        'value' => 'array',
    ];
}
