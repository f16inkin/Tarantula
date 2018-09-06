<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 21.08.2018
 * Time: 16:02
 */

namespace controllers;


use base\ControllerTarantula;
use models\XmlParser;
use SimpleXMLElement;

class ControllerStart extends ControllerTarantula
{
    private $_parser;

    public function __construct()
    {
        parent::__construct();
        $this->_parser = new XmlParser();
    }

    public function actionIndex(){
        $this->_view->setTitle('Начальная страница');
        $this->_view->render('start/start.page');
    }


    public function actionPath(){
        $path = simplexml_load_file(ROOT.'/storage/CloseSession_2018-08-02_08-50-52.xml');
        $amountByPayment = $this->_parser->calcElementsByPayment($path, 'Amount');
        $volumeByPayment = $this->_parser->calcElementsByPayment($path, 'Volume');
        $arrPayments = $this->_parser->getArrPayments();
        $hosesCountersValues = $this->_parser->calcHosesCountersValues($path);
        include $this->_view->returnPagePath('/start/parsed-data.page');
    }

    public function actionReport(){
        $subdivision = 4; //Очевидно
        $fuel_id = 1; //Тут пока через <select> <option>.
        //$content['report'] = $this->_parser->getDataByDate($arrDate, $subdivision, $fuel_id);
        $content['report'] = $this->_parser->getDataByDate('2018-07-31', '2018-08-03', $subdivision, $fuel_id);
        include $this->_view->returnPagePath('/start/report-by-date.page');
    }

    /**
     * Метод используется для чтения XML и добавления данных из него в таблицу
     */
    public function actionAdd(){
        $data = $this->_parser->getXmlFiles(ROOT.'/storage/');
        include $this->_view->returnPagePath('/start/report-by-xml.page');
    }

}
function xmlAttribute($object, $attribute){
    if(isset($object[$attribute]))
        return (string) $object[$attribute];
    else
        return null;
}
?>
