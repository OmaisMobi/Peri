<?php

namespace App\Jobs;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncAttendanceJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected array $attendanceData;
    protected int $teamId;

    public function __construct(array $attendanceData, int $teamId)
    {
        $this->attendanceData = $attendanceData;
        $this->teamId = $teamId;
    }

    public function handle(): void
    {
        foreach ($this->attendanceData as $att) {
            $exists = Attendance::where('user_id', $att['id'])
                ->where('finger', $att['timestamp'])
                ->exists();

            if (!$exists) {
                Attendance::create([
                    'user_id' => $att['id'],
                    'team_id' => $this->teamId,
                    'finger'  => $att['timestamp'],
                ]);
            }
        }
    }
}
