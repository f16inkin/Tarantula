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

class ControllerStart extends ControllerTarantula
{
    private $_parser;

    public function __construct()
    {
        parent::__construct();
        $this->_parser = new XmlParser();
    }

    public function actionIndex(){
        //При интеграции модуля поиск подразделения АЗС осуществлять через this->_subdivision -> getAllGasStations();
        //Предварительно в конструторе создав объект $subdivision;
        //this->_subdivision = new Subdivision();
        $content['subdivisions'] = $this->_parser->getAllGasStations();
        $this->_view->setTitle('Начальная страница');
        $this->_view->render('start/start.page', $content);
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
        $subdivision = $_POST['subdivision_id']; //Очевидно
        $tank_array = $this->_parser->getTankFuelArray($subdivision);
        foreach ($tank_array as $tank_id => $fuel_name){
            $content['report'][$tank_id]['fuel_name'] = $fuel_name;
            $content['report'][$tank_id]['data'] = $this->_parser->getDataByDate('2018-07-31', '2018-09-11', $subdivision, $tank_id);
        }


        if (isset($content['report'])){
            include $this->_view->returnPagePath('/start/report-by-date.page');
        }else{
            //Верну отрицание для того чтобы в AJAX вывести сообщение об отсутсвуии данных.
            //Можно вернуть так же строку типа: echo 'error; и ловить в js так - if (response == 'error'){...}
            return false;
        }
    }

    /**
     * Метод используется для чтения XML и добавления данных из него в таблицу
     */
    public function actionAdd(){
        /*
         * Входные данные с формы по которым будет осуществлятся фильтрация.
         * Перед вставкой
         */
        $subdivision_id = $_POST['subdivision_id'];
        $user = 1; //$_SESSION['user']['id'];

        /*
         * Получаю массив сформированный из данных с XML файлов.
         * Массив имеет следующий вид:
         * CloseSession_2018_08_02-50-52.xml =[
         *      file_name = "CloseSession_2018_08_02-50-52.xml"
         *      data = [
         *          1 = [
         *                  StartDate = 2018-08-01
         *                  TankNum = 1
         *                  ......................
         *                  EndFuelVolume = 31191.67
         *              ]
         *      ]
         * ]
         */
        $arrXmlData = $this->_parser->getXmlFilesData($subdivision_id, ROOT.'/storage/');

        //Здесь разбиваю массив из файлов по одному. В каждом таком одиночном массиве содержатся записи о каждом виде
        //топлива.
        if (!empty($arrXmlData)){
            foreach ($arrXmlData as $singleXmlData){
                if ($this->_parser->insert($singleXmlData['data'], $subdivision_id, $user)){
                    $message[] = ['window' => 'success_window', 'message'=>'Файл '.$singleXmlData['file_name'].' обработан'];
                }else{
                    $message[] = ['window' => 'fail_window', 'message'=>'Файл '.$singleXmlData['file_name'].' не обработан'];
                }
            }
            //Если массив с данныи пустой - это значит, что в директории не загружены файлы.
        }else{
            $message[] = ['window' => 'fail_window', 'message'=>'В указанной директории нет файлов для обработки'];
        }
        //Отправка сообщения с результатом выполнения метода
        echo json_encode($message);
    }

}
?>
