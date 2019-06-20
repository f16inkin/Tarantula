<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 20.06.2019
 * Time: 9:47
 */

namespace application\parser\models;


use application\parser\interfaces\iXmlHandler;
use SimpleXMLElement;

class XmlSessionsSectionHandler implements iXmlHandler
{
    private $_property = '_sessions';

    /**
     * Метод получает данные о смене, для которой будут собранны данные из XML
     * -----------------------------------------------------------------------
     * @param $simpleXmlElement
     * @return array
     */
    public function get(SimpleXMLElement $simpleXmlElement) : array {
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
        $sessionInformation = [
            'Number' => $sessionNumber,
            'StartDateTime' => $sessionStartDateTime,
            'EndDateTime' => $sessionEndDateTime,
            'Operator' => $operator
        ];
        return $sessionInformation;
    }

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