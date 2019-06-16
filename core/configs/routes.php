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
    /**
     * Загрузка страницы для раздела с емкостями
     */
    [
        '^parser/tanks$' => [
            'construct' => false, //Аргумент идет в конструктор
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerTanks',
            'action' => 'Index',
            'arguments' => 0 //Принимает один ааргумент
        ]
    ],
    /**
     * Загрузка данных для раздела "Емкости"
     */
    [
        '^parser/tanks/data$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerTanks',
            'action' => 'GetData',
            'arguments' => 0
        ]
    ],
    /**
     * Вставка данных в БД из раздела "Емкости"
     */
    [
        '^parser/tanks/insert$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerTanks',
            'action' => 'InsertData',
            'arguments' => 0
        ]
    ],
    /**
     * Загрузка главной страницы парсера
     */
    [
        '^parser/main$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerMain',
            'action' => 'Index',
            'arguments' => 0
        ]
    ],
    /**
     * Шаги выполнения процесса загрузки XML файлов через парсер
     */
    [
        '^parser/first-step$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerMain',
            'action' => 'FirstStep',
            'arguments' => 0
        ]
    ],
    [
        '^parser/tanks/subdivisions$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerTanks',
            'action' => 'GetSubdivisions',
            'arguments' => 0
        ]
    ],
    /**
     * Пагинация
     * -----------------------------------------------------------------
     * 1) Открытие выбранной страницы
     * 2) Удаление файлов с последующей подгрузкой на страницу имеющихся
     * 3) Вернет количество страниц для построения навигатора
     */
    [
        '^parser/pagination/([a-z]+)' => [
            'construct' => true,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerPagination',
            'action' => 'OpenPage',
            'arguments' => 1
        ]
    ],
    [
        '^parser/pagination/delete-and-upload/([a-z]+)' => [
            'construct' => true,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerPagination',
            'action' => 'DeleteAndUpload',
            'arguments' => 1
        ]
    ],
    [
        '^parser/pagination/get-pages-count/([a-z]+)' => [
            'construct' => true,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerPagination',
            'action' => 'GetPagesCount',
            'arguments' => 1
        ]
    ],
    /**
     * Загрузка файлов отчетов
     * --------------------------
     */
    [
        '^parser/uploader/upload$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerReportsUploader',
            'action' => 'UploadReports',
            'arguments' => 0
        ]
    ]
];
