<?php

namespace Janfish\Rpc;

use Janfish\Rpc\Server\Authorization;
use Janfish\Rpc\Server\Dispatcher;
use Janfish\Rpc\Server\Exception;

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
     * @var
     */
    protected $appId;

    protected $res;
    protected $config;

    /**
     *
     * @var
     */
    protected $appSecret;

    public $dispatch;

    const RPC_EOL = "\r\n";


    /**
     * Server constructor.
     * @param array $options
     * @param array $res
     * @throws Exception
     */
    public function __construct(array $options, array $res = [])
    {
        $this->config = $options;
        if (isset($options['id'])) {
            $this->appId = $options['id'];
        }
        if (isset($options['secret'])) {
            $this->appSecret = $options['secret'];
        }
        if (!$this->appId || !$this->appSecret) {
            throw new Exception('rpc client params error');
        }
        $this->res = $res ?: self::getHttpRequestRawBody();
        $this->dispatch = new Dispatcher($this->res);
    }


    /**
     * Author:Robert
     *
     * @return array|false|string
     */
    public static function getHttpRequestRawBody(): array
    {
        $raw = file_get_contents("php://input");
        $data = json_decode($raw, true);
        return $data ?: [];
    }

    /**
     * Author:Robert
     *
     * @param $data
     * @return string
     */
    public function end($data): string
    {
        return @json_encode($data).self::RPC_EOL;
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
     * @return bool
     * @throws Exception
     */
    public function checkSign(): bool
    {
        $authorization = new Authorization($this->config);
        return $authorization->check($this->res);
    }


    /**
     * Author:Robert
     *
     * @return string
     */
    public function handle()
    {
        try {
            if ($this->checkSign() === false) {
                return $this->end([
                    'data' => '',
                    'ok' => false,
                    'trace' => '',
                    'error' => 'Forbidden ',
                ]);
            }
            return $this->end([
                'data' => $this->dispatch->run(),
                'ok' => true,
                'trace' => '',
                'error' => '',
            ]);
        } catch (Exception $e) {
            return $this->end([
                'data' => '',
                'ok' => false,
                'trace' => $e->getTraceAsString(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
