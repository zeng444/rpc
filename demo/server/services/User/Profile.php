<?php

namespace Services\User;

use Janfish\Rpc\Server\Exception;
use Services\BaseDemo;

class Profile extends BaseDemo
{

    public function getById($str=''): string
    {
        if($str=="killer"){
            throw new  Exception("这次SB了");
        }
        return "Hello World,$str";
    }
}
