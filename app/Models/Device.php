<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ['team_id', 'device_name', 'device_ip_address', 'device_external_port', 'timezone'];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
