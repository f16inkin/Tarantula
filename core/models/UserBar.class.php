<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 12.11.2018
 * Time: 20:47
 */

namespace core\models;


use core\base\Model;

class UserBar extends Model
{
    private $_components = []; //Массив с компонентами, нотисы, профиль, письма(сообщения)

    public function __construct(array $components)
    {
        parent::__construct();
        $this->_components = $components;
    }

    public function getComponents(){
        return $this->_components;
    }

}