<?php

namespace Janfish\Rpc\Server;

use Janfish\Rpc\Logger\File as FileLogger;
use Janfish\Rpc\Server\Router\BatchDispatcher;
use Janfish\Rpc\Server\Router\Dispatcher;

/**
 * Author:Robert
 *
 * Class Server
 * @package Janfish\Rpc
 */
class Router
{

    /**
     *
     * @var
     */
    protected $appId;

    /**
     * Author:Robert
     *
     * @var mixed
     */
    protected $service;

    /**
     *
     * @var
     */
    protected $req;

    /**
     * @var
     */
    protected $config;

    /**
     *
     * @var
     */
    protected $appSecret;

    /**
     * @var Authorization
     */
    protected $authorization;

    /**
     * @var string
     */
    protected $logPath;

    /**
     *
     * @var
     */
    public $dispatch;


    public static $logger;

    /**
     *
     * @var
     */
    const RPC_EOL = "\r\n";

    /**
     * Router constructor.
     * @param array $options
     * @param string $req
     * @param string $logPath
     * @throws Exception
     * @throws \Janfish\Rpc\Logger\Exception
     */
    public function __construct(array $options, string $req, string $logPath = '')
    {
        $this->config = $options;
        if (isset($options['name'])) {
            $this->service = $options['name'];
        }
        if (isset($options['id'])) {
            $this->appId = $options['id'];
        }
        if (isset($options['secret'])) {
            $this->appSecret = $options['secret'];
        }
        if (!$this->service) {
            throw new Exception('Your service has no name');
        }
        if (!$this->appId || !$this->appSecret) {
            throw new Exception('Request params error 401');
        }
        if ($logPath) {
            $this->logPath = $logPath;
        }
        $this->writeLog(trim($req));
        $this->req = @json_decode($req, true);
        if (!$this->req) {
            throw new Exception('Request data error 400');
        }
        $this->authorization = new Authorization($this->config);
        if (isset($this->req['batch'])) {
            $this->dispatch = new BatchDispatcher($this->req);
        } else {
            $this->dispatch = new Dispatcher($this->req);
        }
    }

    /**
     * Author:Robert
     *
     * @param string $msg
     * @throws \Janfish\Rpc\Logger\Exception
     */
    public function writeLog(string $msg): void
    {
        if ($this->logPath) {
            if (!self::$logger) {
                self::$logger = new FileLogger(['logPath' => $this->logPath]);
            }
            self::$logger->debug($msg);
        }
    }

    /**
     * Author:Robert
     *
     * @param array $data
     * @param string $endChar
     * @return string
     * @throws \Janfish\Rpc\Logger\Exception
     */
    public function response(array $data, string $endChar = self::RPC_EOL): string
    {
        $res = @json_encode($data);
        $this->writeLog($res);
        return @$res.$endChar;
    }

    /**
     * Author:Robert
     *
     * @param $loader
     */
    public function registerLoader($loader): void
    {
        $this->dispatch->registerAutoLoad($loader);
    }

    /**
     * Author:Robert
     *
     * @param $method
     */
    public function afterInstancedService($method): void
    {
        $this->dispatch->afterInstanced($method);
    }

    /**
     * Author:Robert
     *
     * @param $method
     */
    public function afterFindOutService($method): void
    {
        $this->dispatch->afterFindOut($method);
    }


    /**
     * Author:Robert
     *
     * @return string
     * @throws \Janfish\Rpc\Logger\Exception
     */
    public function handle(): string
    {
        try {
            if ($this->authorization->check($this->req) === false) {
                return $this->response([
                    'data' => '',
                    'ok' => false,
                    'trace' => '',
                    'error' => 'Forbidden ',
                ]);
            }
            return $this->response([
                'data' => $this->dispatch->run(),
                'ok' => true,
                'trace' => '',
                'error' => '',
            ]);
        } catch (Exception $e) {
            return $this->response([
                'data' => '',
                'ok' => false,
                'trace' => $e->getTraceAsString(),
                'error' => $e->getMessage(),
            ]);
        }
        //        catch (\Exception $e) {
        //            return $this->response([
        //                'data' => '',
        //                'ok' => false,
        //                'trace' => $e->getTraceAsString(),
        //                'error' => $e->getMessage(),
        //            ]);
        //        }
    }
}
