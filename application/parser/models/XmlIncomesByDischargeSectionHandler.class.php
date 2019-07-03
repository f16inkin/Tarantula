<?php


namespace application\parser\models;


use application\parser\base\ModelParserBase;
use application\parser\interfaces\iXmlHandler;
use SimpleXMLElement;

class XmlIncomesByDischargeSectionHandler extends ModelParserBase implements iXmlHandler
{
    private $_property = '_incomes';

    public function __construct(int $subdivision_id)
    {
        parent::__construct($subdivision_id);
    }

    /**
     * Метод будет возвращать массив с распарсенной информацией из секции
     * - Секция Session: int
     * - Секция Tanks: array = []
     * - Секция Outcomes: array = []
     * - Секция Incomes: array = []
     * ------------------------------------------------------------------
     * @param SimpleXMLElement $simpleXMLElement
     * @return mixed
     */
    public function get(SimpleXMLElement $simpleXMLElement): array
    {
        $sessionData = [];
        foreach ($simpleXMLElement->Sessions->Session->IncomesByDischarge->IncomeByDischarge as $item){
            $tankNum = (int)$item['TankNum'];
            $fuelName = (string)$item['FuelName'];
            $density = (int)$item['Density'];
            $mass = (int)$item['Mass'];
            $volume = (int)$item['Volume'];
            $partnerName = (string)$item['PartnerName'];
            $sessionData[$tankNum]['TankNum'] = $tankNum;
            $sessionData[$tankNum]['FuelName'] = $fuelName;
            $sessionData[$tankNum]['Density'] = $density;
            $sessionData[$tankNum]['Mass'] = $mass;
            $sessionData[$tankNum]['Volume'] = $volume;
            $sessionData[$tankNum]['PartnerName'] = $partnerName;
        }
        return $sessionData;
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