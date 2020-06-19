<?php

return [
    'tls' => [
        'host' => '0.0.0.0',
        'port' => 8000,
        'cert' => __DIR__ . '/../assets/cert.pem',
        'timeout' => 30,
        'passphrase' => 'sujit'
    ],
    'ws' => [
        'addr' => '0.0.0.0',
        'port' => 8001,
        'timeout' => 300,
    ]
];
