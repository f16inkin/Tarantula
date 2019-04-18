<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 10:05
 */

namespace application\parser\base;


use core\base\Model;

class ModelParserBase extends Model
{
    protected $_tanksFuelTypes = [];
    protected $_subdivision_id;

    public function __construct(int $subdivision_id)
    {
        parent::__construct();
        $this->_tanksFuelTypes = $this->getTanksFuelType($subdivision_id);
        $this->_subdivision_id = $subdivision_id;
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

}