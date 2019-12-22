<?php

namespace Janfish\Rpc\Logger;

use SeasLog;

/**
 * Author:Robert
 *
 * Class Logger
 * @package Janfish\Rpc\Log
 */
class File
{

    /**
     * Author:Robert
     *
     * @var array
     */
    protected $_config;


    /**
     * Author:Robert
     *
     * @var
     */
    public $logPath;

    /**
     * Author:Robert
     *
     * @var
     */
    public $folder;

    /**
     * Logger constructor.
     * @param array $config
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        $this->_config = $config;
        if (isset($config['logPath'])) {
            $this->logPath = $config['logPath'];
        }
        $path = explode('/', $this->logPath);
        $folder = current(array_splice($path, -1));
        $path = implode('/', $path);
        if (!$path || !is_readable($path)) {
            throw new Exception(' 日志文件不存在');
        }
        $this->folder = $folder;
        SeasLog::setBasePath($path);
    }


    /**
     * Author:Robert
     *
     * @param string $msg
     * @param string $level
     * @return bool
     */
    public function write(string $msg, string $level = SEASLOG_DEBUG): bool
    {
        SeasLog::setDatetimeFormat('Y-m-d H:i:s');
        SeasLog::setLogger($this->folder);
        return SeasLog::log($level, $msg);
    }

    /**
     * Author:Robert
     *
     * @param string $msg
     * @return bool
     */
    public function debug(string $msg): bool
    {
        return $this->write($msg, SEASLOG_DEBUG);
    }
}
