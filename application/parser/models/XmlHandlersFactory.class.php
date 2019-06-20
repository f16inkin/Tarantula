<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 20.06.2019
 * Time: 14:53
 */

namespace application\parser\models;


use SimpleXMLElement;

class XmlHandlersFactory
{
    private $_handlers = []; //обработчики Xml файлов: XmlSessionSection, XmlTanksSection и тд.

    public function __construct()
    {
        $this->_handlers['sessions'] = new XmlSessionsSectionHandler();
        $this->_handlers['tanks'] = new XmlTanksSectionHandler();

    }

    /**
     * Метод будет получать на вход массив объектов XmlHandled:
     * array = [
     *      [0] => object(XmlHandled)#1
     *      [1] => object(XmlHandled)#2
     *      [2] => object(XmlHandled)#3
     * ]
     * и формировать массив вида:
     * array = [
     *      'session' => [
     *          [0] => ['Number' => data,'StartDateTime' => data,'EndDateTime' => data,'Operator' => data],
     *          [1] => ['Number' => data,'StartDateTime' => data,'EndDateTime' => data,'Operator' => data],
     *          [2] => ['Number' => data,'StartDateTime' => data,'EndDateTime' => data,'Operator' => data]
     *      ],
     *      'tanks' => [
     *          [0] => [Data],
     *          [1] => [Data],
     *          [2] => [Data]
     *      ]
     * ]
     * ----------------------------------------------------------------------------------------------------
     * @param array $handled
     * @return array
     */
    private function restructure(array $handled) : array {
        $restructured = [];
        $i = 0;
        foreach ($handled as $single) {
            $i++;
            $restructured['sessions'][$i] = $single->_session;
            $restructured['tanks'][$i] = $single->_tanks;
            $restructured['outcomes'][$i] = $single->_outcomes;
            $restructured['incomes'][$i] = $single->_incomes;
        }
        return $restructured;
    }

    /**
     * Сформирует объект обработанного Xml файла. Заполнит все свойства объекта соответсвующие секциям файла
     * и вернет его
     * ------------------------------------------------------------------------------------------------------
     * @param SimpleXMLElement $simpleXMLElement
     * @return XmlHandled
     */
    public function handle(SimpleXMLElement $simpleXMLElement) : XmlHandled{
        $handled = new XmlHandled();
        foreach ($this->_handlers as $iXmlHandler){
            $prop = $iXmlHandler->getPropertyName();
            $handled->$prop = $iXmlHandler->get($simpleXMLElement);
        }
        return $handled;
    }

    /**
     * Метод получает на вход, такой перестроенный "квадратный" массив:
     * array = [
     *      'sessions' => [
     *          [0] => ['Number' => data,'StartDateTime' => data,'EndDateTime' => data,'Operator' => data],
     *          [1] => ['Number' => data,'StartDateTime' => data,'EndDateTime' => data,'Operator' => data],
     *          [2] => ['Number' => data,'StartDateTime' => data,'EndDateTime' => data,'Operator' => data]
     *      ],
     *      'tanks' => [
     *          [0] => [Data],
     *          [1] => [Data],
     *          [2] => [Data]
     *      ]
     * ]
     * После для каждой секции: session, tanks и тд. выбирает нужный обработчик и сохраняет данные этой секции
     * разовым запросом.
     * В итоге в независимости от количества поданных на обработку файлов, будет всего 5 запросов к БД,
     * вместо 5*количество файлов запросов (5*40=200 запросов для 40-ка файлов)
     * -------------------------------------------------------------------------------------------------------
     * @param array $restructured
     */
    public function save(array $restructured){
        foreach ($restructured as $section){
            $this->_handlers[key($section)]->save($section);
        }
    }

}