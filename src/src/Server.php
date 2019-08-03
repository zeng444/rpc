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

    /**
     *
     * @var
     */
    protected $appSecret;

    public $dispatch;


    /**
     * Server constructor.
     * @param array $options
     * @throws Exception
     */
    public function __construct(array $options)
    {
        if (isset($options['id'])) {
            $this->appId = $options['id'];
        }
        if (isset($options['secret'])) {
            $this->appSecret = $options['secret'];
        }
        if (!$this->appId || !$this->appSecret) {
            throw new Exception('rpc client params error');
        }
        $this->res = $this->getRequestRawBody();
        $this->checkSign($options);
        $this->dispatch = new Dispatcher($this->res);
    }


    /**
     * Author:Robert
     *
     * @return array|false|string
     */
    public function getRequestRawBody(): array
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
        header('Content-Type: application/json; charset=utf-8');
        echo @json_encode($data);
        exit;
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
     * @param array $config
     * @throws Exception
     */
    public function checkSign(array $config): void
    {
        $authorization = new Authorization($config);
        if ($authorization->check($this->res) === false) {
            $this->end([
                'data' => '',
                'ok' => false,
                'trace' => '',
                'error' => 'Forbidden ',
            ]);
        }
    }


    /**
     * Author:Robert
     *
     */
    public function handle()
    {
        try {
            $this->end([
                'data' => $this->dispatch->run(),
                'ok' => true,
                'trace' => '',
                'error' => '',
            ]);
        } catch (Exception $e) {
            $this->end([
                'data' => '',
                'ok' => false,
                'trace' => $e->getTraceAsString(),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
