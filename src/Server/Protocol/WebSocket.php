<?php

namespace Janfish\Rpc\Server\Protocol;

use Janfish\Rpc\Server\Exception;
use Swoole\WebSocket\Server as SwooleServer;

/**
 * Author:Robert
 *
 * Class Tcp
 * @property WebSocketServer $server
 * @package Core\Server
 */
class WebSocket extends Adapter
{


    /**
     * @var mixed|string
     */
    protected $host = '0.0.0.0';

    /**
     * @var int|mixed
     */
    protected $port = 9501;

    /**
     * Author:Robert
     *
     * @var
     */
    protected $server;

    /**
     * Author:Robert
     *
     * @var string
     */
    protected $pidFile = '/tmp/ws.server.pid';

    /**
     *
     */
    const PROTOCOL_NAME = 'WebSocket';


    /**
     * WebSocket constructor.
     * @param array $option
     */
    public function __construct(array $option = [])
    {
        if (isset($option['host'])) {
            $this->host = $option['host'];
        }
        if (isset($option['port'])) {
            $this->port = $option['port'];
        }
        if (isset($option['pid_file'])) {
            $this->pidFile = $option['pid_file'];
        }
    }

    /**
     * Author:Robert
     *
     * @return bool
     */
    public function create(): bool
    {
//        if ($this->isRunning()) {
//            return false;
//        }
        $this->server = new SwooleServer($this->host, $this->port);
        return true;
    }


    /**
     * Author:Robert
     *
     * @return bool
     * @throws Exception
     */
    public function start(): bool
    {
        $this->runBootstrap();
        $callback = $this->request;
        if (!is_callable($callback)) {
            throw new Exception('启动信息不可用');
        }
        $this->event('message', function (WebSocketServer $server, $frame) use ($callback) {
            $server->push($frame->fd, $callback($frame->data));
        });
        return $this->server->start();
    }
}
