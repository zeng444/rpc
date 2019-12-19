<?php

namespace Janfish\Rpc\Client;

use Janfish\Rpc\Client\Protocol\ClientInterface;
use Janfish\Rpc\Client\Protocol\Http;
use Janfish\Rpc\Client\Protocol\Exception;
use Janfish\Rpc\Client\Protocol\Socket;

/**
 * Author:Robert
 *
 * Class Caller
 * @package Janfish\Rpc\Client
 */
class Caller
{


    /**
     * Author:Robert
     *
     * @var array
     */
    private $_config;

    public static $servicePrefix = 'Services\\';

    /**
     * Caller constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;
    }

    /**
     * Author:Robert
     *
     * @param array $commands
     */
    public function batch(array $commands)
    {
        $className = 'Services\CallCenter\User\Profile';
        $config = $this->_config;
        $guess = explode('\\', preg_replace('/^'.preg_quote(self::$servicePrefix).'/', '', $className));
        $serviceName = $guess[0];
        $className = implode('\\', array_slice($guess, 1));
        print_r([$serviceName, $className]);
        die();
        echo $this->make($commands, $config['id'], $config['secret'], $config['signType'] ?? 'sha1');
        //        return $this->parse((self::getClient($this->_config))->remoteCall($this->make($commands, $config['id'], $config['secret'], $config['signType'] ?? 'sha1')));

    }

    /**
     * Author:Robert
     *
     * @param array $config
     * @return ClientInterface
     */
    public static function getClient(array $config = []): ClientInterface
    {
        $host = $config['host'] ?? '';
        if (preg_match('/^http/i', $host)) {
            return new Http($config);
        } elseif (preg_match('/^tcp/i', $host)) {
            return new Socket($config);
        } else {
            return new Socket($config);
        }
    }

    /**
     * Make Request and generate signature
     * Author:Robert
     *
     * @param array $commands
     * @param string $id
     * @param string $secret
     * @param string $signType
     * @return string
     */
    protected function make(array $commands, string $id, string $secret, string $signType = 'sha1'): string
    {
        $ctx = [
            'algorithm' => $signType,
            'appId' => $id,
            'timestamp' => microtime(true),
            'batch' => $commands,
        ];
        $call = implode(',', array_column($commands, 'call'));
        //sort by dict
        $ctx['signature'] = $signType(sprintf('appId=%s&algorithm=%s&call=%s&secret=%s&service=%s&timestamp=%s', $id, $signType ?: 'sha1', $call, $secret, $ctx['service'], $ctx['timestamp']));

        $ctx = [
            "algorithm" => 'sha1',
            "appId" => '72928888',
            "service" => 'CallCenter',
            //    "call" => 'User\Profile::getById',
            //    "args" => [],
            "timestamp" => '123123123123',
            "signature" => '4fe3f2640608a55d14f9630eb476a1cea6d9b9da',
            "batch" => [
                [
                    "call" => 'User\Profile::getById',
                    "args" => ['robert'],
                ],
                [
                    "call" => 'User\Profile::getById',
                    "args" => ['kille2r'],
                ],
            ],
        ];
        return json_encode($ctx);
    }

    /** parse response data
     * Author:Robert
     *
     * @param string $res
     * @return array
     * @throws Exception
     */
    protected function parse(string $res)
    {
        $ctx = json_decode($res, true);
        if (isset($ctx['ok']) && $ctx['ok']) {
            return $ctx['data'];
        }
        $message = $ctx['error'];
        if (isset($ctx['trace']) && $ctx['trace']) {
            $message .= "\n{$ctx['trace']}";
        }
        throw new Exception($message ?: "exception data:".$res);
    }
}

