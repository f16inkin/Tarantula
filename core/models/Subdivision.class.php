<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 09.07.2018
 * Time: 21:37
 */

namespace core\models;


use core\base\Model;
use core\exceptions\DatabaseException;
use core\libs\Session;

class Subdivision extends Model
{
    /**
     * Subdivision constructor.
     *
     * Создает объект для работы с подразделениями системы
     *
     * @param $user_id
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * Вернет все подразделения зарегистрированные в системе
     *
     * @return array|null
     */
    public function getAllSubdivisions(){
        try{
            $query = ("SELECT * FROM `subdivisions`");
            $result = $this->_db->prepare($query);
            $result->execute();
            if ($result->rowCount() > 0){
                $subdivisions = $result->fetchAll();
                return $subdivisions;
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

    /**
     *
     * Вернет все подразделения доступные пользователю
     *
     * @param $user_id
     * @return array|null
     */
    public function getUserSubdivisions($user_id){
        try{
            $query = ("SELECT `subdivisions`.`id`, `subdivisions`.`name`, `subdivisions`.`number`, `subdivisions`.`address`,
                      `subdivisions`.`status`
                      FROM `user_subdivision`
                      INNER JOIN `subdivisions` ON `subdivisions`.`id` = `user_subdivision`.`subdivision_id`
                      WHERE `user_id` = :user_id");
            $result = $this->_db->prepare($query);
            $result->execute([
                'user_id' => $user_id
            ]);
            if ($result->rowCount() > 0){
                $subdivisions = $result->fetchAll();
                return $subdivisions;
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