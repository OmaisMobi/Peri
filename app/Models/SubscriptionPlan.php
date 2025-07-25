<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'price',
        'billing_type',
        'storage',
        'additional_storage_charges',
        'projects',
        'tasks',
        'team_members',
        'additional_users_charges',
        'biometric_machine',
        'leave_requests',
        'office_shifts',
        'departments',
        'email_push_notifications',
        'reports',
        'roles_permissions',
        'modules',
    ];

    protected $casts = [
        'modules' => 'array',
    ];
}
