<?php

class ApiClient
{
    private $url;
    private $token;

    public function __construct($config)
    {
        $this->url = $config['api_url'];
        $this->token = $config['api_token'];
    }

    public function send($payload)
    {
        $data = json_encode($payload);

        $ch = curl_init($this->url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $this->token,
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);

        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        echo "\n";
        echo "HTTP CODE: {$httpCode}\n";
        echo "CURL ERROR: {$error}\n";
        echo "RESPONSE: {$response}\n";
        echo "\n";

        return $httpCode >= 200 && $httpCode < 300;
    }
}
