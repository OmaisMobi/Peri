<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'company_name',
        'country_id',
        'logo',
        'address',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
