<?php

return new \Phalcon\Config([
    'logger' => [
        'file' => ROOT_PATH.'logs/debug.log',
        'rpc' => ROOT_PATH.'logs/rpc.log',
    ],
    'queue' => [
        "host" => "beanstalkd",
        "port" => "11300",
    ],
    'cache' => [
        'host' => 'redis',
        'port' => '6379',
        'persistent' => false,
        //      'auth'=>'root',
        'index' => 21,
        'lifetime' => 172800,
    ],
    'database' => [
        'adapter' => 'Mysql',
        //        'host' => '192.168.10.112',
        'host' => '192.168.10.191',
        'port' => '3308',
        //                'host' => 'mysql',
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'car_insurance_genius_v2',
        //        'dbname' => 'china_coal_insurance',
        'charset' => 'utf8',
        'options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
        ],
    ],
]);
