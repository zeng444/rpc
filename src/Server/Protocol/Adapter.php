<?php

namespace Janfish\Rpc\Server\Protocol;

use Janfish\Rpc\Server\Exception;

/**
 * Author:Robert
 *
 * Class Adapter
 * @package Janfish\Rpc\Server\Protocal
 */
abstract class Adapter
{


    /**
     * @var mixed|string
     */
    protected $host;

    /**
     * @var int|mixed
     */
    protected $port = 9501;

    /**
     *
     * @var
     */
    protected $pidFile;

    /**
     * Author:Robert
     *
     * @var
     */
    protected $bootstrap;

    /**
     * Author:Robert
     *
     * @var
     */
    protected $request;

    /**
     * Author:Robert
     *
     * @var
     */
    protected $server;

    /**
     * Author:Robert
     *
     * @return bool
     */
    abstract function start(): bool;

    /**
     * Author:Robert
     *
     * @var
     */
    private static $instance;


    /**
     * Adapter constructor.
     * @param array $options
     */
    abstract function __construct(array $options = []);

    /**
     * Author:Robert
     *
     * @param array $options
     * @return mixed
     */
    public static function getServer(array $options = [])
    {
        if (!self::$instance) {
            $className = get_called_class();
            self::$instance = new $className($options);
        }
        return self::$instance;
    }


    /**
     * Author:Robert
     *
     * @return bool
     */
    abstract function create(): bool;


    /**
     * Author:Robert
     *
     * @param string $event
     * @param $method
     */
    public function event(string $event, $method)
    {
        $this->server->on($event, $method);
    }

    /**
     * Author:Robert
     *
     * @param array $setting
     */
    public function set(array $setting = []): void
    {
        if ($this->pidFile) {
            $setting['pid_file'] = $this->pidFile;
        }
        $this->server->set($setting);
    }


    /**
     * Author:Robert
     *
     * @return bool
     */
    public function reload(): bool
    {
        if (!$pid = $this->isRunning()) {
            return false;
        }
        return \Swoole\Process::kill($pid, SIGUSR1);
    }

    /**
     * Author:Robert
     *
     * @return bool
     */
    public function isTaskWorker(): bool
    {
        return $this->server->taskworker;
    }

    /**
     * Author:Robert
     *
     * @return int
     */
    public function getWorkerPid(): int
    {
        return $this->server->worker_pid;
    }

    /**
     * Author:Robert
     *
     * @return int
     */
    public function getWorkerId(): int
    {
        return $this->server->worker_id;
    }

    /**
     * Author:Robert
     *
     * @return int
     */
    public function getManagerPid(): int
    {
        return $this->server->manager_pid;
    }

    /**
     * Author:Robert
     *
     * @return int
     */
    public function getMasterPid(): int
    {
        return $this->server->master_pid;
    }


    /**
     * Author:Robert
     *
     * @return array
     */
    public function getSetting(): array
    {
        return $this->server->setting;
    }

    /**
     * Author:Robert
     *
     * @return array
     */
    public function getPorts()
    {
        return $this->server->ports;
    }

    /**
     * Author:Robert
     *
     * @return array
     */
    public function getConnections()
    {
        return $this->server->connections;
    }



    /**
     * Author:Robert
     *
     * @return bool
     */
    public function restart(): bool
    {
        if (!$pid = $this->stop()) {
            return false;
        }
        sleep(1);
        return $this->start();
    }

    /**
     * Author:Robert
     *
     * @return bool
     */
    public function stop(): bool
    {
        if (!$pid = $this->isRunning()) {
            return false;
        }
        return \Swoole\Process::kill($pid, SIGTERM);
    }

    /**
     * Author:Robert
     *
     * @return int
     */
    public function isRunning(): int
    {
        $pid = $this->getPid();
        if (!$pid) {
            return 0;
        }
        if (!\Swoole\Process::kill($pid, 0)) {
            return 0;
        }
        return $pid;
    }

    /**
     * Author:Robert
     *
     * @return int
     */
    public function getPid(): int
    {
        if (!is_readable($this->pidFile)) {
            return 0;
        }
        $pid = @file_get_contents($this->pidFile);
        if (!$pid) {
            return 0;
        }
        return $pid;
    }

    /**
     * Author:Robert
     *
     * @param $request
     */
    public function registerRequest($request): void
    {
        $this->request = $request;
    }

    /**
     * 创建task
     * Author:Robert
     *
     * @param string $data
     * @param bool $force
     * @return mixed
     */
    public function task(string $data, $force = false)
    {
        if ($force) {
            return $this->server->taskwait($data);
        }
        return $this->server->task($data);
    }


    /**
     * Author:Robert
     *
     * @param $bootstrap
     */
    public function registerBootstrap($bootstrap): void
    {
        $this->bootstrap = $bootstrap;
    }


    /**
     * Author:Robert
     *
     * @throws Exception
     */
    public function runBootstrap()
    {
        if ($this->pidFile && !is_writable(dirname($this->pidFile))) {
            throw new Exception('Pid File '.$this->pidFile.' can\'t create');
        }
        $bootstrapCallback = $this->bootstrap;
        if (is_callable($bootstrapCallback)) {
            $this->event('workerstart', function ($server) use ($bootstrapCallback) {
                $bootstrapCallback($server);
            });
        }
    }


}
