<?php

use Janfish\Rpc\Server as RpcServer;

define('ROOT_PATH', dirname(__DIR__).DIRECTORY_SEPARATOR);
define('CORE_PATH', ROOT_PATH.'core'.DIRECTORY_SEPARATOR);
define('LOG_PATH', ROOT_PATH.'logs'.DIRECTORY_SEPARATOR);

try {

    /**
     * Switch the configuration
     */
    $env = isset($_ENV['SITE_ENV']) ? strtolower($_ENV['SITE_ENV']) : 'prod';

    /**
     * Read the configuration
     */
    $config = include ROOT_PATH."configs/config.php";

    /**
     * Autoload Object
     */
    require_once '../../../vendor/autoload.php';

    /**
     * Autoload Object
     */
    include ROOT_PATH.'configs/autoload.php';


    $di = new  Phalcon\Di\FactoryDefault();

    /**
     * Include Services
     */
    include ROOT_PATH.'configs/services.php';

    $app = new RpcServer($config->service->toArray());

    $app->handle();
} catch (\Exception $e) {
    $errorMsg = $e->getMessage().'<br>'.'<pre>'.$e->getTraceAsString().'</pre>';
    if ($env === 'dev') {
        echo $errorMsg;
    } else {
        error_log('['.date('Y-m-d H:i:s').']'.$errorMsg, 3, LOG_PATH.'exception.log');
    }
}
