<?php


namespace Tls;


/**
 * Class Client
 * @package Tls
 */
class Client
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
    private $socket;

    /**
     * Client constructor.
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
     * @throws \Exception
     */
    public function connect()
    {
        $this->srvCtx = stream_context_create(
            ['ssl' => ['local_cert' => $this->options['tls']['cert'], "crypto_method" => STREAM_CRYPTO_METHOD_TLS_CLIENT]]
        );
        $this->prepareContextOption();
        $url = sprintf("tlsv1.2://%s:%s", $this->options['tls']['host'], $this->options['tls']['port']);
        $this->socket = stream_socket_client($url, $errorNumber, $errorString, $this->options['tls']['timeout'], STREAM_CLIENT_CONNECT, $this->srvCtx);
        if (!$this->socket) {
            throw new \Exception("Unable to connect to server via TLS");
        }
        $meta = stream_get_meta_data($this->socket);
    }

    /**
     * @param $payload
     * @return string
     * @throws \Exception
     */
    public function send($payload)
    {
        $this->connect();
        fwrite($this->socket, $payload);
        $response = stream_socket_recvfrom($this->socket, 8192);
        fclose($this->socket);
        return $response;
    }

    /**
     *
     */
    private function prepareContextOption()
    {
        stream_context_set_option($this->srvCtx, 'ssl', 'allow_self_signed', true);
        stream_context_set_option($this->srvCtx, 'ssl', 'verify_peer', false);
        stream_context_set_option($this->srvCtx, 'ssl', 'verify_peer_name', false);
        stream_context_set_option($this->srvCtx, 'ssl', 'passphrase', $this->options['tls']['passphrase']);
    }
}