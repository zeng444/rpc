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

    /**
     * Author:Robert
     *
     * @var string
     */
    protected $host;

    /**
     * Author:Robert
     *
     * @var int
     */
    protected $timeout;

    /**
     * Http constructor.
     * @param string $host
     * @param int $timeout
     */
    public function __construct(string $host, int $timeout = 2)
    {
        $this->host = $host;
        $this->timeout = $timeout;
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
                'timeout' => $this->timeout,
                'method' => 'POST',
                'header' => sprintf("Content-Type: application/json; charset: utf-8\r\nContent-Length: %d\r\nAccept-Language: %s\r\nCurrency: %s\r\n", strlen($ctx), isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '', isset($_SERVER['HTTP_CURRENCY']) ? $_SERVER['HTTP_CURRENCY'] : ''),
                'content' => $ctx,
            ],
        ];
        return file_get_contents($this->host, false, stream_context_create($options));
    }
}
