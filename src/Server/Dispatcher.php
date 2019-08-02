<?php

namespace Janfish\Rpc\Server;

/**
 * Dispatch RPC request
 * Author:Robert
 *
 * Class Dispatcher
 * @package Janfish\Rpc\Server
 */
class Dispatcher
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var int
     */
    protected $service;

    /**
     * @var int
     */
    protected $call;

    /**
     * @var array
     */
    protected $args;


    public function __construct($data)
    {
        list($this->call, $this->service, $this->args) = [$data['call'], $data['service'], $data['args']];
    }


    /**
     * Get service name
     * Author:Robert
     *
     * @return string
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * Get called method of service
     * Author:Robert
     *
     * @return array
     */
    public function getCall(): array
    {
        return explode('::', $this->call);
    }

    /**
     *  Get called parameter of method
     * Author:Robert
     *
     * @return array
     */
    public function getArg(): array
    {
        return $this->args;
    }


    /**
     * Author:Robert
     *
     * @return mixed
     * @throws Exception
     */
    public function run()
    {
        $called = $this->getCall();
        list($className, $methodName) = $called;
        if (!$className || !class_exists($className)) {
            throw new Exception(sprintf('class %s not exist', $className));
        }
        $instance = new $className();
        if (!method_exists($instance, $methodName)) {
            throw new Exception(sprintf('method %s::%s not exist', $className, $methodName));
        }
        return call_user_func_array([$instance, $methodName], $this->getArg());
    }

    /**
     * Author:Robert
     *
     * @param $loader
     */
    public function registerAutoLoad($loader): void
    {
        static $rpcLoaderInit = false;
        if (!$rpcLoaderInit) {
            spl_autoload_register($loader);
            $rpcLoaderInit = true;
        }
    }
}
