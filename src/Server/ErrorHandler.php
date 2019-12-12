<?php

namespace Janfish\Rpc\Server;

/**
 * Author:Robert
 *
 * Class Exception
 * @package Janfish\Rpc\Server
 */
class ErrorHandler
{

    public $logPath;

    /**
     * ErrorHandler constructor.
     * @param array $option
     */
    public function __construct(array $option)
    {
        if (isset($option['log_file'])) {
            $this->logPath = $option['log_file'];
        }
    }

    /**
     * Author:Robert
     *
     * @param $errNo
     * @param $errStr
     * @param $errFile
     * @param $errLine
     */
    public function handle($errNo, $errStr, $errFile, $errLine)
    {

    }

}
