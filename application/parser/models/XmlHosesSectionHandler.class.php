<?php


namespace application\parser\models;


use application\parser\base\ModelParserBase;
use application\parser\interfaces\iXmlHandler;
use SimpleXMLElement;

class XmlHosesSectionHandler extends ModelParserBase implements iXmlHandler
{
    private $_property = '_hoses';

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
        foreach ($simpleXMLElement->Sessions->Session->Hoses->Hose as $item){
            $HoseNum = (string)$item['HoseNum'];
            $StartCounter = str_replace(',', '.', (string)$item['StartCounter']);;
            $EndCounter = str_replace(',', '.', (string)$item['EndCounter']);;
            $PumpNum = (string)$item['PumpNum'];
            $NumInPump = (string)$item['NumInPump'];
            $HoseType = (string)$item['HoseType'];
            $sessionData[$HoseNum]['HoseNum'] =  $HoseNum;
            $sessionData[$HoseNum]['StartCounter'] =  $StartCounter;
            $sessionData[$HoseNum]['EndCounter'] = $EndCounter;
            $sessionData[$HoseNum]['PumpNum'] = $PumpNum;
            $sessionData[$HoseNum]['NumInPump'] = $NumInPump;
            $sessionData[$HoseNum]['HoseType'] = $HoseType;
            $sessionData[$HoseNum]['Outcomes'] = round($EndCounter - $StartCounter, 2);
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