<?php

namespace App\Models;

use App\Services\PayrollCalculationService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OffCyclePayroll extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'off_cycle_payrolls';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'team_id',
        'user_id',
        'period_start',
        'period_end',
        'earnings',
        'deductions',
        'tax',
        'net_pay',
        'status',
        'rejection_reason',
        'off_cycle_pay_run_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'earnings' => 'array',
        'deductions' => 'array',
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    /**
     * Get the user that owns the off-cycle payroll.
     */
    public function offCyclePayRun()
    {
        return $this->belongsTo(OffCyclePayRun::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payrollCalculationService(): PayrollCalculationService
    {
        return app(PayrollCalculationService::class);
    }

    public function getPeriodAttribute(): string
    {
        return $this->period_start->format('d M Y') . ' - ' . $this->period_end->format('d M Y');
    }

    public function getTotalEarningsAttribute(): float
    {
        return collect($this->earnings)->sum('amount');
    }

    /**
     * Get the total deductions.
     */
    public function getTotalDeductionsAttribute(): float
    {
        return collect($this->deductions)->sum('amount');
    }
    
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
