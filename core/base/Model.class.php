<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 09.05.2018
 * Time: 13:02
 */

namespace core\base;


use core\libs\Db;

class Model
{
    protected $_db; //PDO объект подключения к БД

    /**
     * Model constructor.
     *
     * Создает класс модель с базовой для всех наследников конфигурацией
     */
    public function __construct()
    {
        $this->_db = Db::getConnection();
    }

}