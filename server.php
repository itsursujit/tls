<?php

// Set the ip and port we will listen on
$address = '0.0.0.0';
$port = 8080;

$cert = 'assets/cert.pem';

$context = stream_context_create();

stream_context_set_option($context, 'ssl', 'local_cert', $cert);
stream_context_set_option($context, 'ssl', 'crypto_method', STREAM_CRYPTO_METHOD_TLS_SERVER);

stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
stream_context_set_option($context, 'ssl', 'verify_peer', false);
stream_context_set_option($context, 'ssl', 'verify_peer_name', false);
stream_context_set_option($context, 'ssl', 'passphrase', 'sujit');

$server = stream_socket_server('tlsv1.2://'.$address.':'.$port, $errno,
    $errstr, STREAM_SERVER_BIND|STREAM_SERVER_LISTEN, $context);

// Display server start time
echo "PHP Socket Server started at " . $address . " " . $port . ", at ". date( 'Y-m-d H:i:s' ) ."\n";

// loop and listen
while (true) {
    /* Accept incoming requests and handle them as child processes */
    $client = stream_socket_accept($server);
    if(!$client)
    {
        echo 'Server has some problem';
        break;
    }
    $ip = stream_socket_get_name( $client, true );

    echo "New connection from " . $ip;

    stream_set_blocking($client, true); // block the connection until SSL is done
    stream_socket_enable_crypto($client, true, STREAM_CRYPTO_METHOD_TLS_SERVER);

    // Read the input from the client – 1024 bytes
    $input = fread($client, 1024);

    // unblock connection
    stream_set_blocking($client, false);

    // Strip all white spaces from input
    $output = preg_replace("[ \t\n\r]", "", $input) . "\0";

    $new = $input;

    // Display Date, IP and Msg received
    echo date( 'Y-m-d H:i:s' ) . " | " . $ip . ": \033[0;32m" . $input . "\033[0m" . PHP_EOL;

    fclose($client);
}

// Close the master sockets
socket_close($context);