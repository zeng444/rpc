<?php

namespace Services\User;

use Janfish\Rpc\Server\Exception;
use Services\BaseDemo;
use Janfish\Rpc\Server\Task\Async;

class Profile extends BaseDemo
{

    public function getById($str = ''): string
    {
        Async::call('\Services\User\Profile', 'test', ["why"]);
        if ($str == "killer") {
            throw new  Exception("这次SB了");
        }
        return "Hello World,$str";
    }

    public function test($str)
    {

        return "娃哈哈";
    }
}