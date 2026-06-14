<?php

require_once __DIR__ . '/../app/Agent.php';
require_once __DIR__ . '/../app/Device.php';
require_once __DIR__ . '/../app/ApiClient.php';
require_once __DIR__ . '/../app/Queue.php';

class RunAgent
{
    public function handle()
    {
        $config = require __DIR__ . '/../config/config.php';

        $agent = new Agent($config);
        $agent->run();
    }
}
