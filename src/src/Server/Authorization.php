<?php

namespace Janfish\Rpc\Server;

/**
 * Authorization for RPC Server
 * Class Authorization
 * @package Janfish\Rpc\Server
 */
class Authorization
{
    /**
     *
     * @var
     */
    protected $appId;

    /**
     *
     * @var
     */
    protected $appSecret;

    /**
     *
     * @var
     */
    protected $called;

    /**
     *
     * @var
     */
    protected $signType;


    /**
     * Authorization constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (isset($options['id'])) {
            $this->appId = $options['id'];
        }
        if (isset($options['secret'])) {
            $this->appSecret = $options['secret'];
        }
        if (isset($options['signType'])) {
            $this->signType = $options['signType'];
        }
    }


    /**
     * Author:Robert
     *
     * @param $data
     * @return bool
     * @throws Exception
     */
    public function check($data): bool
    {
        $signType = $data['algorithm'] ?? 'sha1';
        if (!function_exists($signType)) {
            throw new Exception('sign type '.$signType.' not exits');
        }
        if(!isset($data['call']) || !isset($data['service'])){
            throw new Exception('params not exits');
        }
        $signature = $signType(sprintf('appId=%s&algorithm=%s&call=%s&secret=%s&service=%s&timestamp=%s', $this->appId, $signType, $data['call'], $this->appSecret, $data['service'], $data['timestamp']));
        return $signature === $data['signature'];
    }
}
