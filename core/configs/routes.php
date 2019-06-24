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
        '^parser/get-started$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerMain',
            'action' => 'GetStarted',
            'arguments' => 0
        ]
    ],
    [
        '^parser/get-session-data$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerMain',
            'action' => 'GetSessionData',
            'arguments' => 0
        ]
    ],
    /**
     * Линия прогресса. Дает возможность управлять шагами через кнопки линии прогресса
     * -------------------------------------------------------------------------------
     * 1) Первый шаг: загрузка файлов
     * 2) Второй шаг: просмотр файлов
     * 3) Третий шаг: подтверждение пользователем загрузки или замены файлов при загруззке в БД
     * 4) Четвертый шаг: загрузка информации собранной из XML файлов в БД
     * 5) Пятый шаг: завершение процесса обработки файла / файлов
     */
    [
        '^parser/progress-line/first-step$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerMain',
            'action' => 'FirstStep',
            'arguments' => 0
        ]
    ],
    [
        '^parser/progress-line/second-step$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerMain',
            'action' => 'SecondStep',
            'arguments' => 0
        ]
    ],
    [
        '^parser/progress-line/third-step$' => [
            'construct' => false,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerMain',
            'action' => 'ThirdStep',
            'arguments' => 0
        ]
    ],
    /**
     * Инспектор
     * -----------------------------------------------------------------
     * 1) Открытие выбранной страницы
     * 2) Удаление файлов с последующей подгрузкой на страницу имеющихся
     * 3) Вернет количество страниц для построения навигатора
     */
    [
        '^parser/inspector/inspect$' => [
            'construct' => true,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerStorageInspector',
            'action' => 'LoadPage',
            'arguments' => 1
        ]
    ],
    [
        '^parser/inspector/displace$' => [
            'construct' => true,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerStorageInspector',
            'action' => 'DisplaceFiles',
            'arguments' => 1
        ]
    ],
    [
        '^parser/inspector/get-pages-count$' => [
            'construct' => true,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerStorageInspector',
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
    ],
    /**
     * Обработка XML отчетов
     */
    [
        '^parser/handler/handle/([0-9]+)' => [
            'construct' => true,
            'module' => 'parser',
            'folder' => 'application',
            'controller' => 'ControllerXmlSectionHandlersFactory',
            'action' => 'Index',
            'arguments' => 1
        ]
    ]
];
