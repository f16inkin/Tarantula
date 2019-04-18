<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 10:24
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;

class ControllerXmlParser extends ControllerParserBase
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Загружает layout страницы парсера, в которую потом подгружаются части модуля
     */
    public function actionIndex(){
        $this->_view->render($this->_device.'/parser/parser.page');
    }

}