<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 29.03.2019
 * Time: 11:54
 */

namespace application\parser\models;


use application\parser\base\AbstractFileHandler;
use application\parser\base\ModelParserBase;

class Tanks extends ModelParserBase
{
    public function __construct(int $subdivision_id)
    {
        parent::__construct($subdivision_id);
    }

    /**
     * Метод вернет массив с данными считаннами из Xml отчета
     * ------------------------------------------------------
     * @param $simpleXmlElement
     * @return array | null
     */
    private function getXmlTanksData($simpleXmlElement){
        if (!isset($simpleXmlElement)){
            return null;
        }
        /**
         * Объявляю массив в который будут собираться распарсенные данные из XML отчета
         * arrXml= [SessionInformation = [], SessionData = []]:
         * SessionInformation = [Number, StartDateTime, EndDateTime, Operator] - информация о смене.
         * SessionData = [TankNum, StartFuelVolume, EndFactVolume, EndDensity, EndTemperature, EndMass, Fuel, Outcome,
         * Income, EndFuelVolume, Overage] - информация о топливе за смену.
         */
        $arrXml = [];
        /**
         * Получаю данные о смене.
         */
        $sessionInformation = $this->getSessionInformation($simpleXmlElement);
        /*
         * Собираю массив из данных которые я могу считать из XML^
         * - Номер емкости,
         * - Начальный объем,
         * - Фактический объем, объем после замера метрштоком
         * - Плотность, именно плотность не удельный весь
         * - Температура
         * - Масса топлива
         * - Идентификатор топлива
         */
        $sessionData = [];
        foreach ($simpleXmlElement->Sessions->Session->Tanks->Tank as $item){
            $tankNum = (int)$item['TankNum'];
            $sessionData[$tankNum]['TankNum'] = $tankNum;
            $sessionData[$tankNum]['StartFuelVolume'] = str_replace(',', '.', (string)$item['StartFuelVolume']);
            $sessionData[$tankNum]['EndFactVolume'] = str_replace(',', '.', (string)$item['EndFactVolume']);
            $sessionData[$tankNum]['EndDensity'] = (!empty((string)$item['EndDensity']) ? (string)$item['EndDensity'] : 0);
            $sessionData[$tankNum]['EndTemperature'] = (!empty((string)$item['EndTemperature']) ? (string)$item['EndTemperature'] : 0);
            $sessionData[$tankNum]['EndMass'] = (!empty((string)$item['EndMass']) ? (string)$item['EndMass'] : 0);
            $sessionData[$tankNum]['Fuel'] = $this->_tanksFuelTypes['names'][$tankNum];
        }
        /*
         * Заполняю массив данными об отпущенном топливе в разрезе емкости / вида топлива.
         * Сначала добавляю в массив выше, новый индекс для каждой емкости и приравниваю его значение 0.
         * Это делается для избежания notice "undefined index".
         * После, я уже считываю значения outcome из XML файла и прибавляю их только для тех емкостей из которых
         * был отпуск топлива. Те емкости из которых топливо не сливалось остануться с outcome равным 0.
         */
        //Шаг №1
        foreach ($sessionData as $item){
            $TankNum = (string)$item['TankNum'];
            $sessionData[$TankNum]['Outcome'] = 0;
        }
        //Шаг №2
        foreach ($simpleXmlElement->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelRelease = str_replace(',', '.', (string) $item['Volume']);
            $sessionData[$TankNum]['Outcome'] += $FuelRelease;
        }
        /*
         * Заполняю массив данными о принятом топливе
         * Сначала так же как и с outcome добавляю новый индекс в масив arrXml и приравниваю его 0.
         * Потом прибавляю к нему значения для тех емкостей в которые происходила приемка топлива.
         */
        //Шаг №1
        foreach ($sessionData as $item){
            $TankNum = (string)$item['TankNum'];
            $sessionData[$TankNum]['Income'] = 0;
        }
        //Шаг №2
        foreach ($simpleXmlElement->Sessions->Session->IncomesByDischarge->IncomeByDischarge as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelIncome = str_replace(',', '.', (string) $item['Volume']);
            $sessionData[$TankNum]['Income'] += $FuelIncome;
        }
        /*
         * Вычисляю расчетный остаток.
         * Добавляю новый индекс EndFuelVolume и приравниваю его значение к 0.
         * Высчитываю разницу между начальным объемом
         */
        //Шаг №1
        foreach ($sessionData as $item){
            $TankNum = (string)$item['TankNum'];
            $sessionData[$TankNum]['EndFuelVolume'] = 0;
        }
        //Шаг №2
        foreach ($sessionData as $item){
            $TankNum = (string)$item['TankNum'];
            $sessionData[$TankNum]['EndFuelVolume'] = $item['StartFuelVolume'] + $item['Income'] - $item['Outcome'];
        }
        /*
         * Вычисляю излишки топлива.
         * Добавляю новый индекс Overage и приравниваю его значение к 0.
         * Высчитываю разницу между расчетным и фактическим остатками
         */
        //Шаг №1
        foreach ($sessionData as $item){
            $TankNum = (string)$item['TankNum'];
            $sessionData[$TankNum]['Overage'] = 0;
        }
        //Шаг №2
        foreach ($sessionData as $item){
            $TankNum = (string)$item['TankNum'];
            $sessionData[$TankNum]['Overage'] = $item['EndFactVolume'] - $item['EndFuelVolume'];
        }
        /*
         * Собираю все в выходной массив.
         * Возвращаю данные если все прошло удачно.
         */
        $arrXml['SessionInformation'] = $sessionInformation;
        $arrXml['SessionData'] = $sessionData;
        return $arrXml;
    }

    //
    public function insertTanksData(array $xmlFileData){
        try{
            if (isset($xmlFileData)){
                /**
                 * Сначала форирую полузапрос.
                 */
                $query = ("INSERT INTO `tarantula_fuel` (`start_volume`, `end_volume`, `fact_volume`, `overage`, `income`,
                          `outcome`, `density`, `temperature`, `mass`, `tank`, `session`)
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
                foreach ($xmlFileData['SessionData'] as $row){
                    $query .= sprintf("(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s),",
                        preg_replace('/[^0-9.]/', '', $row['StartFuelVolume']),
                        preg_replace('/[^0-9.]/', '', $row['EndFuelVolume']),
                        preg_replace('/[^0-9.]/', '', $row['EndFactVolume']),
                        $row['Overage'],
                        $row['Income'],
                        $row['Outcome'],
                        preg_replace('/[^0-9.]/', '', $row['EndDensity']),
                        preg_replace('/[^-?0-9.]/', '', $row['EndTemperature']), //Допускаются отрицательные значения
                        preg_replace('/[^0-9.]/', '', $row['EndMass']),
                        $row['TankNum'],
                        $xmlFileData['SessionInformation']['Number']
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
        }catch (DatabaseException $e){
            echo 'Db Error';
        }
    }

    /**
     * Возвращает массив с распарсенными данными из XML
     * ------------------------------------------------
     * @param string $directory
     * @return array
     */
    public function getTanksData(AbstractFileHandler $handler){
        $dbHandler  = new DatabaseHandler($handler);
        $simpleXmlElements = $dbHandler->loadCorrectXml();
        $arr = [];
        //Прохожу по каждому элементу массива simpleXmlElements и получаю из него информацию по смене.
        //Возвращаю массив с распарсенными данными
        foreach ($simpleXmlElements as $simpleXmlElement){
            $arr[$simpleXmlElement['file_name']] = $this->getXmlTanksData($simpleXmlElement['simpleXmlElement']);
            $arr[$simpleXmlElement['file_name']]['RecordId'] = $simpleXmlElement['record_id'];
        }
        return $arr;
    }

}