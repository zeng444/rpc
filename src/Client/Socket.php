<?php

namespace Janfish\Rpc\Client;

/**
 * Janfish RPC client
 * Author:Robert
 *
 * Class Client
 * @package Janfish\Rpc\Client
 */
class Socket implements ClientInterface
{

    /**
     * Author:Robert
     *
     * @var
     */
    protected $_di;


    /**
     * Author:Robert
     *
     * @var mixed
     */
    public $host = 'tcp://127.0.0.1:8989';


    const RPC_EOL = "\r\n";

    /**
     * Socket constructor.
     * @param $host
     */
    public function __construct(string $host)
    {
        $this->host = $host;
    }

    /***
     * Author:Robert
     *
     * @param string $ctx
     * @return mixed
     * @throws Exception
     */
    public function remoteCall(string $ctx): string
    {
        $fp = stream_socket_client($this->host, $errno, $errstr);
        if (!$fp) {
            throw new Exception("stream_socket_client fail errno={$errno} errstr={$errstr}");
        }
        fwrite($fp, $ctx.self::RPC_EOL);

        $res = '';
        while (!feof($fp)) {
            $tmp = stream_socket_recvfrom($fp, 1024);
            if ($pos = strpos($tmp, self::RPC_EOL)) {
                $res .= substr($tmp, 0, $pos);
                break;
            } else {
                $res .= $tmp;
            }
        }
        fclose($fp);
        return $res;
    }
}
