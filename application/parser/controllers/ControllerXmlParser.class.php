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
    private $_parser;
    private $_storage = ROOT.'/application/parser/storage';
    private $_subdivision_id;
    private $_subdivision_ids;

    public function __construct($subdivision_id = null)
    {
        parent::__construct();
        if (isset($subdivision_id)){
            $this->_parser = new XmlParser($subdivision_id);
            $this->_subdivision_id = $subdivision_id;
        }
    }

    /**
     * Подгружает переданную в параметрах страницу, загружает в нее переданные массив данных. AJAX.
     *
     * @param string $page
     * @param array $content
     */
    private function loadPage(string $page, array $content){
        include $this->_view->returnPagePath('application', $this->_device.$page);
    }


    public function actionIndex(){
        $content['subdivision'] = $this->_subdivision_id;
        $this->_view->setTitle('Парсер XML файлов');
        $this->_view->render($this->_device.'/parser/parser.page', $content);
    }

    public function actionGetMain(){
        $content = [];
        $this->loadPage('/parser/ajax/successed/main.page', $content);
    }

    public function actionGetTanks(){
        $subdivision = $_POST['subdivision'];
        //$content = $this->_parser->getTanksData($this->_storage);
        $content = (new XmlParser($subdivision))->getTanksData($this->_storage);
        $this->_view->setTitle('Данные по емкостям');
        $this->loadPage('/parser/ajax/successed/tanks/tanks.page', $content);
    }

}