<?php

// app/Services/LeaveService.php

namespace App\Services;

use App\Models\Leave;
use App\Models\LeaveType;
use Carbon\Carbon;
use Filament\Facades\Filament;

class LeaveService
{
    public function getLeaveBalanceForUser($user, $fromDate, $toDate)
    {
        $gender = strtolower(trim($user->gender ?? ''));
        $martialStatus = strtolower(trim($user->martial_status ?? ''));
        $applyOn = (!$gender || !$martialStatus) ? 'all' : "{$gender}_{$martialStatus}";

        $leaveTypes = Filament::getTenant()->leaveTypes()
            ->where(function ($query) use ($applyOn) {
                $query->where('apply_on', 'all')->orWhere('apply_on', $applyOn);
            })
            ->get();

        $balances = [];

        $reportStartDate = Carbon::parse($fromDate)->startOfDay();
        $reportEndDate = Carbon::parse($toDate)->endOfDay();

        foreach ($leaveTypes as $leaveType) {
            $allowed = $leaveType->leaves_count ?? 0;
            $name = $leaveType->name;

            $leaves = Filament::getTenant()->leaves()
                ->where('user_id', $user->id)
                ->where('leave_type', $name)
                ->where('status', 'approved')
                ->where(function ($query) use ($reportStartDate, $reportEndDate) {
                    $query->where('starting_date', '<=', $reportEndDate)
                        ->where('ending_date', '>=', $reportStartDate);
                })
                ->get();

            $paidUsed = 0.0;
            $unpaidUsed = 0.0;

            foreach ($leaves as $leave) {
                $leaveStartDate = Carbon::parse($leave->starting_date)->startOfDay();
                $leaveEndDate = Carbon::parse($leave->ending_date)->endOfDay();

                // Determine the intersection period between the leave dates and the report range
                $intersectStart = $leaveStartDate->max($reportStartDate);
                $intersectEnd = $leaveEndDate->min($reportEndDate);

                if ($intersectStart->lte($intersectEnd)) {
                    for ($date = $intersectStart->copy(); $date->lte($intersectEnd); $date->addDay()) {
                        // Check if this specific day is within the report range (redundant check due to intersection logic, but safe)
                        if ($date->between($reportStartDate, $reportEndDate, true)) {
                            // Check if this specific day is a holiday for the user
                            $isHoliday = false;
                            // You might need to inject or resolve the Holiday model/service here
                            $holidays = Filament::getTenant()->holidays()
                                ->whereDate('starting_date', '<=', $date)
                                ->whereDate('ending_date', '>=', $date)
                                ->get();

                            foreach ($holidays as $holiday) {
                                $users = $holiday->users ?? [];
                                $departments = $holiday->departments ?? [];
                                $shifts = $holiday->shifts ?? [];

                                if (
                                    $holiday->apply === 'all' ||
                                    (is_array($users) && in_array($user->id, $users)) || // Check if $users is array
                                    (is_array($departments) && in_array($user->department_id, $departments)) || // Check if $departments is array
                                    (is_array($shifts) && in_array($user->shift_id, $shifts)) // Check if $shifts is array
                                ) {
                                    $isHoliday = true;
                                    break;
                                }
                            }

                            // Count the day if it's within the report range and not a holiday (for paid leave)
                            if ($leave->paid == 1) {
                                if (!$isHoliday) { // Only count paid leave days if not a holiday
                                    if ($leave->type === 'regular') {
                                        $paidUsed += 1.0;
                                    } elseif ($leave->type === 'half_day') {
                                        // Assuming a half-day leave record represents 0.5 day if it falls within the range
                                        $paidUsed += 0.5;
                                    } elseif ($leave->type === 'short_leave') {
                                        // Assuming 0.25 for short leave if it falls within the range
                                        $paidUsed += 0.25;
                                    }
                                }
                            } else { // Unpaid leave counts regardless of holiday status
                                if ($leave->type === 'regular') {
                                    $unpaidUsed += 1.0;
                                } elseif ($leave->type === 'half_day') {
                                    $unpaidUsed += 0.5;
                                } elseif ($leave->type === 'short_leave') {
                                    $unpaidUsed += 0.25;
                                }
                            }
                        }
                    }
                }
            }

            $balances[] = [
                'name' => $name,
                'total' => $allowed, // This remains the total allowed based on duration/policy
                'used' => $paidUsed, // This is now paid leave used *within the report range*
                'unpaid_used' => $unpaidUsed, // This is now unpaid leave used *within the report range*
                'remaining' => max(0, $allowed - $paidUsed), // This remaining is based on total allowed vs paid used in range
            ];
        }

        return $balances ?? [];
    }
}
