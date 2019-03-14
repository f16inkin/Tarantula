<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 08.08.2018
 * Time: 19:17
 */

namespace core\models;


use core\base\Logger;
use core\libs\DatabaseException;

class SystemLogger extends Logger
{
    /**
     * SystemLogger constructor.
     *
     * Event codes - будут использоватся для поиска событий
     * 1 => Отсутствует маршрут или же класс указанный в маршруте
     * 2 => Добавлен новый пользователь
     * 3 => Изменен пользователь
     * 4 => Удален пользователь
     * 5 => Добавлено новое подразделение
     * 6 => Подразделение изменено
     * 7 => Удалено подразделение
     * 8 => Система включена
     * 9 => Система отключена
     * 10 => Модуль включен
     * 11 => Модуль отключен
     * 12 =>
     * 13 =>
     * 14 =>
     * 15 =>
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function register($event_code, $event_message)
    {
        try{
            $date = date('Y-m-d');
            $time = date('H:i:s');
            $query = ("INSERT INTO `system_logs` (`event_code`, `event_message`, `date`, `time`)
                       VALUES (:event_code, :event_message, :date, :time)");
            $result = $this->_db->prepare($query);
            $result->execute([
                'event_code' => $event_code,
                'event_message' => $event_message,
                'date' => $date,
                'time' => $time,
            ]);

        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

}