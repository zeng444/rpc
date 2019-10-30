<?php


namespace Janfish\Rpc\Server\Protocol;

use Janfish\Rpc\Server\Exception;
use Swoole\Http\Server as SwooleServer;

/**
 * Author:Robert
 *
 * Class Http
 * @package Core\Rpc\Server
 */
class Http extends Adapter
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
    protected $pidFile = '/tmp/http.server.pid';


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
     * Author:Robert
     *
     * @var
     */
    protected $app;

    /**
     *
     */
    const PROTOCOL_NAME = 'Http';

    /**
     * Socket constructor.
     * @param array $option
     */
    public function __construct($option = [])
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
        if ($this->isRunning()) {
            return false;
        }
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
        $this->event('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use ($callback) {
            $response->end($callback($request->rawContent()));
        });
        return $this->server->start();
    }
}
