<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 29.03.2019
 * Time: 11:32
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;

class ControllerMain extends ControllerParserBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function actionIndex(){
        $content = [];
        $this->loadPage('/parser/ajax/successed/main.page', $content);
    }

}