<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 13.03.2019
 * Time: 10:24
 */

namespace application\parser\controllers;


use application\base\ControllerApplication;
use application\parser\models\XmlParser;

class ControllerXmlParser extends ControllerApplication
{
    public function __construct()
    {
        parent::__construct();
    }

    public function actionIndex()
    {
        $parser = new XmlParser(4);
        $content = $parser->getOutcomesData(ROOT.'/application/parser/storage');
        $this->_view->setTitle('Парсер XML файлов');
        $this->_view->render($this->_device.'/parser/parser.page',$content);
    }

}