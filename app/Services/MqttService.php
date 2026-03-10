<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttService
{
    public function send($topic, $message)
    {
        $server = '172.16.151.161';
        $port = 1883;
        $clientId = 'laravel-' . uniqid();

        $settings = new ConnectionSettings();

        $mqtt = new MqttClient($server, $port, $clientId);
        $mqtt->connect($settings, true);

        $mqtt->publish($topic, $message, 0);

        $mqtt->disconnect();
    }
}
