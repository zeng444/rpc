<?php

use Janfish\Rpc\Client;
use Janfish\Rpc\Client\Exception;

include '../../vendor/autoload.php';
$services = require_once 'configs/services.php';

try {
    Client::init($services);
    $user = new Services\CallCenter\Services\User\Profile();
    $data = $user->getById(12);
    var_dump($data);
} catch (Exception  $e) {
    echo $e->getMessage();
}
