<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 09.05.2018
 * Time: 16:01
 */

namespace application\start\controllers;


use application\base\ControllerApplication;

class ControllerStart extends ControllerApplication
{
    public function __construct()
    {
        parent::__construct();
    }

    public function actionIndex(){
        $this->_view->setTitle('Начало работы');
        $this->_view->render($this->_device.'/start/start.page');
    }

}