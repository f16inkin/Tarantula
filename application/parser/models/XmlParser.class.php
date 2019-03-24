<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 13.03.2019
 * Time: 10:23
 */

namespace application\parser\models;


use core\base\Model;
use core\exceptions\DatabaseException;

class XmlParser extends Model
{
    private $_tanksFuelTypes = [];

    public function __construct(int $subdivision_id)
    {
        parent::__construct();
        $this->_tanksFuelTypes = $this->getTanksFuelType($subdivision_id);
    }

    /*---------------------------------------------------------------------------------------------------------------*/
    /*------------------------------------------ОБЩИЕ МЕТОДЫ ОБРАБОТКИ ДАННЫХ----------------------------------------*/
    /*---------------------------------------------------------------------------------------------------------------*/
    /**
     * Метод возвращает массив с данными о том в какой емкости находится какой вид топлива для выбранного подразделения
     * ----------------------------------------------------------------------------------------------------------------
     * array =[ids = [], names = []]:
     * ids = [3 => 1, 4 => 2, 5 => 4, 1 => 5, 2 => 5, 6 => 5] - массив где цифровому ключу идентификатору емкости
     * соответствует идентификатор топлива.
     * names = [3 => Аи92, 4=> Аи95, 5 => Дт, 1 => ДТ-ЕВРО, 2 => ДТ-ЕВРО, 6 => ДТ-ЕВРО] - массив где именному ключу
     * идентификатору емкости соответствует идентификатор топлива.
     *
     * @param $subdivision
     * @return null | array
     */
    private function getTanksFuelType($subdivision){
        try{
            $query = ("SELECT `tank_number`, `fuel_name`, `fuel_type` FROM `tanks`
                       INNER JOIN `fuel_types` ON `fuel_types`.`id` = `tanks`.`fuel_type`
                       WHERE `subdivision` = :subdivision");
            $result = $this->_db->prepare($query);
            $result->execute([
                'subdivision' => $subdivision
            ]);
            if ($result->rowCount() > 0){
                while ($row = $result->fetch()){
                    $tanksFuelType['ids'][$row['tank_number']] = $row['fuel_type'];
                    $tanksFuelType['names'][$row['tank_number']] = $row['fuel_name'];
                }
                return $tanksFuelType;
            }
            return null;
        }catch (\Exception $e){

        }
    }

    /**
     * Метод получает данные о смене, для которой будут собранны данные из XML
     * -----------------------------------------------------------------------
     * @param $simpleXmlElement
     * @return array
     */
    private function getSessionInformation($simpleXmlElement){
        /*
        * Собираю массив из данных о смене:
        * - Номер смены,
        * - Дата открытия смены,
        * - Дата закрытиясмены,
        * - Ф.И.О. Оператора
        */
        $sessionNumber = (string)$simpleXmlElement->Sessions->Session['SessionNum'];
        $sessionStartDateTime = (string)$simpleXmlElement->Sessions->Session['StartDateTime'];
        $sessionEndDateTime = (string)$simpleXmlElement->Sessions->Session['EndDateTime'];
        $operator = (string)$simpleXmlElement->Sessions->Session['UserName'];
        $SessionInformation = [
            'Number' => $sessionNumber,
            'StartDateTime' => $sessionStartDateTime,
            'EndDateTime' => $sessionEndDateTime,
            'Operator' => $operator
        ];
        return $SessionInformation;
    }

    /*---------------------------------------------------------------------------------------------------------------*/
    /*--------------------------------------ОБРАБОТКА ДАННЫХ ИЗ РАЗДЕЛА СВЯЗАННОГО С ЕМКОСТЯМИ-----------------------*/
    /*---------------------------------------------------------------------------------------------------------------*/
    /*----------------------------------------------Получение данных-------------------------------------------------*/
    /*---------------------------------------------------------------------------------------------------------------*/
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
            $sessionData[$tankNum]['EndDensity'] = (string)$item['EndDensity'];
            $sessionData[$tankNum]['EndTemperature'] = (string)$item['EndTemperature'];
            $sessionData[$tankNum]['EndMass'] = str_replace(',', '.', (string)$item['EndMass']);
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

    /**
     * Возвращает массив с распарсенными данными из XML
     * ------------------------------------------------
     * @param string $directory
     * @return array
     */
    public function getTanksData(string $directory){
        $simpleXmlElements = $this->getXmlFilesList($directory);
        $arr = [];
        //Прохожу по каждому элементу массива simpleXmlElements и получаю из него информацию по смене.
        //Возвращаю массив с распарсенными данными
        foreach ($simpleXmlElements as $simpleXmlElement){
            if (isset($simpleXmlElement['simpleXmlElement'])){
                $arr[$simpleXmlElement['file_name']]['file_name'] = $simpleXmlElement['file_name'];
                $arr[$simpleXmlElement['file_name']]['data'] = $this->getXmlTanksData($simpleXmlElement['simpleXmlElement']);
            }
        }
        return $arr;
    }

    /*---------------------------------------------------------------------------------------------------------------*/
    /*------------------------------------------------Вставка данных-------------------------------------------------*/
    /*---------------------------------------------------------------------------------------------------------------*/
    private function insertTanksData(array $xmlFileData, int $subdivision_id, int $session_id){
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
        }catch (DatabaseException $e){
            echo 'Db Error';
        }
    }

    /*---------------------------------------------------------------------------------------------------------------*/
    /*----------------------------------------------ОБРАБОТКА ФАЙЛОВ-------------------------------------------------*/
    /*---------------------------------------------------------------------------------------------------------------*/
    /**
     * Метод вернет масив объектов SimpleXmlElements с данными собранными с Xml файлов
     * -----------------------------------------------------------------------------
     * @param string $directory
     * @return array
     */
    private function getXmlFilesList(string $directory){
        //
        $files = scandir($directory);
        $simpleXmlElements = [];
        //Отключаю ошибки libxml и беру полномочия на обработку ошибок на себя.
        libxml_use_internal_errors(true);
        //Получаю имена всех файлов находящихся в директории storage и затем преобразую содержимое этих файлов в
        //объекты SimpleXML и наполняю ими массив simpleXmlElements.
        for ($i = 2; $i < count($files); $i++){
            $simpleXmlElements[$i]['file_name'] = $files[$i];
            $simpleXmlElements[$i]['simpleXmlElement'] = simplexml_load_file($directory.'/'.$files[$i]) ? simplexml_load_file($directory.'/'.$files[$i]) : null;
        }
        //Возвращаю обработку ошибок в стандартное положение.
        libxml_use_internal_errors(false);
        return $simpleXmlElements;
    }


}