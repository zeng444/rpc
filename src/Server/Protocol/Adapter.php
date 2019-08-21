<?php

namespace Janfish\Rpc\Server\Protocol;


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
     * Author:Robert
     *
     * @param $bootstrap
     */
    public function registerBoostrap($bootstrap): void
    {
        $this->bootstrap = $bootstrap;
    }


    /**
     * Author:Robert
     *
     */
    public function runBootstrap()
    {
        $bootstrapCallback = $this->bootstrap;
        if (is_callable($bootstrapCallback)) {
            $this->event('workerstart', function ($server) use ($bootstrapCallback) {
                $bootstrapCallback($server);
            });
        }
    }

}
