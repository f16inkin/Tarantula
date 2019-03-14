<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 10.06.2018
 * Time: 14:07
 */

namespace core\base;


abstract class Logger extends Model
{

    /**
     * Logger constructor.
     *
     * Автоматически подключен к БД по созданию
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Метод который будет регистрировать произошедшие события в БД
     *
     * @return mixed
     */
    abstract public function register($event_code, $event_message);
}