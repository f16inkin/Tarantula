<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 20.06.2019
 * Time: 15:19
 */

namespace application\parser\models;


use application\parser\base\ModelParserBase;
use application\parser\interfaces\iXmlHandler;
use SimpleXMLElement;

class XmlTanksSectionHandler extends ModelParserBase implements iXmlHandler
{
    private $_property = '_tanks';

    public function __construct(int $subdivision_id)
    {
        parent::__construct($subdivision_id);
    }

    /**
     * Метод будет возвращать массив с распарсенной информацией из секции
     * - Секция Session: array = []
     * - Секция Tanks: array = []
     * - Секция Outcomes: array = []
     * - Секция Incomes: array = []
     * ------------------------------------------------------------------
     * @param SimpleXMLElement $simpleXMLElement
     * @return mixed
     */
    public function get(SimpleXMLElement $simpleXMLElement) : array {
        //if (!isset($simpleXmlElement)){
        //    return null;
        //}
        /**
         * Объявляю массив в который будут собираться распарсенные данные из XML отчета
         * arrXml= [SessionInformation = [], SessionData = []]:
         * SessionInformation = [Number, StartDateTime, EndDateTime, Operator] - информация о смене.
         * SessionData = [TankNum, StartFuelVolume, EndFactVolume, EndDensity, EndTemperature, EndMass, Fuel, Outcome,
         * Income, EndFuelVolume, Overage] - информация о топливе за смену.
         */
        $arrXml = [];

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
        foreach ($simpleXMLElement->Sessions->Session->Tanks->Tank as $item){
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
        foreach ($simpleXMLElement->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
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
        foreach ($simpleXMLElement->Sessions->Session->IncomesByDischarge->IncomeByDischarge as $item){
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
            $sessionData[$TankNum]['Overage'] = round(($item['EndFactVolume'] - $item['EndFuelVolume']),2);
        }
        /*
         * Собираю все в выходной массив.
         * Возвращаю данные если все прошло удачно.
         */
        $arrXml = $sessionData;
        return $arrXml;
    }

    /**
     * Получает имя свойства для объекта XmlHandled: _session, _tanks, _outcomes и тд.
     * --------------------------------------------------------
     * @return string
     */
    public function getPropertyName(): string
    {
        return $this->_property;
    }

    /**
     * Получает на вход перестроенный массив включающий в себя данные одного формата:
     * 'session' => [
     *          [0] => ['Number' => data,'StartDateTime' => data,'EndDateTime' => data,'Operator' => data],
     *          [1] => ['Number' => data,'StartDateTime' => data,'EndDateTime' => data,'Operator' => data],
     *          [2] => ['Number' => data,'StartDateTime' => data,'EndDateTime' => data,'Operator' => data]
     *      ]
     * После чего одним запросом к БД заполняет таблицу.
     * ----------------------------------------------------------------------------------------------------
     * @param array $restructured
     * @return mixed
     */
    public function save(array $restructured)
    {
        // TODO: Implement save() method.
    }
}