<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 20.06.2019
 * Time: 14:57
 */

namespace application\parser\models;


class XmlReportHandled
{
    private $_sessions = [];    //номер смены
    private $_tanks = [];       //информация по емкостям
    private $_outcomes = [];    //информация по отпущенному топливу
    private $_incomes = [];     //информация по принятому топливу

    /**
     * Magic
     * -------------
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    /**
     * Magic
     * -------------
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->$name;
    }

}