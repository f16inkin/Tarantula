<?php


namespace application\parser\models;


use application\parser\base\ModelParserBase;
use application\parser\interfaces\iXmlHandler;
use SimpleXMLElement;

class XmlOutcomesByRetailSectionHandler extends ModelParserBase implements iXmlHandler
{
    private $_property = '_outcomes';

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
        foreach ($simpleXMLElement->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $sessionData['byAmount'][$TankNum]['Info']['FuelName'] = $FuelName;
            for ($i = 0; $i < count($this->_payments); $i++){
                $sessionData['byAmount'][$TankNum]['Payment'][$this->_payments[$i]] = 0;
            }
        }


        foreach ($simpleXMLElement->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $sessionData['byVolume'][$TankNum]['Info']['FuelName'] = $FuelName;
            for ($i = 0; $i < count($this->_payments); $i++){
                $sessionData['byVolume'][$TankNum]['Payment'][$this->_payments[$i]] = 0;
            }
        }


        foreach ($simpleXMLElement->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $PaymentModeName = (string)$item['PaymentModeName'];
            $Element = str_replace(',', '.', (string) $item['Amount']);
            $sessionData['byAmount'][$TankNum]['Info']['FuelName'] = $FuelName;
            $sessionData['byAmount'][$TankNum]['Payment'][$PaymentModeName] += $Element;
        }

        foreach ($simpleXMLElement->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $PaymentModeName = (string)$item['PaymentModeName'];
            $Element = str_replace(',', '.', (string) $item['Volume']);
            $sessionData['byVolume'][$TankNum]['Info']['FuelName'] = $FuelName;
            $sessionData['byVolume'][$TankNum]['Payment'][$PaymentModeName] += $Element;
        }
        $sessionData['byAmount'] = array_values($sessionData['byAmount']);
        $sessionData['byVolume'] = array_values($sessionData['byVolume']);
        $sessionData['payments'] = $this->_payments;
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