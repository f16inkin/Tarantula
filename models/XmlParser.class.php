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
    private $_arrPayments;          //Допустимые виды оплаты в ПО "Топаз АЗС".

    private $_arrStationsFuel; //Массив содержащий соотношение топлива к емкости для каждой АЗС

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

        $this->_arrStationsFuel = [
            '00-000004' => [
                1 => 'ДТ-ЕВРО',
                2 => 'ДТ-ЕВРО',
                3 => 'Аи92',
                4 => 'Аи95',
                5 => 'ДТ',
                6 => 'ДТ-ЕВРО'
            ],
            '00-000005' => [
                1 => 'ДТ-ЕВРО',
                2 => 'ДТ-ЕВРО',
                3 => 'Аи95',
                4 => 'Аи92',
                5 => 'ДТ',
                6 => 'Аи98'
            ],
            '00-000006' => [
                2 => 'ДТ-ЕВРО',
                3 => 'Аи95',
                4 => 'Аи92',
                6 => 'ДТ'
            ]
        ];
    }


    /**
     * Метод будет возвращать из БД массив с данными о том в какой емкости находится какой вид топлива
     */
    private function getTanksFuelType($subdivision){
        try{
            /*$query = ("SELECT `number`, `fuel_type`, `name` FROM `tanks`
                       INNER JOIN `fuel_types` ON `tanks`.`fuel_type` = `fuel_types`.`id`
                       WHERE `subdivision` = :subdivision");*/
            $query = ("SELECT `number`, `fuel_type` FROM `tanks`
                       WHERE `subdivision` = :subdivision");
            $result = $this->_db->prepare($query);
            $result->execute([
                'subdivision' => $subdivision
            ]);
            if ($result->rowCount() > 0){
                while ($row = $result->fetch()){
                    $tanksFuelType[$row['number']] = $row['fuel_type'];
                }
                return $tanksFuelType;
            }
            return null;
        }catch (\Exception $e){
            echo 'Error';
        }
    }

    /**
     * Метод будет возвращать идентификатор топлива, для текущей емкости выбранного подразделения.
     * Пример: для емкости 1 АЗС Чугуевка, этот метод на момент, вернет 5. Так как 5 это id ДТ-ЕВРО из таблицы
     * fuel_types.
     *
     * @param $station_code
     * @param $tank
     * @return mixed
     */
    public function getFuelTypeFromTank(int $subdivision, string $tank){
        $arrTanksFuelTypes = $this->getTanksFuelType($subdivision);
        return $arrTanksFuelTypes[$tank];
    }

    private function getTanksData($simpleXmlElement){
        //Получаю полную дату открытия смены в формате строки
        $xmlDate = (string)$simpleXmlElement->Sessions->Session['StartDateTime'];
        //Конвертирую в удобный для вставки в БД формат
        $startDate = date('d.m.Y', strtotime($xmlDate));
        //Объявляю массив в который будут собираться распарсенные данные из XML отчета
        $arrXml = [];

        foreach ($simpleXmlElement->Sessions->Session->Tanks->Tank as $item){
            $tankNum = (string)$item['TankNum'];
            $arrXml[$tankNum]['StartDate'] = $startDate;
            $arrXml[$tankNum]['TankNum'] = $tankNum;
            $arrXml[$tankNum]['StartFuelVolume'] = str_replace(',', '.', (string)$item['StartFuelVolume']);
            $arrXml[$tankNum]['EndFactVolume'] = str_replace(',', '.', (string)$item['EndFactVolume']);
            $arrXml[$tankNum]['EndDensity'] = (string)$item['EndDensity'];
            $arrXml[$tankNum]['EndTemperature'] = (string)$item['EndTemperature'];
            $arrXml[$tankNum]['EndMass'] = str_replace(',', '.', (string)$item['EndMass']);
            $arrXml[$tankNum]['FuelName'] = $this->getFuelTypeFromTank(4, $tankNum);
        }
        //Заполняю массив данными об отпущенном топливе в разрезе емкости / вида топлива
        foreach ($simpleXmlElement->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $arrXml[$TankNum]['Outcome'] = 0;
        }

        foreach ($simpleXmlElement->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelRelease = str_replace(',', '.', (string) $item['Volume']);
            $arrXml[$TankNum]['Outcome'] += $FuelRelease;
        }
        //Заполняю массив данными о принятом топливе
        foreach ($arrXml as $item){
            $TankNum = (string)$item['TankNum'];
            $arrXml[$TankNum]['Income'] = 0;
        }

        foreach ($simpleXmlElement->Sessions->Session->IncomesByDischarge->IncomeByDischarge as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelIncome = str_replace(',', '.', (string) $item['Volume']);
            $arrXml[$TankNum]['Income'] += $FuelIncome;
        }
        //Вычисляю расчетный остаток
        foreach ($arrXml as $item){
            $TankNum = (string)$item['TankNum'];
            $arrXml[$TankNum]['EndFuelVolume'] = 0;
        }
        foreach ($arrXml as $item){
            $TankNum = (string)$item['TankNum'];
            $outcome = isset($item['Outcome']) ? $item['Outcome'] : 0;
            $arrXml[$TankNum]['EndFuelVolume'] = $item['StartFuelVolume'] - $outcome;
        }

        return $arrXml;
    }

    public function getXmlFiles($directory){
        $files = scandir($directory);
        $simpleXmlElements = [];
        for ($i = 2; $i < count($files); $i++){
            $simpleXmlElements[] = simplexml_load_file(ROOT.'/storage/'.$files[$i]);
        }
        $arr = [];
        foreach ($simpleXmlElements as $simpleXmlElement){
            $arr[] = $this->getTanksData($simpleXmlElement);
        }
        return $arr;
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

    /**
     * @return array
     */
    public function getArrPayments(): array
    {
        return $this->_arrPayments;
    }

}