<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 09.05.2018
 * Time: 12:58
 */

namespace core\base;


use core\libs\AccessChecker;
use core\libs\Configurator;
use core\models\SystemLogger;
use core\models\UserLogger;

abstract class Controller
{
    protected $_view;               //Представление
    protected $_user;               //Пользователь
    protected $_user_logger;        //Логирование действий пользователя
    protected $_system_logger;      //Логирование системных событий
    protected $_device;             //Тип устройства: мобильное, планшет, компьютер
    protected $_access_checker;     //проверка прав доступа к подразделениям

    /**
     * Controller constructor.
     *
     * Создает абстрактный класс контроллер с базовой для всех наследников конфигурацией
     */
    public function __construct()
    {
        $this->_view = new View();
        $this->_user = new User(true);
        $this->_access_checker = new AccessChecker($this->_user->getId());
        if (Configurator::getSysConfiguration()['user_log']){
            $this->_user_logger = new UserLogger();
        }
        if (Configurator::getSysConfiguration()['system_log']){
            $this->_system_logger = new SystemLogger();
        }
        //Определяю тип устройства
        $device = Configurator::getDevice();
        $this->_device = $device;
    }

    /**
     * Абстрактный метод, реализуемый
     *
     * @return mixed
     */
    abstract public function actionIndex();
}