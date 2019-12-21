<?php

use Janfish\Rpc\Client;

include '../../vendor/autoload.php';
$services = require_once 'configs/services.php';


//单次调用
Client::init($services);
$data = Services\CallCenter\User\Profile::getById();
print_r($data.PHP_EOL);

//单次调用
$clientBatch = new Client\Batch($services);
$data = $clientBatch->call([
    'class' => 'Services\CallCenter\User\Profile',
    'method' => 'getById',
    'args' => ['1'],
]);
print_r($data.PHP_EOL);
//批量调用
$commands = [
    "user1" => [
        'class' => 'Services\CallCenter\User\Profile',
        'method' => 'getById',
        'args' => ['1'],
    ],
    "user2" => [
        'class' => 'Services\CallCenter\User\Profile',
        'method' => 'getById',
    ],
    "user3" => [
        'class' => '\Services\CallCenter\User\Profile',
        'method' => 'getById',
        'args' => ['2'],
    ],
];
//显式调用
$clientBatch = new Client\Batch($services);
$data = $clientBatch->call($commands);
print_r($data);
//隐式调用
$data = Services\Client::call($commands);
print_r($data);

