<?php

namespace App\Filament\Client\CustomWidgets;

use App\Models\Attendance;
use App\Models\User;
use App\Services\LeaveService;
use App\Services\HelperService;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class UserAttendanceSummaryChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Attendance Summary';
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = null;
    protected int|string|array $columnSpan = 6;

    public ?string $userId = null;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['display' => false],
                ],
                'y' => [
                    'grid' => ['display' => false],
                    'ticks' => ['display' => false],
                ],
            ],
        ];
    }

    protected function getData(): array
    {
        $user = User::find($this->userId);

        if (!$user) {
            return [
                'labels' => ['Presents', 'Absents', 'Leaves'],
                'datasets' => [[
                    'data' => [0, 0, 0],
                    'backgroundColor' => ['#10B981', '#EF4444', '#F59E0B'],
                ]],
            ];
        }

        $startDate = Carbon::now()->startOfYear();
        $endDate = Carbon::now();
        $totalDaysInYear = $startDate->diffInDays($endDate) + 1;

        $helper = new HelperService();

        // 1. Calculate Present Days (excluding holidays)
        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('finger', [$startDate, $endDate])
            ->get();

        $presentDates = $attendances->pluck('finger')
            ->map(fn($finger) => Carbon::parse($finger)->toDateString())
            ->unique()
            ->reject(fn($dateString) => $helper->checkHoliday($user, Carbon::parse($dateString)));

        $presents = $presentDates->count();

        // 2. Calculate Leaves using LeaveService
        $leaveService = new LeaveService();
        $leaveBalances = $leaveService->getLeaveBalanceForUser($user, $startDate, $endDate);

        $leaves = collect($leaveBalances)->sum(fn($b) => $b['used'] + $b['unpaid_used']);

        // 3. Count total non-working days (holidays from DB only)
        $nonWorkingDaysCount = 0;
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            if ($helper->checkHoliday($user, $date)) {
                $nonWorkingDaysCount++;
            }
        }

        // 4. Calculate working days and absents
        $totalWorkingDays = $totalDaysInYear - $nonWorkingDaysCount;
        $absents = max(0, $totalWorkingDays - $presents - $leaves);

        return [
            'labels' => ['Presents', 'Absents', 'Leaves'],
            'datasets' => [[
                'data' => [$presents, $absents, $leaves],
                'backgroundColor' => ['#10B981', '#EF4444', '#F59E0B'],
            ]],
        ];
    }

    public static function canView(): bool
    {
        return Auth::check();
    }
}
