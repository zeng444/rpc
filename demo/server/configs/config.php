<?php

return new \Phalcon\Config([
    'logger' => [
        'rpc' => ROOT_PATH.'logs/rpc.log',
    ],
    'database' => [
        'adapter' => 'Mysql',
        'host' => 'mysql',
        'port' => '3306',
        'username' => 'root',
        'password' => 'root',
        'dbname' => 'test',
        'charset' => 'utf8',
        'options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
        ],
    ],
]);
