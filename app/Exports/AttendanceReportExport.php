<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class AttendanceReportExport implements FromArray
{
    public $userAttendanceData;
    public $policy;
    public $allLeaveTypes;

    public function __construct($userAttendanceData, $policy, $allLeaveTypes)
    {
        $this->userAttendanceData = $userAttendanceData;
        $this->policy = $policy;  // Ensure this holds the correct policy object
        $this->allLeaveTypes = $allLeaveTypes;
    }

    public function array(): array
    {
        $exportData = [];

        if (!$this->policy) {
            throw new \Exception('Policy object is missing');
        }

        // Header
        $header = [
            'Employee ID',
            'Type',
            'Name',
            'Joining Date',
            'Duration',
            'Absents'
        ];

        // Add late and overtime if enabled in policy
        if ($this->policy->late_policy_enabled) {
            $header[] = 'Late Minutes';
        }

        if ($this->policy->overtime_policy_enabled) {
            $header[] = 'Overtime Minutes';
        }

        // Add leave types to header
        foreach ($this->allLeaveTypes as $type) {
            $header[] = $type;
        }

        // Add leave balance columns
        $header[] = 'Paid Leaves';
        $header[] = 'Unpaid Leaves';

        $exportData[] = $header;

        // Rows
        foreach ($this->userAttendanceData as $data) {
            $row = [
                $data['employee_id'],
                $data['attendance_type'],
                $data['name'],
                $data['joining_date'],
                $data['employment_duration'],
                $data['absents']
            ];

            // Add late minutes if policy is enabled
            if ($this->policy->late_policy_enabled) {
                $row[] = $data['late_minutes'];
            }

            // Add overtime minutes if policy is enabled
            if ($this->policy->overtime_policy_enabled) {
                $row[] = $data['overtime_minutes'];
            }

            // Add leave type data
            foreach ($this->allLeaveTypes as $type) {
                $leave = collect($data['leave_balances'])->firstWhere('name', $type);
                $row[] = $leave ? "{$leave['used']} / {$leave['total']}" : '0 / 0';
            }

            // Add leave balance
            $row[] = $data['total_paid_leaves'] ?? '0 / 0';

            // Add unpaid leaves
            $unpaidLeaves = collect($data['leave_balances'])->sum('unpaid_used');
            $row[] = $unpaidLeaves ?? '0';

            $exportData[] = $row;
        }

        return $exportData;
    }
}
