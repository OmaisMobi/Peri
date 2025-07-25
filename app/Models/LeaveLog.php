<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveLog extends Model
{
    protected $fillable = [
        'leave_id',
        'role_id',
        'level',
        'status',
        'remarks',
    ];

    public function leave()
    {
        return $this->belongsTo(\App\Models\Leave::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }
}
