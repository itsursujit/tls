<?php

$host = '0.0.0.0';
$port = 8080;
$timeout = 30;
$cert = 'assets/cert.pem';

$context = stream_context_create(
    [ 'ssl'=> [ 'local_cert'=> $cert, "crypto_method" => STREAM_CRYPTO_METHOD_TLS_CLIENT ] ]
);

stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
stream_context_set_option($context, 'ssl', 'verify_peer', false);
stream_context_set_option($context, 'ssl', 'verify_peer_name', false);
stream_context_set_option($context, 'ssl', 'passphrase', 'sujit');

if ($socket = stream_socket_client( 'tlsv1.2://'.$host.':'.$port, $errno,
    $errstr, 30, STREAM_CLIENT_CONNECT, $context) ) {
    $meta = stream_get_meta_data($socket);

    print_r( $meta );

    fwrite($socket, "Hello, World!\n");
    echo stream_socket_recvfrom($socket,8192);
    fclose($socket);
} else {
    echo "ERROR: $errno - $errstr\n";
}