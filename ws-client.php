<?php

require(__DIR__ . '/vendor/autoload.php');

use WebSocket\Client;
$options = require __DIR__ . '/config/config.php';
$url = sprintf("ws://{$options['ws']['addr']}:{$options['ws']['port']}");
$client = new Client($url);

$payload = 'exit';
$client->send($payload);

echo 'Message from Server' . $client->receive();

$client->close();
