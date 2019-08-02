<?php

namespace Core;

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
abstract class ServiceBase implements InjectionAwareInterface
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
     * ServiceBase constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Author:Robert
     *
     */
    public function init()
    {
        $this->setDI(Di::getDefault());
        $this->db = $this->getDi()->get('db');
    }


    /**
     * @param DiInterface $di
     */
    public function setDI(DiInterface $di)
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
