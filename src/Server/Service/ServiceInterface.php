<?php

namespace Janfish\Rpc\Server\Service;

/**
 * Author:Robert
 *
 * Interface ServiceInterface
 */
interface  ServiceInterface
{

    /**
     * 被远程调用必须继承的方法
     */
    public function init();
}