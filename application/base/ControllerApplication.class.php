<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 19.08.2018
 * Time: 14:03
 */

namespace application\base;


use core\base\Controller;
use core\libs\Configurator;

class ControllerApplication extends Controller
{
    /**
     * ControllerApplication constructor.
     *
     * Базовый для наследования всеми контроллерами приложения.
     * Устанавливается свой шаблон приложения, поведение при отключении системы.
     */
    public function __construct()
    {
        if (Configurator::getSysConfiguration()['is_online']){
            parent::__construct();
            $this->_view->setLayout('application', $this->_device.'/template');
            $this->_view->setPages('application/views/pages/');
        }else{
            header("location: /error/system-is-offline");
        }
    }

    public function actionIndex()
    {
        // TODO: Implement actionIndex() method.
    }

}