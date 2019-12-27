<?php

namespace Janfish\Rpc\Server\Task;

use Janfish\Rpc\Logger\File as Logger;
use Janfish\Rpc\Server\Exception;
use Janfish\Rpc\Server\Protocol\Adapter;

/**
 * 异步任务服务
 * Author:Robert
 *
 * Class Async
 * @package Janfish\Rpc\Server\Task
 * @method static call(string $class, string $method, array $args)
 */
class Async
{
    /**
     * Author:Robert
     *
     * @var Adapter
     */
    protected $server;

    /**
     * Author:Robert
     *
     * @var int
     */
    protected $taskWorkerNum = 0;

    /**
     * Author:Robert
     *
     * @var
     */
    protected $logFile;

    /**
     * Author:Robert
     *
     * @var
     */
    static private $instance;

    protected $logger;


    /**
     * Async constructor.
     * @param Adapter $server
     * @param array $options
     * @throws \Janfish\Rpc\Logger\Exception
     */
    public function __construct(Adapter $server, $options = [])
    {

        $this->server = $server;
        if (isset($options['task_worker_num'])) {
            $this->taskWorkerNum = $options['task_worker_num'];
        }
        if (isset($options['task_log_file'])) {
            $this->logFile = $options['task_log_file'];
        }
        if ($this->logFile) {
            $this->logger = new Logger(['logPath' => $this->logFile]);
        }

    }

    /**
     * Author:Robert
     *
     * @param Adapter|null $server
     * @param array|null $config
     * @return Async
     * @throws \Janfish\Rpc\Logger\Exception
     */
    static public function getInstance(Adapter $server = null, array $config = null)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($server, $config);
        }
        return self::$instance;
    }

    /**
     * Author:Robert
     *
     * @param $methodName
     * @param array $args
     * @return mixed
     * @throws Exception
     * @throws \Janfish\Rpc\Logger\Exception
     */
    public static function __callStatic($methodName, array $args)
    {
        $instance = self::getInstance();
        if ($methodName === 'call') {
            return $instance->task(...$args);
        }
        return $instance->$methodName(...$args);
    }


    /**
     * Author:Robert
     *
     * @param string $class
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws Exception
     */
    protected function task(string $class, string $method, array $args)
    {
        if ($this->taskWorkerNum < 1) {
            throw new Exception('task function is not config,plz set task_worker_num');
        }
        return $this->server->task(json_encode([$class, $method, $args]), false);
    }

    /**
     * Author:Robert
     *
     * @param string $class
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws Exception
     */
    protected function debug(string $class, string $method, array $args)
    {
        if ($this->taskWorkerNum < 1) {
            throw new Exception('task function is not config,plz set task_worker_num');
        }
        return $this->server->task(json_encode([$class, $method, $args]), true);
    }

    /**
     * Author:Robert
     *
     */
    public function handle()
    {
        if ($this->taskWorkerNum < 1) {
            return true;
        }

        $this->server->event('task', function (\Swoole\Server $server, $taskId, $fromId, $data) {
            list($class, $method, $args) = json_decode($data, true);
            if (!class_exists($class)) {
                throw new Exception("Class $class is not exist");
            }
            $instance = new $class();
            if (!method_exists($instance, $method)) {
                throw new Exception("Class $class is not exist");
            }
            $result = $instance->$method(...$args);
            $server->finish($result);
        });
        $logger = $this->logger;
        $this->server->event('finish', function (\Swoole\Server $server, $taskId, $data) use ($logger) {
            $msg = "Task#$taskId finished, ".json_encode($data);
            if ($logger) {
                $logger->debug($msg);
            } else {
                echo $msg.PHP_EOL;
            }
        });
        return true;
    }

}