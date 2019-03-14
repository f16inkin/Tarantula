<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 06.08.2018
 * Time: 19:13
 */

namespace core\libs;


use core\base\User;

class Auth
{
    /**
     * Принимает в качестве параметра объект пользователя который требует авторизации и инициализирует его из сессии
     * Метод создан как оптимизированный вариант создания пользователя без обращения в БД.
     *
     * @param User $user
     */
    public static function authorize(User $user){
        if (self::authentication()){
            $user->setId($_SESSION['user']['id']);
            $user->setLogin($_SESSION['user']['login']);
            $user->setSurname($_SESSION['user']['surname']);
            $user->setFirstname($_SESSION['user']['firstname']);
            $user->setSecondname($_SESSION['user']['secondname']);
            $user->setShortname($_SESSION['user']['shortname']);
            $user->setFoto($_SESSION['user']['foto']);
            $user->setRoles($_SESSION['user']['roles']);
        }
    }

    /**
     * Проводится аутентификация пользователя пытабщегося войти в систему.
     * 1) Проводится проверка на наличие куки и сессии. И если их нет перенаправляет на модуль идентификации.
     * 2) Если имеется куки то производится создание сессии и ее наполнение данными для последующего использования
     * 3) В случае успеха аутентификации вернет истину
     *
     * @return bool
     */
    private static function authentication(){
        //Нет куки и сессии = идентификация пользователя через ввод логина и пароля
        //-----------------------------------------------------------------------------------------------------------//
        if (Cookie::checkCookie() == false and !$_SESSION){
            header("location:/login");
            return false;
        }
        //В случае наличия куки и ее валидации методом CheckCookie сессия будет инициализированна из id куки.
        //В случае если состояние сесси требует обновления, она будет переинициализированна
        //-----------------------------------------------------------------------------------------------------------//
        elseif (!$_SESSION or $_SESSION['state'] === 'need_to_update'){
            Session::initUserSession(Cookie::getCookieID());
        }
        //Если сессия создана или уже существует, то вернет подтверждение об успешной аутентификации
        return true;
    }

}