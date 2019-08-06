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
     * @var
     */
    protected $timeout;


    /**
     * Author:Robert
     *
     * @var mixed
     */
    public $host = 'tcp://127.0.0.1:8989';


    const RPC_EOL = "\r\n";

    /**
     * Socket constructor.file_get_contents timeout
     * @param string $host
     * @param int $timeout
     */
    public function __construct(string $host, int $timeout = 2)
    {
        $this->host = $host;
        $this->timeout = $timeout;
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
        $fp = stream_socket_client($this->host, $errno, $errstr, $this->timeout);
        if (!$fp) {
            throw new Exception("stream_socket_client fail errno={$errno} errstr={$errstr}");
        }
        fwrite($fp, $ctx.self::RPC_EOL);
        stream_set_timeout($fp, $this->timeout);
        $res = '';
        while (!feof($fp)) {
            $tmp = fgets($fp, 1024);
            $info = stream_get_meta_data($fp);
            if ($info['timed_out']) {
                throw new Exception("stream_socket_client timeout");
            }
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
