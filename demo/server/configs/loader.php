<?php

use Phalcon\Loader;

$loader = new Loader();
$loader->registerNamespaces([
    'Services' => ROOT_PATH.'services/',
]);
$loader->register();