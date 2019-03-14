<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 02.07.2018
 * Time: 20:20
 */

namespace core\models;


use core\base\Model;
use core\libs\DatabaseException;
use core\libs\Db;

class Role extends Model
{
    protected $_permissions = [];

    /**
     * Role constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Вернет список привелегий доступных выбранному пользователю
     * Используется статический метод так как мне нужен объект роли, а всего лишь список привелегий конкретного user'а
     *
     * @param int $id
     * @return array
     */
    public static function getRolePermissions(int $id){
        try{
            $query = ("SELECT `permissions`.`name` FROM `role_permission`
                      INNER JOIN `permissions`  ON `role_permission`.`permission_id` = `permissions`.`id`
                      WHERE `role_permission`.`role_id` = :id");
            $result = Db::getConnection()->prepare($query);
            $result->execute([
                'id' => $id,
            ]);
            //Объявляю этот массив, в случае если прав не будет, то метод вернет пустой массив
            //Вместо ошибки Undefined variable
            $permissions = [];
            while ($row = $result->fetch()){
                $permissions[$row['name']] = true;
            }
            return $permissions;
        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

    /**
     * Добавляет роль в систему
     *
     * @param string $name
     * @param string $description
     */
    public function insertRole(string $name, string $description){
        try{
            $query = ("INSERT INTO `roles` (`name`, `description`) VALUES (:name, :description)");
            $result = $this->_db->prepare($query);
            $success = $result->execute([
                'name' => $name,
                'description' => $description,
            ]);
            //В случае успешной вставки вернет id добавленной роли
            return $success ? $this->_db->lastInsertId() : null;

        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

    /**
     * Добавляет пользователю роль
     *
     * @param int $user
     * @param int $role
     * @return bool
     */
    public function insertUserRole(int $user, int $role){
        try{
            $query = ("INSERT INTO `user_role` (`user_id`, `role_id`) VALUES (:user, :role)");
            $result = $this->_db->prepare($query);
            $success = $result->execute([
                'user' => $user,
                'role' => $role,
            ]);
            //Вернет true/false
            return $success;
        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

    /**
     * Удаляет роль из системы
     *
     * @param int $id
     * @return bool
     */
    public function deleteRole(int $id){
        try{
            $query = ("DELETE FROM `roles` WHERE `id` = :id");
            $result = $this->_db->prepare($query);
            $success = $result->execute([
                'id' => $id,
            ]);
            //Вернет true/false
            return $success;
        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

    /**
     * Удалит роль у пользователя
     *
     * @param int $user
     * @param int $role
     * @return bool
     */
    public function deleteUserRole(int $user, int $role){
        try{
            $query = ("DELETE FROM `user_role` WHERE `user_id` = :user and `role_id` = :role");
            $result = $this->_db->prepare($query);
            $success = $result->execute([
                'user' => $user,
                'role' => $role,
            ]);
            return $success;
        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

}