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
use core\models\Subdivision;

class ControllerXmlParser extends ControllerApplication
{
    private $_parser;
    private $_storage = ROOT.'/application/parser/storage';
    private $_subdivisions;

    public function __construct()
    {
        parent::__construct();
        $this->_subdivisions = (new Subdivision())->getUserSubdivisions($this->_user->getId());
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
        $content['subdivisions'] = $this->_subdivisions;
        $this->_view->setTitle('Парсер XML файлов');
        $this->_view->render($this->_device.'/parser/parser.page', $content);
    }

    public function actionGetMain(){
        $content = [];
        $this->loadPage('/parser/ajax/successed/main.page', $content);
    }

    public function actionGetTanks(){
        $subdivision = $_POST['subdivision'];
        $content['subdivisions'] = $this->_subdivisions;
        $content['files'] = (new XmlParser($subdivision))->getTanksData($this->_storage);
        $this->_view->setTitle('Данные по емкостям');
        $this->loadPage('/parser/ajax/successed/tanks/tanks.page', $content);
    }

}