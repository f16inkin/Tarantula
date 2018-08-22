<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 21.08.2018
 * Time: 15:36
 */

namespace base;


use tarantula\libs\Db;

class ModelTarantula
{
    protected $_db;

    public function __construct()
    {
        $this->_db = Db::getConnection();
    }

}