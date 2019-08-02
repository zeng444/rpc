<?php
/**
 * Services are globally registered in this file
 *
 * @var \Phalcon\Config $config
 */

use Phalcon\Cache\Frontend\Data as FrontData;
use Phalcon\Cache\Backend\Redis as BackendCache;
use Phalcon\Logger\Adapter\File as FileAdapter;
use Phalcon\Mvc\Model\MetaData\Redis as MetaDataCache;

/**
 * Sets the config
 */
$di->setShared('config', function () use ($config) {
    return $config;
});

/**
 * Set env
 */
$di->setShared("env", function () {
    return (isset($_SERVER['SITE_ENV']) && $_SERVER['SITE_ENV']) ? strtolower($_SERVER['SITE_ENV']) : 'prod';
});


/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () use ($config, $di) {
    $dbConfig = $config->database->toArray();
    $adapter = $dbConfig['adapter'];
    unset($dbConfig['adapter']);
    $class = 'Phalcon\Db\Adapter\Pdo\\'.$adapter;
    $db = new $class($dbConfig);
    return $db;
});


/**
 * database schema
 * Author:Robert
 *
 * @return MetaDataCache
 */
if (isset($config->redis) && isset($config->database) && $di->get('env') !== 'dev') {
    $di['modelsMetadata'] = function () use ($config) {
        $redisConfig = $config->redis->toArray();
        $redisConfig['statsKey'] = '_PHCM_MM_'.$config->database->dbname;
        $redisConfig['lifetime'] = 86400;
        $redisConfig['index'] = 5;
        $metadata = new MetaDataCache($redisConfig);
        return $metadata;
    };
}


/**
 * cache
 */
if (isset($config->cache)) {
    $di->set('cache', function () use ($config) {
        $frontCache = new FrontData(["lifetime" => $config->cache->lifetime]);
        return new BackendCache($frontCache, $config->cache->toArray());
    });
}


/**
 * logger
 */
if (isset($config->logger)) {
    $di->setShared('logger', function () use ($config) {
        return new FileAdapter($config->logger->file);
        /*$logger->critical("This is a critical message");
        $logger->emergency("This is an emergency message");
        $logger->debug("This is a debug message");
        $logger->error("This is an error message");
        $logger->info("This is an info message");
        $logger->notice("This is a notice message");
        $logger->warning("This is a warning message");
        $logger->alert("This is an alert message");*/
    });
}
