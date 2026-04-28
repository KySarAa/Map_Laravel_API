<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class MqttService
{
    public function send($topic, $message)
    {
        $server = '0e6eab887f084d39be5a2e37743aa9bc.s1.eu.hivemq.cloud';
        $port = 8883;
        $clientId = 'laravel-' . uniqid();

        $settings = (new ConnectionSettings)
            ->setUsername('laravel')
            ->setPassword('Zoe3****')
            ->setUseTls(true)
            ->setTlsVerifyPeer(true)
            ->setTlsVerifyPeerName(true);

        $mqtt = new MqttClient($server, $port, $clientId);
        $mqtt->connect($settings, true);

        $mqtt->publish($topic, $message, 0);

        $mqtt->disconnect();
    }
}
