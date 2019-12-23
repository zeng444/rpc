<?php

namespace Janfish\Rpc;

use Janfish\Rpc\Server\Exception;
use Janfish\Rpc\Server\Protocol\Tcp;

/**
 * Author:Robert
 *
 * Class Server
 * @package Janfish\Rpc
 */
class Server
{

    /**
     *
     */
    const RESTART_SLEEP_TIME = 1;

    /**
     * MAX_COROUTINE
     */
    const MAX_COROUTINE = 300000;

    /**
     * Author:Robert
     *
     * @return array
     */
    public static function defaultOptions(): array
    {
        return [
            'open_eof_check' => true,
            'package_eof' => "\r\n",
            'open_eof_split' => false,
        ];
    }


    /**
     * Author:Robert
     *
     * @param array $config
     * @param array $serviceConfigs
     * @param string $protocol
     * @param string $initCallback
     * @return bool
     * @throws Exception
     * @throws Logger\Exception
     */
    public static function start(array $config, array $serviceConfigs = [], string $protocol = Tcp::PROTOCOL_NAME, $initCallback = ''): bool
    {
        \Swoole\Coroutine::set([
            'max_coroutine' => self::MAX_COROUTINE,
        ]);
        $serverConfig = $config['server'] ?? [];
        $server = self::createServer($serverConfig, ucfirst($protocol));
        if (!call_user_func([$server, 'create'])) {
            return false;
        }
        $options = self::defaultOptions();
        if (isset($config['options'])) {
            $options = array_merge(self::defaultOptions(), $config['options']);
        }
        $server->set($options);
        //注册服务启动
        $server->registerBootstrap(function () use ($initCallback, $server) {
            if (is_callable($initCallback)) {
                $initCallback($server);
            }
        });
        //注册异步任务
        $task = Server\Task\Async::getInstance($server, [
            'task_worker_num' => $config['options']['task_worker_num'] ?? 0,
            'task_log_file' => $config['server']['task_log_file'] ?? '',
        ]);
        $task->handle();

        //注册数据包接收
        $server->registerRequest(function ($req) use ($serverConfig, $serviceConfigs) {
            $router = new Server\Router($serviceConfigs, $req, $serverConfig['log_file'] ?? '');
            $router->afterFindOutService(function ($service) {
                $service[0] = ($serverConfig['servicePrefix'] ?? 'Services\\').$service[0];
                return $service;
            });
            $router->afterInstancedService(function ($instance) {
                if (method_exists($instance, "init")) {
                    $instance->init();
                }
            });
            return $router->handle();
        });
        return $server->start();
    }

    /**
     * Author:Robert
     *
     * @param array $serverConfig
     * @param string $protocol
     * @return bool
     * @throws Exception
     */
    public static function stop(array $serverConfig, string $protocol = Tcp::PROTOCOL_NAME): bool
    {
        return self::createServer($serverConfig['server'] ?? [], ucfirst($protocol))->stop();
    }

    /**
     * Author:Robert
     *
     * @param array $serverConfig
     * @param string $protocol
     * @return bool
     * @throws Exception
     */
    public static function reload(array $serverConfig, string $protocol = Tcp::PROTOCOL_NAME): bool
    {
        return (self::createServer($serverConfig['server'] ?? [], ucfirst($protocol)))->reload();
    }


    /**
     * Author:Robert
     *
     * @param array $serverConfig
     * @param array $serviceConfigs
     * @param string $protocol
     * @param string $initCallback
     * @return bool
     * @throws Exception
     * @throws Logger\Exception
     */
    public static function restart(array $serverConfig, array $serviceConfigs = [], string $protocol = Tcp::PROTOCOL_NAME, $initCallback = ''): bool
    {
        $server = ucfirst($protocol);
        if (!(self::stop($serverConfig, $server))) {
            return false;
        }
        sleep(self::RESTART_SLEEP_TIME);
        return self::start($serverConfig, $serviceConfigs, $server, $initCallback);
    }

    /**
     * Author:Robert
     *
     * @param string $protocol
     * @return mixed
     */
    public static function getServer(string $protocol = Tcp::PROTOCOL_NAME)
    {
        return ('Janfish\\Rpc\\Server\\Protocol\\'.ucfirst($protocol))::getServer();
    }

    /**
     * Author:Robert
     *
     * @param array $serverConfig
     * @param string $protocol
     * @return mixed
     * @throws Exception
     */
    private static function createServer(array $serverConfig, string $protocol = Tcp::PROTOCOL_NAME)
    {
        $server = 'Janfish\\Rpc\\Server\\Protocol\\'.$protocol;
        if (!class_exists($server)) {
            throw  new Exception('不存在的服务器协议');
        }
        $server = $server::getServer($serverConfig);
        if (!is_subclass_of($server, 'Janfish\\Rpc\\Server\\Protocol\\Adapter')) {
            throw  new Exception('所属服务器协议不合法');
        }
        return $server;
    }
}
