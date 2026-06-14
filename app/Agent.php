<?php

class Agent
{
    private $config;
    private $device;
    private $api;
    private $queue;

    public function __construct($config)
    {
        $this->config = $config;

        $this->device = new Device($config['device']);
        $this->api = new ApiClient($config);
        $this->queue = new Queue();
    }

    public function run()
    {
        echo "Agent Started...\n";

        while (true) {

            $this->syncDevice();
            $this->syncToServer();

            sleep($this->config['sync_interval']);
        }
    }

    private function syncDevice()
    {
        try {

            $this->device->connect(
                $this->config['device']['ip'],
                $this->config['device']['port']
            );

            $lastSyncTime = $this->getLastSyncTime();

            $logs = $this->device->getLogs($lastSyncTime);

            if (empty($logs)) {

                echo "No new logs found\n";

                $this->device->disconnect();

                return;
            }

            foreach ($logs as $log) {
                $this->queue->add($log);
            }

            $latestTime = end($logs)['punch_time'];

            $this->setLastSyncTime($latestTime);

            echo "New logs synced: " . count($logs) . PHP_EOL;

            $this->device->disconnect();

        } catch (Exception $e) {

            echo "Device error: " . $e->getMessage() . PHP_EOL;
        }
    }

    private function syncToServer()
    {
        $logs = $this->queue->getAll();

        if (empty($logs)) {
            echo "No data to sync\n";
            return;
        }

        $payload = [
            'device_serial' => 'DEVICE_001',
            'logs' => $logs
        ];

        $success = $this->api->send($payload);

        if ($success) {
            $this->queue->clear();
            echo "Synced to server\n";
        } else {
            echo "Sync failed, retry later\n";
        }
    }

    private function getLastSyncTime()
    {
        $file = __DIR__ . '/../storage/sync_state.json';

        if (!file_exists($file)) {
            return null;
        }

        $data = json_decode(file_get_contents($file), true);

        return $data['last_sync_time'] ?? null;
    }

    private function setLastSyncTime($time)
    {
        $file = __DIR__ . '/../storage/sync_state.json';

        file_put_contents(
            $file,
            json_encode([
                'last_sync_time' => $time
            ], JSON_PRETTY_PRINT)
        );
    }
}
