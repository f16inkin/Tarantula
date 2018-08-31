<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 23.08.2018
 * Time: 9:58
 */

namespace models;


use base\ModelTarantula;

class XmlParser extends ModelTarantula
{
    private $_arrPayments;

    public function __construct()
    {
        parent::__construct();
        $this->_arrPayments = [
            'Наличные',
            'Ведомость',
            'Без скидки',
            'Банк.карта',
            'Карты ТОПДОН',
            'Дисконтная карта',
            'Со скидкой',
            'Переливы',
            'Банк. карта',
            'Дисконтные карты',
        ];
    }


    public function calcElementsByPayment($path, $element){
        $arrElements = [];
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $arrElements[$TankNum]['Info']['FuelName'] = $FuelName;
            for ($i = 0; $i < count($this->_arrPayments); $i++){
                $arrElements[$TankNum]['Payment'][$this->_arrPayments[$i]] = 0;
            }
        }
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $PaymentModeName = (string)$item['PaymentModeName'];
            $Element = str_replace(',', '.', (string) $item[$element]);
            $arrElements[$TankNum]['Info']['FuelName'] = $FuelName;
            $arrElements[$TankNum]['Payment'][$PaymentModeName] += $Element;
        }
        return $arrElements;
    }

    public function calcFuelRelease($path){
        $arrRelease =[];
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            for ($i = 0; $i < 6; $i++){
                $arrRelease[$TankNum]['TankNumber'] = $TankNum;
                $arrRelease[$TankNum][$FuelName] = 0;
                //$arrRelease[$FuelName] = 0;
            }
        }
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $FuelRelease = str_replace(',', '.', (string) $item['Volume']);
            for ($i = 0; $i < 6; $i++){
                $arrRelease[$TankNum][$FuelName] += $FuelRelease;
                //$arrRelease[$FuelName] += $FuelRelease;
            }
        }
        return $arrRelease;
    }

    public function calcHosesCountersValues($path){
        $hosesCountersValues = [];
            foreach ($path->Sessions->Session->Hoses->Hose as $item) {
                $hoseNum = (string)$item['HoseNum'];
                $startCounter = str_replace(',', '.', (string)$item['StartCounter']);
                $endCounter = str_replace(',', '.', (string)$item['EndCounter']);
                $difference = $endCounter - $startCounter;
                $hosesCountersValues[$hoseNum]['HoseNum'] = $hoseNum; //Номер рукава
                $hosesCountersValues[$hoseNum]['StartCounter'] = $startCounter; //Начальный счетчик
                $hosesCountersValues[$hoseNum]['EndCounter'] = $endCounter;   //Конечный счетчик
                $hosesCountersValues[$hoseNum]['Difference'] = $difference; //разница между счетчиками
            }
        return $hosesCountersValues;
    }

    public function getDataByDate($date_start, $date_end, $subdivision, $fuel_id){
        //Если дата не выбрана пользователем, то поиск идет на текущую дату
        if (!isset($date_start)){
            $date_start = date("Y-m-d");
        }
        if (!isset($date_end)){
            $date_end = date("Y-m-d");
        }
        //Запрос данных из БД по значениям
        $query = ("SELECT * FROM `tarantula_fuel`
                   WHERE `subdivision` = :subdivision AND `fuel_id` = :fuel_id AND `date` BETWEEN :date_start AND :date_end");
        $result = $this->_db->prepare($query);
        $result->execute([
            'date_start' => $date_start,
            'date_end' => $date_end,
            'subdivision' => $subdivision,
            'fuel_id' => $fuel_id,
        ]);
        //В случае если записи найдены для установленных фильтров. Наполняю массив значениями этих записей
        if ($result->rowCount() > 0){
            $i = 1;
            $outputData = [];
            while ($row = $result->fetch()){
                $outPutData['data'][$i]['id'] = $row['id'];
                $outPutData['data'][$i]['fuel_id'] = $row['fuel_id'];
                $outPutData['data'][$i]['start_volume'] = $row['start_volume'];
                $outPutData['data'][$i]['end_volume'] = $row['end_volume'];
                $outPutData['data'][$i]['fact_volume'] = $row['fact_volume'];
                $outPutData['data'][$i]['income'] = $row['income'];
                $outPutData['data'][$i]['outcome'] = $row['outcome'];
                $outPutData['data'][$i]['density'] = $row['density'];
                $outPutData['data'][$i]['temperature'] = $row['temperature'];
                $outPutData['data'][$i]['mass'] = ($row['density']/1000)*$row['fact_volume'];
                $outPutData['data'][$i]['date'] = $row['date'];
                $outPutData['data'][$i]['overage'] = $row['fact_volume']-$row['end_volume'];
                $outPutData['data'][$i]['overage'] = $row['fact_volume']-$row['end_volume'];
                $i++;
            }
            $rpm = []; //Реализация по массе, начиная со дня date_start + 1
            $fact_outcome = []; //Фактический отпуск
            $count = count($outPutData['data']);
            //Массив всегда начинается с индекса 1. Изходя из логики расчета РпМ стартовым значением пербора будет 2.
            //Формула РпМ = Масса(вчера) + Приход(сегодня) - Масса(сегодня)
            for ($i = 2; $i < $count+1; $i++){
                $rpm[$i] = $outPutData['data'][$i-1]['mass']+$outPutData['data'][$i]['income']*($outPutData['data'][$i]['density']/1000)-$outPutData['data'][$i]['mass'];
                $outPutData['data'][$i]['rpm'] = $rpm[$i];
                //Фартический отпуск формула: ФО = ФО(вчера) - ФО(сегодня) + Приход(сегодня)
                $fact_outcome[$i] = $outPutData['data'][$i-1]['fact_volume']-$outPutData['data'][$i]['fact_volume']+$outPutData['data'][$i]['income'];
                $outPutData['data'][$i]['fact_outcome'] = $fact_outcome[$i];
            }
            return $outPutData;
        }
    }

    public function getDataByDate1($arrDate, $subdivision, $fuel_id){
        $outPutData = [];
        $query = ("SELECT * FROM `tarantula_fuel` 
                   WHERE `subdivision` = :subdivision AND `fuel_id` =:fuel_id AND `date` = :date ");
        foreach ($arrDate as $singleDate){
            $result = $this->_db->prepare($query);
            $result->execute([
                'subdivision'=>$subdivision,
                'date' => $singleDate,
                'fuel_id' => $fuel_id
            ]);
            $data = $result->fetch();
            $outPutData['data'][$data['id']]['id'] = $data['id'];
            $outPutData['data'][$data['id']]['fuel_id'] = $data['fuel_id'];
            $outPutData['data'][$data['id']]['start_volume'] = $data['start_volume'];
            $outPutData['data'][$data['id']]['end_volume'] = $data['end_volume'];
            $outPutData['data'][$data['id']]['fact_volume'] = $data['fact_volume'];
            $outPutData['data'][$data['id']]['income'] = $data['income'];
            $outPutData['data'][$data['id']]['outcome'] = $data['outcome'];
            $outPutData['data'][$data['id']]['density'] = $data['density'];
            $outPutData['data'][$data['id']]['temperature'] = $data['temperature'];
            $outPutData['data'][$data['id']]['mass'] = ($data['density']/1000)*$data['fact_volume'];
            $outPutData['data'][$data['id']]['date'] = $data['date'];
            $outPutData['data'][$data['id']]['overage'] = $data['fact_volume']-$data['end_volume'];
            $outPutData['data'][$data['id']]['overage'] = $data['fact_volume']-$data['end_volume'];
        }
        $rpm = [];
        for ($i = 2; $i < count($outPutData['data'])+1; $i++){
            $rpm[$i] = $outPutData['data'][$i-1]['mass']+$outPutData['data'][$i]['income']*($outPutData['data'][$i]['density']/1000)-$outPutData['data'][$i]['mass'];
            $outPutData['data'][$i]['rpm'] = $rpm[$i];
            $fact_outcome[$i] = $outPutData['data'][$i-1]['fact_volume']-$outPutData['data'][$i]['fact_volume']+$outPutData['data'][$i]['income'];
            $outPutData['data'][$i]['fact_outcome'] = $fact_outcome[$i];
        }
        return $outPutData;
    }

    /**
     * @return array
     */
    public function getArrPayments(): array
    {
        return $this->_arrPayments;
    }

}