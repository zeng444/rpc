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
     * @param string $type
     * @param $initCallback
     * @return bool
     * @throws Exception
     */
    public static function start(array $config, array $serviceConfigs = [], string $type = Tcp::PROTOCOL_NAME, $initCallback = ''): bool
    {
        \Swoole\Coroutine::set([
            'max_coroutine' => self::MAX_COROUTINE,
        ]);
        $serverConfig = $config['server'] ?? [];
        $server = self::createServer($serverConfig, ucfirst($type));
        if (!call_user_func([$server, 'create'])) {
            return false;
        }
        $options = self::defaultOptions();
        if (isset($config['options'])) {
            $options = array_merge(self::defaultOptions(), $config['options']);
        }
        $server->set($options);
        $server->registerBoostrap(function () use ($initCallback, $server) {
            if (is_callable($initCallback)) {
                $initCallback($server);
            }
        });
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
     * @param string $type
     * @return bool
     * @throws Exception
     */
    public static function stop(array $serverConfig, string $type = Tcp::PROTOCOL_NAME): bool
    {
        return (self::createServer($serverConfig, ucfirst($type)))->stop();
    }

    /**
     * Author:Robert
     *
     * @param array $serverConfig
     * @param string $type
     * @return bool
     * @throws Exception
     */
    public static function reload(array $serverConfig, string $type = Tcp::PROTOCOL_NAME): bool
    {
        return (self::createServer($serverConfig, ucfirst($type)))->reload();
    }


    /**
     * Author:Robert
     *
     * @param array $serverConfig
     * @param array $serviceConfigs
     * @param string $type
     * @return bool
     * @throws Exception
     */
    public static function restart(array $serverConfig, array $serviceConfigs = [], string $type = Tcp::PROTOCOL_NAME): bool
    {
        $type = ucfirst($type);
        if (!(self::createServer($serverConfig, $type))->stop()) {
            return false;
        }
        sleep(self::RESTART_SLEEP_TIME);
        return self::start($serverConfig, $serviceConfigs, $type);
    }

    /**
     * Author:Robert
     *
     * @param array $serverConfig
     * @param string $type
     * @return bool|Server\Protocol\Tcp
     * @throws Exception
     */
    private static function createServer(array $serverConfig, string $type = Tcp::PROTOCOL_NAME)
    {
        $type = 'Janfish\\Rpc\\Server\\Protocol\\'.$type;
        if (!class_exists($type)) {
            throw  new Exception('不存在的服务器协议');
        }
        $server = new $type($serverConfig);
        if (!is_subclass_of($server, 'Janfish\\Rpc\\Server\\Protocol\\Adapter')) {
            throw  new Exception('所属服务器协议不合法');
        }
        return $server;
    }
}
