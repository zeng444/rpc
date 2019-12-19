<?php

use Janfish\Rpc\Client;
use Janfish\Rpc\Client\Exception;

include '../../vendor/autoload.php';
$services = require_once 'configs/services.php';

try {
    Client::init($services);

//    $caller = new Client\Caller($services);
//    $caller->batch([
//        [
//            'class' => 'Services\CallCenter\User\Profile',
//            'method' => 'getById',
//            'args' => [],
//        ],
//        [
//            'call' => 'Services\CallCenter\User\Profile',
//            'args' => [],
//        ],
//    ]);

        $data = Services\CallCenter\User\Profile::getById();
        var_dump($data);
} catch (Exception  $e) {
    echo $e->getMessage();
}
