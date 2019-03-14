<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 07.08.2018
 * Time: 20:43
 */

namespace core\models;


use core\base\Logger;
use core\libs\DatabaseException;

class UserLogger extends Logger
{
    /**
     * UserLogger constructor.
     *
     * Event codes - будут использоватся для поиска событий
     * 1 => Авторизация пользователя пройдена;
     * 2 => Авторизация пользователя не пройдена;
     * 3 => Пользователь вышел из системы
     * 4 => Пользователь добавил новое подразделение
     * 5 => Пользователь удалил подразделение
     * 6 => Пользователь изменил подразделение
     * 7 => Пользователь отключил систему
     * 8 => Пользователь отключил модуль
     * 9 => Пользователь сохранил / обновил информацию в системе
     * 10 => Пользователю неудалось сохранить / обновить информацию в системе
     * 11 => Пользователь добавил информацию в систему
     * 12 => Пользователю не удалось добавить информацию в систему
     * 13 =>
     * 14 =>
     * 15 =>
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Метод регистрирующий действие пользователя в системе
     *
     * @param $event
     * @param $user
     */
    public function register($event_code, $event_message){
        try{
            $user = $_SESSION['user']['id'];
            $date = date('Y-m-d');
            $time = date('H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'];
            $query = ("INSERT INTO `user_logs` (`event_code`, `event_message`, `user`, `ip`, `date`, `time`) 
                   VALUES (:event_code, :event_message, :user, :ip, :date, :time)");
            $result = $this->_db->prepare($query);
            $result->execute([
                'event_code'=>$event_code,
                'event_message'=>$event_message,
                'user' => $user,
                'ip' => $ip,
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