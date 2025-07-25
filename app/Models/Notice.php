<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'is_active',
        'team_id',
    ];

    // Automatically cast the 'content' attribute to an array
    protected $casts = [
        'content' => 'array',
    ];

    // A function to get the content
    public function getContentAttribute($value)
    {
        return json_decode($value, true);
    }

    // A function to set the content as a JSON string before saving it
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = json_encode($value);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
