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
    private $_arrPayments;  //Допустимые виды оплаты в ПО "Топаз АЗС".


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
        ];
    }

    /**
     * Метод будет возвращать из БД массив с данными о том в какой емкости находится какой вид топлива для выбранного
     * подразделения
     */
    private function getTanksFuelType($subdivision){
        try{
            $query = ("SELECT `number`, `name`, `fuel_type` FROM `tanks`
                       INNER JOIN `fuel_types` ON `fuel_types`.`id` = `tanks`.`fuel_type`
                       WHERE `subdivision` = :subdivision");
            $result = $this->_db->prepare($query);
            $result->execute([
                'subdivision' => $subdivision
            ]);
            if ($result->rowCount() > 0){
                while ($row = $result->fetch()){
                    $tanksFuelType['ids'][$row['number']] = $row['fuel_type'];
                    $tanksFuelType['names'][$row['number']] = $row['name'];
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
    public function getFuelTypeFromTank(int $subdivision, int $tank, string $field){
        $arrTanksFuelTypes = $this->getTanksFuelType($subdivision);
        return $arrTanksFuelTypes[$field][$tank];
    }

    /**
     * Метод собирает информацию из XML файла в массив
     *
     * @param int $subdivision_id
     * @param $simpleXmlElement
     * @return array
     */
    private function getXmlData(int $subdivision_id, $simpleXmlElement){
        if (!isset($simpleXmlElement)){
            return null;
        }
        //Получаю полную дату открытия смены в формате строки
        $xmlDate = (string)$simpleXmlElement->Sessions->Session['StartDateTime'];
        //Конвертирую в удобный для вставки в БД формат
        $startDate = date('Y-m-d', strtotime($xmlDate));
        //Объявляю массив в который будут собираться распарсенные данные из XML отчета
        $arrXml = [];
        /*
         * Собираю массив из данных которые я могу считать из XML^
         * - Дата открытия смены,
         * - Номер емкости,
         * - Начальный объем,
         * - Фактический объем, объем после замера метрштоком
         * - Плотность, именно плотность не удельный весь
         * - Температура
         * - Масса топлива
         * - Идентификатор топлива
         */
        foreach ($simpleXmlElement->Sessions->Session->Tanks->Tank as $item){
            $tankNum = (int)$item['TankNum'];
            $arrXml[$tankNum]['StartDate'] = $startDate;
            $arrXml[$tankNum]['TankNum'] = $tankNum;
            $arrXml[$tankNum]['StartFuelVolume'] = str_replace(',', '.', (string)$item['StartFuelVolume']);
            $arrXml[$tankNum]['EndFactVolume'] = str_replace(',', '.', (string)$item['EndFactVolume']);
            $arrXml[$tankNum]['EndDensity'] = (string)$item['EndDensity'];
            $arrXml[$tankNum]['EndTemperature'] = (string)$item['EndTemperature'];
            $arrXml[$tankNum]['EndMass'] = str_replace(',', '.', (string)$item['EndMass']);
            $arrXml[$tankNum]['Fuel'] = $this->getFuelTypeFromTank($subdivision_id, $tankNum, 'ids');
        }
        /*
         * Заполняю массив данными об отпущенном топливе в разрезе емкости / вида топлива.
         * Сначала добавляю в массив выше, новый индекс для каждой емкости и приравниваю его значение 0.
         * Это делается для избежания notice "undefined index".
         * После, я уже считываю значения outcome из XML файла и прибавляю их только для тех емкостей из которых
         * был отпуск топлива. Те емкости из которых топливо не сливалось остануться с outcome равным 0.
         */
        //Шаг №1
        foreach ($arrXml as $item){
            $TankNum = (string)$item['TankNum'];
            $arrXml[$TankNum]['Outcome'] = 0;
        }
        //Шаг №2
        foreach ($simpleXmlElement->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelRelease = str_replace(',', '.', (string) $item['Volume']);
            $arrXml[$TankNum]['Outcome'] += $FuelRelease;
        }
        /*
         * Заполняю массив данными о принятом топливе
         * Сначала так же как и с outcome добавляю новый индекс в масив arrXml и приравниваю его 0.
         * Потом прибавляю к нему значения для тех емкостей в которые происходила приемка топлива.
         */
        //Шаг №1
        foreach ($arrXml as $item){
            $TankNum = (string)$item['TankNum'];
            $arrXml[$TankNum]['Income'] = 0;
        }
        //Шаг №2
        foreach ($simpleXmlElement->Sessions->Session->IncomesByDischarge->IncomeByDischarge as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelIncome = str_replace(',', '.', (string) $item['Volume']);
            $arrXml[$TankNum]['Income'] += $FuelIncome;
        }
        /*
         * Вычисляю расчетный остаток.
         * Добавляю новый индекс EndFuelVolume и приравниваю его значение к 0.
         * Высчи тваю разницу между начальным объемом
         */
        //Шаг №1
        foreach ($arrXml as $item){
            $TankNum = (string)$item['TankNum'];
            $arrXml[$TankNum]['EndFuelVolume'] = 0;
        }
        //Шаг №2
        foreach ($arrXml as $item){
            $TankNum = (string)$item['TankNum'];
            $arrXml[$TankNum]['EndFuelVolume'] = $item['StartFuelVolume'] + $item['Income'] - $item['Outcome'];
        }
        return $arrXml;
    }

    /**
     * Метод возвращает массив с данными считанными со всех XML файлов которые находятся в специальном хранилище
     *
     * @param int $subdivision_id
     * @param string $directory
     * @return array
     */
    public function getXmlFilesData(int $subdivision_id, string $directory){
        $files = scandir($directory);
        $simpleXmlElements = [];
        //Отключаю ошибки libxml и беру полномочия на обработку ошибок на себя.
        libxml_use_internal_errors(true);
        //Получаю имена всех файлов находящихся в директории storage и затем преобразую содержимое этих файлов в
        //объекты SimpleXML и наполняю ими массив simpleXmlElements.
        for ($i = 2; $i < count($files); $i++){
            $simpleXmlElements[$i]['file_name'] = $files[$i];
            $simpleXmlElements[$i]['simpleXmlElement'] = simplexml_load_file(ROOT.'/storage/'.$files[$i]) ? simplexml_load_file(ROOT.'/storage/'.$files[$i]) : null;
        }
        //Возвращаю обработку ошибок в стандартное положение.
        libxml_use_internal_errors(false);
        $arr = [];
        //Прохожу по каждому элементу массива simpleXmlElements и получаю из него информацию по смене.
        //Возвращаю массив с распарсенными данными
        foreach ($simpleXmlElements as $simpleXmlElement){
            $arr[$simpleXmlElement['file_name']]['file_name'] = $simpleXmlElement['file_name'];
            $arr[$simpleXmlElement['file_name']]['data'] = $this->getXmlData($subdivision_id, $simpleXmlElement['simpleXmlElement']);
        }
        return $arr;
    }

    /**
     * Метод вставляет в таблицу базы данных информацию полученную из XML файлов
     *
     * @param $xmlFileData
     * @param $subdivision_id
     * @param $user
     */
    public function insert($xmlFileData, $subdivision_id, $user){
        try{
            if (isset($xmlFileData)){
                /**
                 * Сначала форирую полузапрос.
                 */
                $query = ("INSERT INTO `tarantula_fuel` (`fuel_id`, `start_volume`, `end_volume`, `fact_volume`,
                      `income`, `outcome`, `density`, `temperature`, `subdivision`, `user`, `tank`, `date`)
                       VALUES ");
                /**
                 * Затем для каждой строки и пришедшего массива (данные за один день работы АЗС) получаю значения в формате:
                 * (1, 1000, 500, 510, 0, 500, 735, 25, 4, 1, 1, 2018-09-10). Количество таких строк равно количеству
                 * строк в пришедшем массиве. После получения всех этих строк я добавляют их в полузапрос, заканчивая
                 * формирование полного запроса в формате:
                 * ----------------------------------------------------------------------------------------------------
                 * INSERT INTO `tarantula_fuel` (`fuel_id`, `start_volume`, `end_volume`, `fact_volume`,
                 * `income`, `outcome`, `density`, `temperature`, `subdivision`, `user`, `tank`, `date`)
                 *  VALUES
                 * (5, 32634.32, 31191.67, 31885, 0, 1442.65, 836, 25, 4, 1, 1, '2018-08-01'),
                 * (5, 37593.04, 36804, 36982.6, 0, 789.04, 836, 24, 4, 1, 2, '2018-08-01'),
                 * (1, 3916.62, 4787.11, 4500, 2000, 1129.51, 738, 21, 4, 1, 3, '2018-08-01'),
                 * ...........................................................................
                 * (2, 2382.57, 2936.91, 3310, 1000, 445.66, 736, 20, 4, 1, 4, '2018-08-01');
                 * ----------------------------------------------------------------------------------------------------
                 *
                 */
                foreach ($xmlFileData as $row){
                    $query .= sprintf("(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, '%s'),",
                        preg_replace('/[^0-9.]/', '', $row['Fuel']),
                        preg_replace('/[^0-9.]/', '', $row['StartFuelVolume']),
                        $row['EndFuelVolume'],
                        $row['EndFactVolume'],
                        $row['Income'],
                        $row['Outcome'],
                        $row['EndDensity'],
                        $row['EndTemperature'],
                        $subdivision_id,
                        $user,
                        $row['TankNum'],
                        $row['StartDate']
                    );
                }
                //Обрезаю в конце запроса запятую
                $query = rtrim($query, ',');
                //Выполняю запрос и возвращаю овет  о результате в json формате
                $result = $this->_db->prepare($query);
                //Верну ответ поб успехе или наоборот
                return $result->execute() ? true : false;
            }else{
                return false;
            }

        }catch (\Exception $e){
            echo 'Database error in Insert Method';
        }
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

    public function getDataByDate($date_start, $date_end, $subdivision, $tank){
        //Если дата не выбрана пользователем, то поиск идет на текущую дату
        if (!isset($date_start)){
            $date_start = date("Y-m-d");
        }
        if (!isset($date_end)){
            $date_end = date("Y-m-d");
        }
        //Запрос данных из БД по значениям
        $query = ("SELECT * FROM `tarantula_fuel`
                   WHERE `subdivision` = :subdivision  AND `date` BETWEEN :date_start 
                   AND :date_end AND `tank` = :tank");
        $result = $this->_db->prepare($query);
        $result->execute([
            'date_start' => $date_start,
            'date_end' => $date_end,
            'subdivision' => $subdivision,
            'tank' => $tank
        ]);
        //В случае если записи найдены для установленных фильтров. Наполняю массив значениями этих записей
        if ($result->rowCount() > 0){
            $i = 1;
            $outPutData = [];
            while ($row = $result->fetch()){
                $outPutData[$i]['id'] = $row['id'];
                $outPutData[$i]['date'] = $row['date'];
                $outPutData[$i]['fuel_id'] = $row['fuel_id'];
                $outPutData[$i]['start_volume'] = $row['start_volume'];
                $outPutData[$i]['fact_volume'] = $row['fact_volume'];
                $outPutData[$i]['income'] = $row['income'];
                $outPutData[$i]['outcome'] = round($row['outcome'], 2);
                $outPutData[$i]['density'] = $row['density'];
                $outPutData[$i]['temperature'] = $row['temperature'];
                //Вычисляемые значения
                $outPutData[$i]['mass'] = round(($row['density']/1000)*$row['fact_volume'], 2);
                $outPutData[$i]['end_volume'] = round($row['start_volume'] + $row['income'] - $row['outcome'], 2);
                $outPutData[$i]['overage'] = round($row['fact_volume']-$row['end_volume'], 2);
                $i++;
            }
            $rpm = []; //Реализация по массе, начиная со дня date_start + 1
            $fact_outcome = []; //Фактический отпуск
            $count = count($outPutData);
            //Массив всегда начинается с индекса 1. Изходя из логики расчета РпМ стартовым значением пербора будет 2.
            //Формула РпМ = Масса(вчера) + Приход(сегодня) - Масса(сегодня)
            for ($i = 2; $i < $count+1; $i++){
                $rpm[$i] = $outPutData[$i-1]['mass']+$outPutData[$i]['income']*($outPutData[$i]['density']/1000)-$outPutData[$i]['mass'];
                $outPutData[$i]['rpm'] = $rpm[$i];
                //Фартический отпуск формула: ФО = ФО(вчера) - ФО(сегодня) + Приход(сегодня)
                $fact_outcome[$i] = $outPutData[$i-1]['fact_volume']-$outPutData[$i]['fact_volume']+$outPutData[$i]['income'];
                $outPutData[$i]['fact_outcome'] = round($fact_outcome[$i], 2);
            }
            //$a = $outPutData;
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

    public function getTankFuelArray($subdivision){
        $data = $this->getTanksFuelType($subdivision);
        return $data['names'];
    }

    /**
     * Это временных метод, он будет удален. Так как бует вместо него использоватся системный метод поиска подразделений
     * по секциям
     *
     * @return array
     */
    public function getAllGasStations(){
        return [
          4 => 'АЗС Чугуевка',
          5 => 'АЗС Таежка',
          6 => 'АЗС Анучино',
          7 => 'АЗС Дальнегорск',
          11 => 'АЗС Арсеньев'
        ];
    }

}