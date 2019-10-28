<?php

namespace Services;

use Janfish\Rpc\Server\Service\ServiceInterface;
use Phalcon\Di;
use Phalcon\DI\InjectionAwareInterface;
use Phalcon\DiInterface;

/**
 * Author:Robert
 *
 * Class ServiceBase
 * @package Core
 *
 * @property  \Phalcon\Db\Adapter $db
 */
abstract class BaseDemo implements InjectionAwareInterface,ServiceInterface
{

    /**
     * Author:Robert
     *
     * @var
     */
    protected $_di;

    /**
     * Author:Robert
     *
     * @var
     */
    protected $db;


    /**
     * db helper
     */
    public function init()
    {
        $this->setDi(Di::getDefault());
        $this->db = $this->getDi()->get('db');
    }


    /**
     * @param DiInterface $di
     */
    public function setDi(DiInterface $di)
    {
        $this->_di = $di;
    }

    /**
     * @return mixed
     */
    public function getDi()
    {
        return $this->_di;
    }
}
