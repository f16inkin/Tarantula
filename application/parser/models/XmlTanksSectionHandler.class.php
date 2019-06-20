<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 20.06.2019
 * Time: 15:19
 */

namespace application\parser\models;


use application\parser\interfaces\iXmlHandler;
use SimpleXMLElement;

class XmlTanksSectionHandler implements iXmlHandler
{
    private $_property = '_tanks';

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
        return $messagw = [0 => 'Tanks data'];
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