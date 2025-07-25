<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'team_id', 'finger', 'note'];

    protected $casts = [
        'finger' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the team that owns the attendance.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
    /**
     * Get attendances within a date range for an active user with attendance config.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Carbon\Carbon  $from
     * @param  \Carbon\Carbon  $to
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function scopeAttendanceWithinDateRange($query, $from, $to)
    {
        return $query->whereBetween('finger', [$from, $to->endOfDay()])
            ->whereHas('user', function ($q) {
                $q->where('active', 1)
                    ->where('attendance_config', 1);
            })
            ->with('user');
    }

    /**
     * Calculate the total late minutes for a user on a specific date
     *
     * @param Collection $fingers
     * @param User $user
     * @param Carbon $date
     * @return int
     */
    public static function calculateLateMin($fingers, $user, $date)
    {
        $shiftLog = ShiftLog::where('shift_id', $user->shift_id)
            ->whereDate('created_at', '<=', $date->toDateString())
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$shiftLog) {
            return 0;
        }

        $shiftStart = Carbon::parse($date->format('Y-m-d') . ' ' . $shiftLog->starting_time)->setSeconds(0);
        $shiftEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $shiftLog->ending_time)->setSeconds(0);
        $in = $fingers->first()->copy()->setSeconds(0);
        $out = $fingers->last()->copy()->setSeconds(0);
        if (!self::firstTimeShouldBeIn($fingers, $user, $date)) {
            $lateIn = $in->gt($shiftStart) ? $in->diffInMinutes($shiftStart) : 0;
        } else {
            $lateIn = 0;
        }
        if (!self::lastTimeShouldBeOut($fingers, $user, $date)) {
            $earlyOut = $out->lt($shiftEnd) ? $shiftEnd->diffInMinutes($out) : 0;
        } else {
            $earlyOut = 0;
        }
        return - ($lateIn + $earlyOut);
    }
    /**
     *
     * @param $fingers
     * @param User $user
     * @param Carbon $date
     * @return bool
     */
    public static function firstTimeShouldBeIn($fingers, $user, $date)
    {
        $minFinger = $fingers->first()->copy()->setSeconds(0);
        return Leave::where('user_id', $user->id)
            ->where('type', 'short_leave')
            ->where('status', 'approved')
            ->whereDate('starting_date', '<=', $date->toDateString())
            ->whereDate('ending_date', '>=', $date->toDateString())
            ->whereTime('starting_time', '<=', $minFinger->format('H:i:s'))
            ->whereTime('ending_time', '>=', $minFinger->format('H:i:s'))
            ->exists();
    }
    /**
     * Check if the last time should be out based on the user's leave
     *
     * @param $fingers
     * @param User $user
     * @param Carbon $date
     * @return bool
     */
    public static function lastTimeShouldBeOut($fingers, $user, $date)
    {
        $maxFinger = $fingers->last()->copy()->setSeconds(0);
        return Leave::where('user_id', $user->id)
            ->where('type', 'short_leave')
            ->where('status', 'approved')
            ->whereDate('starting_date', '<=', $date->toDateString())
            ->whereDate('ending_date', '>=', $date->toDateString())
            ->whereTime('starting_time', '>=', $maxFinger->format('H:i:s'))
            ->exists();
    }
    
}
