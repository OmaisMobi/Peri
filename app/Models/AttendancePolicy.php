<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendancePolicy extends Model
{
    protected $fillable = [
        'team_id',
        'late_policy_enabled',
        'enable_late_come',
        'enable_early_leave',
        'time_offset_allowance',
        'half_day_late_policy',
        'single_biometric_policy_enabled',
        'single_biometric_behavior',
        'grace_policy_enabled',
        'days_counter',
        'late_penalty',
        'grace_duration',
        'overtime_policy_enabled',
        'overtime_start_delay',
        'overtime_max_minutes',
        'overtime_duration',
        'consecutive_leaves_policy_enabled',
        'consecutive_leave_gap_days',
        'sandwich_rule_policy_enabled',
        'leaves_policy_option'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
