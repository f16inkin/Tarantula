<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 13.03.2019
 * Time: 10:23
 */

namespace application\parser\models;


use core\base\Model;

class XmlParser extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Метод возвращает массив с данными о том в какой емкости находится какой вид топлива для выбранного подразделения
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

    private function getFuelTypeFromTank(int $subdivision, int $tank, string $field){
        $arrTanksFuelTypes = $this->getTanksFuelType($subdivision);
        return $arrTanksFuelTypes[$field][$tank];
    }

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

    public function getXmlFilesList(int $subdivision_id, string $directory){
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
        $arr = [];
        //Прохожу по каждому элементу массива simpleXmlElements и получаю из него информацию по смене.
        //Возвращаю массив с распарсенными данными
        foreach ($simpleXmlElements as $simpleXmlElement){
            $arr[$simpleXmlElement['file_name']]['file_name'] = $simpleXmlElement['file_name'];
            $arr[$simpleXmlElement['file_name']]['data'] = $this->getXmlData($subdivision_id, $simpleXmlElement['simpleXmlElement']);
        }
        return $arr;
    }


}