<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryComponentCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'team_id'];

    public function salaryComponents()
    {
        return $this->hasMany(SalaryComponent::class, 'salary_component_category_id');
    }
}
