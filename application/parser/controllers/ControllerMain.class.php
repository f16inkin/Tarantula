<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 29.03.2019
 * Time: 11:32
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\models\XmlReportsHandler;

class ControllerMain extends ControllerParserBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function actionIndex(){
        $handler = new XmlReportsHandler($this->_storage);
        $correct_files = count($handler->scanDataBase(1));
        $incorrect_files = count($handler->scanDataBase(0));
        $processed_files = $correct_files + $incorrect_files;
        $content['files']['processed'] = $processed_files;
        $content['files']['correct'] = $correct_files;
        $content['files']['incorrect'] = $incorrect_files;
        $this->loadPage('/parser/ajax/successed/main.page', $content);
    }

}