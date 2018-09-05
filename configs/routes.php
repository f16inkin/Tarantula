<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 09.05.2018
 * Time: 15:59
 */
return[
    /*----------------------------------------Модуль Парсер------------------------------------------------*/
    /*#####################################################################################################*/
    //Стартовый контроллер
    [
        '^start$'       => 'start/index',
        'construct'     =>  false
    ],
    [
        '^start/path$'  => 'start/path',
        'construct'     =>  false
    ],
    [
        '^start/report$'  => 'start/report',
        'construct'     =>  false
    ],
    [
        '^start/add$'  => 'start/add',
        'construct'     =>  false
    ]

];
