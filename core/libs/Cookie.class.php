<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 11.06.2018
 * Time: 16:56
 */

namespace core\libs;


class Cookie
{
    /**
     * Проверяет наличие куки в браузере
     *
     * Достает из куки id  в случае если она есть и достает из БД соль для данного id пользователя.
     * Достает из куки хэш и сравнивает его с хэшем вычесленным на основе хэширования соли и браузера пользователя
     * Если хэши совпадают, кука считается легальной(не устаревшей) и возвращается ответ об этом иначе ложь.
     *
     * @return bool
     */
    public static function checkCookie(){
        if (isset($_COOKIE[Configurator::getSysConfiguration()['cookie_name']])) {
            //$data_array = explode("-", $_COOKIE["AuthUserRestrictedArea"]);
            $id = self::getCookieID();
            $cookie_hash = self::getCookieHash();
            $query = "SELECT `id`, `secret_key` FROM `users` WHERE `id` = :id";
            $result = Db::getConnection()->prepare($query);
            $result->execute(['id' => $id]);
            if($result->rowCount()>0){
                $user = $result->fetch();
                $evaluate_hash = sha1($user['secret_key'].":".$_SERVER['HTTP_USER_AGENT']);
                if ($cookie_hash == $evaluate_hash){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Достает идентификатор пользователя из куки
     *
     * @return mixed
     */
    public static function getCookieID(){
        if (isset($_COOKIE[Configurator::getSysConfiguration()['cookie_name']])){
            $data_array = explode("-", $_COOKIE[Configurator::getSysConfiguration()['cookie_name']]);
            return $data_array[0];
        }
    }

    /**
     * Достает хэш из куки
     *
     * @return mixed
     */
    private static function getCookieHash(){
        if (isset($_COOKIE[Configurator::getSysConfiguration()['cookie_name']])){
            $data_array = explode("-", $_COOKIE[Configurator::getSysConfiguration()['cookie_name']]);
            return $data_array[1];
        }
    }
}