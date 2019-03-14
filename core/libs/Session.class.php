<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 01.08.2018
 * Time: 20:08
 */

namespace core\libs;


use core\exceptions\DatabaseException;
use core\models\Role;

class Session
{
    /**
     * Метод для инициализации сессии пользователя со всеми необходимыми параметрами
     *
     * @param $user_id
     * @return null
     */
    public static function initUserSession($user_id){
        try{
            $query = ("SELECT * FROM `users` WHERE `id` = :id");
            $result = Db::getConnection()->prepare($query);
            $result->execute(
                ['id' => $user_id]
            );
            $data = $result->fetchAll();
            if (!empty($data)){
                //Инициализирую сессию
                $_SESSION['user']['id'] = $data[0]['id'];
                $_SESSION['user']['login'] = $data[0]['login'];
                $_SESSION['user']['surname'] = $data[0]['surname'];
                $_SESSION['user']['firstname'] = $data[0]['firstname'];
                $_SESSION['user']['secondname'] = $data[0]['secondname'];
                $_SESSION['user']['foto'] = $data[0]['foto'];
                $_SESSION['user']['shortname'] = $data[0]['surname'].' '.mb_substr($data[0]['firstname'],0,1,"UTF-8").'. '.mb_substr($data[0]['secondname'],0,1,"UTF-8").'.';
                $_SESSION['user']['roles'] = self::initUserRoles($data[0]['id']);
                //Сразу же после инициализации сессии устанавливаю ей статус нормального состояния
                self::setState('normal');
                return true;
            }
            return false;
        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

    /**
     * Инициализирует роли для текущего пользователя
     */
    private static function initUserRoles($user_id){
        try{
            $query = ("SELECT `user_role`.`role_id`, `roles`.`name`, `roles`.`description` FROM `user_role`
                  INNER JOIN `roles` ON `user_role`.`role_id` = `roles`.`id`
                  WHERE `user_role`.`user_id` = :user_id");
            $result = Db::getConnection()->prepare($query);
            $result->execute([
                'user_id'=>$user_id
            ]);
            if ($result->rowCount() > 0) {
                while($row = $result->fetch()) {
                    $roles[$row["name"]]['id'] = $row['role_id'];
                    $roles[$row["name"]]['permissions'] = Role::getRolePermissions($row['role_id']);
                }
                return $roles;
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
     * Установка состояния сессии. Нужно для того чтобы отслеживать изменения относящиеся к классу пользователя
     * Возможные состояния сессии:
     * 1) normal - рабочее состояние
     * 2) need_to_update - состояние пр икотором сессия должна быть пересоздана
     *
     * @param string $state
     */
    public static function setState(string $state){
        $_SESSION['state'] = $state;
    }

    /**
     * Возвращает сокрашенноеимя в виде Фамилия И. О.
     *
     * @return string
     */
    public static function getShortName(){
        return $_SESSION['user']['shortname'];
    }

    /**
     * Возвращает ссылку на фотографию пользователя
     *
     * @return string
     */
    public static function getFoto(){
        return $_SESSION['user']['foto'];
    }

}