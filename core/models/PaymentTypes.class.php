<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.12.2018
 * Time: 20:31
 */

namespace core\models;


use core\base\Model;
use core\libs\Db;

class PaymentTypes extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function insert(){
        //Вставит в таблицу записи о видах топлива
    }

    public function update(){
        //Обновит в таблице записи с видами топлива
    }

    public function delete(){
        //Удалит записи из таблицы
    }

    /**
     * Верет все виды оплат для текущего подразделения
     *
     * @param int $subdivision_id
     * @return array|null
     */
    public static function getAvailablePaymentTypes(int $subdivision_id){
        try{
            $query = ("SELECT `payment_types`.`id` FROM `payment_subdivision`
                INNER JOIN `payment_types`  ON `payment_subdivision`.`payment_id` = `payment_types`.`id`
                WHERE `payment_subdivision`.`subdivision_id` = :subdivision_id");
            $result = Db::getConnection()->prepare($query);
            $result->execute([
                'subdivision_id' => $subdivision_id
            ]);
            if($result->rowCount()>0){
                return $result->fetchAll();
            }
            //Вернет null, если на заправке не принимается оплата не по одному из видов
            return null;
        }catch (\Exception $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

}