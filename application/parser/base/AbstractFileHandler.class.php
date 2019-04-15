<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 15.04.2019
 * Time: 15:52
 */

namespace application\parser\base;


abstract class AbstractFileHandler
{
    protected $_storage;

    public function __construct(string $storage)
    {
        $this->_storage = $storage;
    }

    abstract public function scanStorage();

}