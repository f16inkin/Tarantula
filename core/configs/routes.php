<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 09.05.2018
 * Time: 15:59
 */
return[
    /*----------------------------------------Система-------------------------------------------------*/
    /*#####################################################################################################*/
    //Работа системы приостановлена
    [
        '^error/system-is-offline$' => [
            'construct' => false,
            'module' => null,
            'folder' => 'core',
            'controller' => 'ControllerError',
            'action' => 'SystemIsOffline'
        ]
    ],
    //Ошибка доступа
    [
        '^error/access-denied$' => [
            'construct' => false,
            'module' => null,
            'folder' => 'core',
            'controller' => 'ControllerError',
            'action' => 'AccessDenied'
        ]
    ],
    //Авторизация
    [
        '^login$'   => [
            'construct' => false,
            'module' => null,
            'folder' => 'core',
            'controller' => 'ControllerLogin',
            'action' => 'Login',
        ]
    ],
    [
        '^logout$'   => [
            'construct' => false,
            'module' => null,
            'folder' => 'core',
            'controller' => 'ControllerLogin',
            'action' => 'Login',
        ]
    ],
    /*----------------------------------------Админ панель-------------------------------------------------*/
    /*#####################################################################################################*/
    //Администрирование системы мониторинга
    [
        '^admin$' => [
            'construct' => false,
            'module' => 'start',
            'folder' => 'administrator',
            'controller' => 'ControllerStart',
            'action' => 'Index',
        ]
    ],
    /*----------------------------------------Приложение---------------------------------------------------*/
    /*#####################################################################################################*/
    //Главная страница
    [
        '^start$' => [
            'construct' => false,
            'module' => 'start',
            'folder' => 'application',
            'controller' => 'ControllerStart',
            'action' => 'Index',
            'arguments' => 0 //Или просто можно не писать этот параметр
        ]
    ],
    //Парсер Xml
    [
        '^parser$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerXmlParser',
            'action' => 'Index',
            'arguments' => 0 //Или просто можно не писать этот параметр
        ]
    ],
    [
        '^parser/tanks$' => [
            'construct' => false, //Аргумент идет в конструктор
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerXmlParser',
            'action' => 'GetTanks',
            'arguments' => 0 //Принимает один ааргумент
        ]
    ],
    [
        '^parser/main$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerXmlParser',
            'action' => 'GetMain',
            'arguments' => 0
        ]
    ],
];
