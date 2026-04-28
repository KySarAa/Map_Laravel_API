<?php

return [

    'default' => [
        'host' => '0e6eab887f084d39be5a2e37743aa9bc.s1.eu.hivemq.cloud',
        'port' => 8883,
        'username' => 'laravel',
        'password' => 'Zoe3****',
        'client_id' => 'laravel-' . uniqid(),
        'clean_session' => true,

        'tls' => [
            'enabled' => true,
            'verify_peer' => true,
            'verify_peer_name' => true,
        ],
    ],

];
