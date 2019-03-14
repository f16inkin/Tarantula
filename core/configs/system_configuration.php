<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 10.06.2018
 * Time: 16:23
 */
return [
    'user_log' => true,                             //Логирование действий пользователей
    'system_log' => true,                           //Логирование событий произощедших в системе
    'cookie_live' => 86400,                         //Время жизни куки
    'cookie_name' => 'AuthUserRestrictedArea',      //Имя куки авторизации
    'is_online' => true,                            //Статус: работает система или нет
];