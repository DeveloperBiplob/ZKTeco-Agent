<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Rats\Zkteco\Lib\ZKTeco;

class Device
{
    private $zk;

    public function connect($ip, $port)
    {
        $this->zk = new ZKTeco($ip, $port);

        if (!$this->zk->connect()) {
            throw new Exception("Cannot connect to ZKTeco device");
        }

        return true;
    }

    public function getLogs($lastSyncTime = null)
    {
        $attendance = $this->zk->getAttendance();

        $logs = [];

        foreach ($attendance as $log) {

            $timestamp = $log['timestamp'];

            if (
                $lastSyncTime &&
                strtotime($timestamp) <= strtotime($lastSyncTime)
            ) {
                continue;
            }

            $logs[] = [
                'employee_id' => $log['id'],
                'punch_time'  => $timestamp
            ];
        }

        usort($logs, function ($a, $b) {
            return strtotime($a['punch_time'])
                <=> strtotime($b['punch_time']);
        });

        return $logs;
    }

    public function disconnect()
    {
        if ($this->zk) {
            $this->zk->disconnect();
        }
    }
}
