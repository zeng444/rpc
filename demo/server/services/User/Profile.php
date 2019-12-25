<?php

namespace Services\User;

use Janfish\Rpc\Server;
use Janfish\Rpc\Server\Exception;
use Services\BaseDemo;
use Janfish\Rpc\Server\Task\Async;

class Profile extends BaseDemo
{

    public function getById($str = ''): string
    {
        var_dump((Server::getServer())->isTaskWorker());
        var_dump((Server::getServer())->getWorkerPid());
        var_dump((Server::getServer())->getSetting());
        var_dump((Server::getServer())->getMasterPid());
        var_dump((Server::getServer())->getWorkerId());
        var_dump((Server::getServer())->getManagerPid());
//        var_dump((Server::getServer())->getPorts());
//        Async::call('\Services\User\Profile', 'test', ["why"]);
        if ($str == "killer") {
            throw new  Exception("这次SB了");
        }
        return "Hello World,$str";
    }
}