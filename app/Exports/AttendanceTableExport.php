<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Carbon\Carbon;

class AttendanceTableExport implements FromArray
{
    protected $userAttendanceData;
    protected $users;

    public function __construct($userAttendanceData, $users)
    {
        $this->userAttendanceData = $userAttendanceData;
        $this->users = $users;
    }

    public function array(): array
    {
        $exportData = [];

        $datesGroupedByMonth = $this->groupDatesByMonth();

        // Flattened list of dates (for accurate headers and row alignment)
        $allDates = [];
        foreach ($datesGroupedByMonth as $dates) {
            foreach ($dates as $date) {
                $allDates[] = $date;
            }
        }

        // Header
        $headerRow = ['ID', 'Type', 'Name', 'A / L / LM / OT'];
        foreach ($allDates as $date) {
            $headerRow[] = Carbon::parse($date)->format('d-M'); // or 'Y-m-d' if needed
        }

        $exportData[] = $headerRow;

        foreach ($this->userAttendanceData as $data) {
            $row = [
                strip_tags($data['employee_id']),
                strip_tags($data['attendance_type']),
                strip_tags($data['name']),
                strip_tags($data['total_late_minutes'])
            ];

            foreach ($allDates as $date) {
                $content = str_replace(['<br>', '<br/>', '<br />'], "\r\n", $data[$date] ?? '');
                $row[] = strip_tags($content);
            }

            $exportData[] = $row;
        }

        return $exportData;
    }


    // Helper function to group dates by month (like your view code)
    private function groupDatesByMonth()
    {
        $datesGroupedByMonth = [];
        $firstUserData = reset($this->userAttendanceData);  // Get the first user
        $dates = array_keys($firstUserData); // Get all dates from the first user

        foreach ($dates as $key) {
            if (Carbon::canBeCreatedFromFormat($key, 'Y-m-d')) {
                $month = Carbon::parse($key)->format('F-Y');  // Group by full month-year (e.g., May-2025)
                $datesGroupedByMonth[$month][] = $key;
            }
        }

        return $datesGroupedByMonth;
    }
}
