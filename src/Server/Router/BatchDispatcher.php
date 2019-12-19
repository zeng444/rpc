<?php

namespace Janfish\Rpc\Server\Router;


use Janfish\Rpc\Server\Exception;


class BatchDispatcher
{

    /**
     * Author:Robert
     *
     * @var array
     */
    protected $callData = [];

    /**
     * Author:Robert
     *
     * @var
     */
    protected $afterInstanced;

    /**
     * Author:Robert
     *
     * @var
     */
    protected $afterFindOut;

    /**
     * Dispatcher constructor.
     * @param $data
     * @throws Exception
     */
    public function __construct($data)
    {
        if (!isset($data['batch']) || !is_array($data['batch'])) {
            throw new Exception('参数错误');
        }

        $this->callData = $data['batch'];
    }


    /**
     * Author:Robert
     *
     * @throws Exception
     */
    public function run()
    {
        $result = [];
        foreach ($this->callData as $data) {
            $dispatch = new Dispatcher($data);
            $dispatch->afterFindOut($this->afterFindOut);
            $dispatch->afterInstanced($this->afterInstanced);
            $result[] = $dispatch->run();
        }
        return $result;
    }

    /**
     * Author:Robert
     *
     * @param $method
     */
    public function afterInstanced($method): void
    {
        $this->afterInstanced = $method;
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

    /**
     * Author:Robert
     *
     * @param $method
     */
    public function afterFindOut($method): void
    {
        $this->afterFindOut = $method;
    }

}