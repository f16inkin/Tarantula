<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 09.05.2018
 * Time: 16:05
 */

namespace core\controllers;


use core\base\Controller;

class ControllerError extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->_view->setLayout('core', $this->_device.'/errors');
        $this->_view->setPages('core/views/pages/');
    }
    public function actionIndex()
    {
        // TODO: Implement actionIndex() method.
    }

    /**
     * Главный метод оповещает о том, что такой контроллер в системе не найден
     */
    public function actionError404(){
        $this->_view->setTitle('Отсутствует контроллер');
        $this->_view->render($this->_device.'/errors/error.controller-absent.page');
        $this->_system_logger->register(1, 'Отсустствует контроллер');
    }

    public function actionAccessDenied(){
        $this->_view->setTitle('Ошибка доступа');
        $this->_view->render($this->_device.'/errors/error.access-deny.page');
    }

    public function actionSystemIsOffline(){
        $this->_view->setTitle('Система остановлена');
        $this->_view->render($this->_device.'/errors/error.system-offline.page');
    }

    public function actionMenu(){
        $this->_view->setTitle('Ошибка меню');
        $this->_view->render($this->_device.'/errors/error.no-menu.page');
    }

}