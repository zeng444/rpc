<?php

namespace Janfish\Rpc\Client;

/**
 * Janfish RPC client
 * Author:Robert
 *
 * Class Client
 * @package Janfish\Rpc\Client
 */
class Http implements ClientInterface
{

    protected $host;

    /**
     * Http constructor.
     * @param $host
     */
    public function __construct($host)
    {
        $this->host = $host;
    }

    /**
     * Apply remote call
     * Author:Robert
     *
     * @param $ctx
     * @return mixed
     */
    public function remoteCall(string $ctx): string
    {
        // RPC call
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => sprintf("Content-Type: application/json; charset: utf-8\r\nContent-Length: %d\r\nAccept-Language: %s\r\nCurrency: %s\r\n", strlen($ctx), isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '', isset($_SERVER['HTTP_CURRENCY']) ? $_SERVER['HTTP_CURRENCY'] : ''),
                'content' => $ctx,
            ],
        ];
        return file_get_contents($this->host, false, stream_context_create($options));
    }
}
