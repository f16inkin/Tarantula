<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 11.06.2018
 * Time: 17:04
 */

namespace core\controllers;


use core\base\View;
use core\libs\Configurator;
use core\models\UserLogger;
use core\models\Login;

class ControllerLogin
{
    protected $_view;
    protected $_login;
    private $_device;
    private $_user_logger;

    /**
     * ControllerLogin constructor.
     *
     * Создает контроллер авторизации пользователя
     *
     * Конфигурирует параметры куки, логгер, представление отличное от основного.
     */
    public function __construct()
    {
        $this->_view = new View();
        $this->_login = new Login();
        if (Configurator::getSysConfiguration()['user_log']){
            $this->_user_logger = new UserLogger();
        }
        //Определяю тип устройства
        $device = Configurator::getDevice();
        $this->_device = $device;
        //Структура отображения
        $this->_view->setLayout('core', $device.'/login');  //Лайаут для системы
        $this->_view->setPages('core/views/pages/');     //Ссылка к папке со страницами
    }

    /**
     * Авторизация пользователя в системе
     */
    public function actionLogin(){
        $user_name = (isset($_POST['user_field']) ? $_POST['user_field']:'');
        $password = (isset($_POST['password_field']) ? $_POST['password_field']:'');
        if($this->_login->doLogin($user_name, $password)){
            $this->_user_logger->register(1, 'Успешная авторизация пользователя '.$user_name);
            header("location:/start");
        }
        $this->_view->setTitle('Вход');
        $this->_view->render($this->_device.'/login/login.page');
    }

    /**
     * Выход пользователя из системы
     */
    public function actionLogout(){
        if ($_SESSION){
            $this->_user_logger->register(3, 'Пользователь вышел из системы ');
            $this->_login->doLogout();
        }
        header("location:/login");
    }

}