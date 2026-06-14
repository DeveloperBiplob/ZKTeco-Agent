<?php

return [
    // 'api_url' => 'https://erp.company.com/api/device-attendance-sync',
    // 'api_url' => 'http://127.0.0.1:8000/device/sync-attendance',
    'api_url' => 'http://127.0.0.1:8000/api/device/sync-attendance',
    'api_token' => 'YOUR_TOKEN_HERE',

    'device' => [
        'ip' => '192.168.0.201',
        'port' => 4370,
        'password' => 0
    ],

    'sync_interval' => 60
];
