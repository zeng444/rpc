<?php

namespace Janfish\Rpc\Server\Protocol;

use Swoole\Server as SwooleServer;
use Janfish\Rpc\Server\Exception;

/**
 * Author:Robert
 *
 * Class Tcp
 * @package Core\Server
 */
class Tcp extends Adapter
{

    /**
     * @var SwooleServer
     */
    protected $server;

    /**
     * Author:Robert
     *
     * @var
     */
    protected $pidFile = '/tmp/tcp.server.pid';


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
     * @var int
     */
    protected $mode = SWOOLE_PROCESS;

    /**
     *
     */
    const PROTOCOL_NAME = 'Tcp';

    /**
     * Author:Robert
     *
     * @var
     */
    protected $app;

    /**
     * Socket constructor.
     * @param array $option
     * @param int $mode
     */
    public function __construct($option = [], int $mode = SWOOLE_PROCESS)
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
        $this->mode = $mode;
    }

    /**
     * Author:Robert
     *
     */
    public function create(): bool
    {
        if ($this->isRunning()) {
            return false;
        }
        $this->server = new SwooleServer($this->host, $this->port, $this->mode);
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
        $callback = $this->bootstrap;
        if (!is_callable($callback)) {
            throw new Exception('启动信息不可用');
        }
        $this->event('receive', function (SwooleServer $server, $fd, $reactor_id, $data) use ($callback) {
            $server->send($fd, $callback($data));
        });
        return $this->server->start();
    }
}
