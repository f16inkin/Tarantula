<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 09.05.2018
 * Time: 13:06
 */
define('ROOT', dirname(__FILE__)); //Константа корневая директория
ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
date_default_timezone_set('Asia/Vladivostok');
use core\libs\Configurator;
use core\libs\Matcher;
use core\libs\Router;
//autoload #1
function __autoload($className)
{
    try {
        $fileName = str_replace('\\', '/', $className) . '.class.php';
        if (!file_exists($fileName)) {
            //Если это не класс то скорее всего требуется интерфейс
            $fileName = str_replace('\\', '/', $className) . '.interface.php';
            if (!file_exists($fileName)) {
                //Если это не класс и не интерфейс то скорее всего требуется трейт
                $fileName = str_replace('\\', '/', $className) . '.trait.php';
                if (!file_exists($fileName)) {
                    throw new Exception('Искомая конструкция не найдена!'.$className);
                }
            }
        }
        require_once $fileName;
    }
    catch (Exception $e){
        echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
    }
    /*try {
        $fileName = str_replace('\\', '/', $className) . '.class.php';
        if (!file_exists($fileName)) {
            throw new Exception('Class not found!'.$className);
        }
        require_once $fileName;
    }
    catch (Exception $e){
        echo 'Выброшено исключение: ',  $e->getMessage(), "\n";
    }*/
}
//Определяю маршруты
$routes = Configurator::getRoutes();
//Определяю совпадения с маршрутами
$matcher = new Matcher($routes);
//Создаю маршрутизатор
$router = new Router($matcher);
//Запускаю маршрутизацию
$router->Start();
//var_dump($router);
/*function convert($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}
echo convert(memory_get_usage(true)).' Затрачено памяти';
echo '<br>';
echo memory_get_peak_usage();*/