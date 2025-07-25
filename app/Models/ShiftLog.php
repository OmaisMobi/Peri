<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShiftLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'shift_id',
        'team_id',
        'name',
        'short_leave',
        'starting_time',
        'ending_time',
        'break_start',
        'break_end',
        'half_day_check_in',
        'half_day_check_out',
    ];
}
