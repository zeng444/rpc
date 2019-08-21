<?php

use \Janfish\Rpc\Server;

define('ROOT_PATH', dirname(__DIR__).DIRECTORY_SEPARATOR);
define('CORE_PATH', ROOT_PATH.'core'.DIRECTORY_SEPARATOR);
define('LOG_PATH', ROOT_PATH.'logs'.DIRECTORY_SEPARATOR);

try {

    /**
     * Autoload Object
     */
    require_once ROOT_PATH.'vendor/autoload.php';


    $env = isset($_ENV['SITE_ENV']) ? strtolower($_ENV['SITE_ENV']) : 'prod';


    /**
     * Include RPC configuration
     */
    $serverConfig = include ROOT_PATH."configs/rpc.php";

    /**
     * Include RPC services configuration
     */
    $servicesConfig = include ROOT_PATH."configs/services.php";

    $command = $argv[1] ?? 'start';

    $type = $argv[2] ?? Server\Protocol\Tcp::PROTOCOL_NAME;

    if (in_array($command, ['start', 'restart'])) {

        Server::$command($serverConfig, $servicesConfig, $type, function () use ($env) {
            /**
             * Autoload Object
             */
            include ROOT_PATH.'configs/loader.php';

            /**
             * Include RPC services configuration
             */
            include ROOT_PATH.'configs/components.php';
        });

    } elseif (in_array($command, ['reload', 'start', 'stop'])) {
        Server::$command($serverConfig, $type);
    }

} catch (\Exception $e) {
    $errorMsg = $e->getMessage().'<br>'.'<pre>'.$e->getTraceAsString().'</pre>';
    if ($env === 'dev') {
        echo $errorMsg;
    } else {
        error_log('['.date('Y-m-d H:i:s').']'.$errorMsg, 3, LOG_PATH.'exception.log');
    }
}
