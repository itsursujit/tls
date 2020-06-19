<?php

require(__DIR__ . '/vendor/autoload.php');
$options = require __DIR__ . '/config/config.php';

$client = new \Tls\Client($options['tls']['host'] . ':' . $options['tls']['port'], $options['tls']['passphrase'], $options);
$client->send('Hello World');