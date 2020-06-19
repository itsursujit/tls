<?php


namespace Tls;


/**
 * Class Server
 * @package Tls
 */
class Server
{
    /**
     * @var
     */
    private $addr;

    /**
     * @var
     */
    private $options;

    /**
     * @var
     */
    private $passphrase;

    /**
     * @var
     */
    private $srvCtx;

    /**
     * @var
     */
    private $server;

    /**
     * Server constructor.
     * @param $addr
     * @param $passphrase
     * @param $options
     */
    public function __construct($addr, $passphrase, $options)
    {
        $this->addr = $addr;
        $this->options = $options;
        $this->passphrase = $passphrase;
    }

    /**
     *
     */
    public function connect()
    {
        $this->srvCtx = stream_context_create();
        $this->prepareContextOption();
        $url = sprintf("tlsv1.2://%s:%s", $this->options['tls']['host'], $this->options['tls']['port']);
        $this->server = stream_socket_server($url, $errorNumber,
            $errorString, STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, $this->srvCtx);
        echo "PHP Socket Server started at " . $url . ", at " . date('Y-m-d H:i:s') . "\n";
    }

    /**
     *
     */
    public function wait()
    {
        $this->connect();
        while (true) {
            /* Accept incoming requests and handle them as child processes */
            $client = stream_socket_accept($this->server);
            if (!$client) {
                echo 'Server has some problem';
                break;
            }
            $ip = stream_socket_get_name($client, true);

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
            echo date('Y-m-d H:i:s') . " | " . $ip . ": \033[0;32m" . $input . "\033[0m" . PHP_EOL;

            fclose($client);
        }

// Close the master sockets
        socket_close($this->server);
    }

    /**
     *
     */
    private function prepareContextOption()
    {
        stream_context_set_option($this->srvCtx, 'ssl', 'local_cert', $this->options['tls']['cert']);
        stream_context_set_option($this->srvCtx, 'ssl', 'crypto_method', STREAM_CRYPTO_METHOD_TLS_SERVER);

        stream_context_set_option($this->srvCtx, 'ssl', 'allow_self_signed', true);
        stream_context_set_option($this->srvCtx, 'ssl', 'verify_peer', false);
        stream_context_set_option($this->srvCtx, 'ssl', 'verify_peer_name', false);
        stream_context_set_option($this->srvCtx, 'ssl', 'passphrase', $this->options['tls']['passphrase']);
    }

}