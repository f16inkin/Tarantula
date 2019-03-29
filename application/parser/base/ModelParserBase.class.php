<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 29.03.2019
 * Time: 11:55
 */

namespace application\parser\base;


use core\base\Model;

class ModelParserBase extends Model
{
    protected $_tanksFuelTypes = [];

    public function __construct(int $subdivision_id)
    {
        parent::__construct();
        $this->_tanksFuelTypes = $this->getTanksFuelType($subdivision_id);
    }

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
    protected function getSessionInformation($simpleXmlElement){
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

    /**
     * Метод вернет масив объектов SimpleXmlElements с данными собранными с Xml файлов
     * -----------------------------------------------------------------------------
     * @param string $directory
     * @return array
     */
    protected function getXmlFilesList(string $directory){
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