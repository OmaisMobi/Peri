<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'apply_on',
        'leaves_count',
        'team_id', // Make team_id fillable
    ];
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
