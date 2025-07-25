<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxSlabs extends Model
{
    use HasFactory;

    protected $fillable = [
        'country',
        'salary_currency',
        'financial_year_start',
        'financial_year_end',
        'slabs_data',
    ];

    protected $casts = [
        'slabs_data' => 'array',
        'financial_year_start' => 'date',
        'financial_year_end' => 'date',
    ];

    public function getFinancialYearStartMdAttribute()
    {
        return $this->financial_year_start?->format('m-d');
    }

    public function getFinancialYearEndMdAttribute()
    {
        return $this->financial_year_end?->format('m-d');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
