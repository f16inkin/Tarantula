<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 14.07.2018
 * Time: 11:13
 */

namespace core\models;


use core\base\Model;
use core\exceptions\DatabaseException;
use core\libs\Db;

class FuelTypes extends Model
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
     * Получает все доступные виды топлива для подразделения
     *
     * @param int $subdivision_id
     * @return array|null
     */
    public static function getAvailableFuelTypes(int $subdivision_id){
        //Метод который вернет все доступные виды топлива для текущей АЗС
        try{
            $query = ("SELECT `fuel_types`.`id`, `fuel_types`.`name` FROM `fuel_subdivision`
                INNER JOIN `fuel_types`  ON `fuel_subdivision`.`fuel_id` = `fuel_types`.`id`
                WHERE `fuel_subdivision`.`subdivision_id` = :subdivision_id");
            $result = Db::getConnection()->prepare($query);
            $result->execute([
                'subdivision_id' => $subdivision_id,
            ]);
            if($result->rowCount()>0){
                return $result->fetchAll();
            }
            //Вернет null, если на заправке не реализуется не один вид топлива
            return null;
        }catch (\Exception $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

    /**
     * Получает все виды топлива реализуемые в организации
     *
     * @return array|null
     */
    public static function getAllFuelTypes(){
        try{
            $query = ("SELECT * FROM `fuel_types` ORDER BY `id` ASC");
            $result = Db::getConnection()->prepare($query);
            $result->execute();
            if ($result->rowCount() > 0){
                return $result->fetchAll();
            }
            return null;
        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }
}