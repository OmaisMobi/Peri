<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'loan_name',
        'user_id',
        'loan_amount',
        'issue_date',
        'reason',
        'deduction_start_date',
        'installment_amount',
        'remaining_amount',
        'status',
        'team_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
