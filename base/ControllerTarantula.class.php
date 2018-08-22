<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 21.08.2018
 * Time: 15:29
 */

//namespace application\modules\tarantula\base; //Такой неймспейс будет на продакшене, чтобы контроллеры ровн огрузились
namespace base;


class ControllerTarantula
{
    protected $_view;

    public function __construct()
    {
        $this->_view = new ViewTarantula();
        $this->_view->setLayout('template');
        $this->_view->setPages('views/pages/');
    }

}