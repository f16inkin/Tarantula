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
        //$path = $xml = simplexml_load_file($_FILES['xml_file']['tmp_name']);
        //$path = simplexml_load_file(ROOT.'/storage/test.xml');
        $path = simplexml_load_file(ROOT.'/storage/CloseSession_2018-08-02_08-50-52.xml');
        /*echo '-------------------ЕМКОСТИ-------------------';
        echo '---------------------------------------------';
        echo '<br>';
        foreach ($path->Sessions->Session->Tanks->Tank as $item) {
            echo 'Номер емкости ';
            echo $item['TankNum'];
            echo ' Начальный объем ';
            echo $item['StartFuelVolume'];
            echo ' Плотность ';
            echo $item['EndDensity'];
            echo '<br>';
        }
        echo '<br>';
        echo '------------------СЧЕТЧИКИ------------------';
        echo '---------------------------------------------';
        echo '<br>';
        $hosesCountersValues = $this->_parser->calcHosesCountersValues($path);
        foreach ($hosesCountersValues as $singleHoseCounterValue){
            echo 'Номер рукава ';
            echo $singleHoseCounterValue['HoseNum'];
            echo ' Начальный счетчик ';
            echo $singleHoseCounterValue['StartCounter'];
            echo ' Конечный счетчик ';
            echo $singleHoseCounterValue['EndCounter'];
            echo '<br>';
        }*/
        echo '--------------------ЛИТРЫ--------------------';
        echo '---------------------------------------------';


        echo '--------------------ДЕНЬГИ-------------------';
        echo '---------------------------------------------';
        //$amountByPayment = $this->_parser->calcAmountByPayment($path);
        $amountByPayment = $this->_parser->calcElementsByPayment($path, 'Amount');
        $volumeByPayment = $this->_parser->calcElementsByPayment($path, 'Volume');
        $arrPayments = $this->_parser->getArrPayments();
        include $this->_view->returnPagePath('/start/parsed-data.page');
    }
}
function xmlAttribute($object, $attribute){
    if(isset($object[$attribute]))
        return (string) $object[$attribute];
    else
        return null;
}
?>
