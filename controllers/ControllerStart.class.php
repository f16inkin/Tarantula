<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 21.08.2018
 * Time: 16:02
 */

namespace controllers;


use base\ControllerTarantula;
use SimpleXMLElement;

class ControllerStart extends ControllerTarantula
{
    public function __construct()
    {
        parent::__construct();
    }

    public function actionIndex(){
        $this->_view->setTitle('Начальная страница');
        $this->_view->render('start/start.page');
    }

    public function actionPath(){
        //$path = $xml = simplexml_load_file($_FILES['xml_file']['tmp_name']);
        //$path = simplexml_load_file(ROOT.'/storage/test.xml');
        $path = simplexml_load_file(ROOT.'/storage/CloseSession_2018-08-02_08-50-52.xml');
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
        echo '---------------------------------------------';
        echo '<br>';
        foreach ($path->Sessions->Session->Hoses->Hose as $item) {
            echo 'Номер рукава ';
            echo $item['HoseNum'];
            echo ' Начальный счетчик ';
            echo $item['StartCounter'];
            echo ' Конечный счетчик ';
            echo $item['EndCounter'];
            echo '<br>';
        }
        echo '<br>';
        echo '---------------------------------------------';
        echo '<br>';
        $arrTop= [];
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $PaymentModeName = (string)$item['PaymentModeName'];
            $Volume = str_replace(',', '.', (string) $item['Volume']);
            $arrTop[$TankNum][$PaymentModeName][] = $Volume;
        }
        for ($i = 1; $i < 7; $i++){
            foreach ($arrTop[$i] as $key => $value){
                echo $key.' --- '.array_sum($value);
                echo '<br>';
            }
        }

        echo '<pre>';
       // print_r($arrTop);
        echo '</pre>';
        echo '<br>';
        echo '---------------------------------------------';
        echo '<br>';
        /*foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail[0]->attributes() as $a => $b) {
            echo $a,'="',$b;
            echo '<br>';
        }*/
        /*$arrTop = [];
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $arrTop[(string)$item['TankNum']][(string)$item['PaymentModeName']] += str_replace(',', '.', (string) $item['Amount']);
        }
        echo '<pre>';
        print_r($arrTop);
        echo '</pre>';*/
    }
}
function xmlAttribute($object, $attribute){
    if(isset($object[$attribute]))
        return (string) $object[$attribute];
    else
        return null;
}
$r=array(array(5,7), array(9,9));
foreach($r as $key => $val)
    echo $key.' — '.array_sum($val).'<br />';
?>