<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 31.10.2018
 * Time: 19:09
 */

namespace core\libs;


class PermissionChecker
{
    /*
     * Задача класса вернуть ответ о том есть ли у пользователя который инициализировал сессию запрашиваемая
     * привелегия. Достаточно для этого одного статического метода.
     */

    public static function hasPermission(string $permission){
        foreach ($_SESSION['user']['roles'] as $role) {
            return array_key_exists($permission, $role['permissions']) ? true : false;
        }
    }
}