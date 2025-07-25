<?php

namespace App\Http\Controllers;

use AgileBM\ZKLib\ZKLib;
use App\Jobs\SyncAttendanceJob;
use App\Models\Attendance;
use App\Models\Device;
use App\Models\Team;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function index()
    {
        $devices = Device::all();
        foreach ($devices as $device) {
            $ip = $device->device_ip_address;
            $port = $device->device_external_port;
            $team = Team::find($device->team_id);
            $datetime = new DateTime('now', new DateTimeZone($team->timezone));
            $cdate = $datetime->format('Y-m-d H:i:s');
            $zk = new ZKLib($ip, $port);
            try {
                $zk->connect();
                $zk->disableDevice();
                $zk->setTime($cdate);
                $attendance = $zk->getAttendance();
                $zk->clearAttendance();
                $zk->enableDevice();
                $zk->disconnect();
                dispatch(new SyncAttendanceJob($attendance, $device->team_id));
                $allAttendance[$ip] = $attendance;
                return response()->json([
                    'attendance' => $allAttendance,
                ]);
            } catch (\Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
    }
}
