<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 19.08.2018
 * Time: 15:24
 */

namespace administrator\start\controllers;


use administrator\base\ControllerAdministrator;

class ControllerStart extends ControllerAdministrator
{
    public function __construct()
    {
        parent::__construct();
    }

    public function actionIndex()
    {
        $this->_view->setTitle('Панель администратора');
        $this->_view->render($this->_device.'/start/start.page');
    }

}