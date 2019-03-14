<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 09.05.2018
 * Time: 15:57
 */

namespace core\libs;


class Router
{
    private $_matches;
    private $_url;

    /**
     * Router constructor.
     * @param Matcher $matcher
     *
     * Получает на вход объект с совпадениями для маршрутизации, а так же Url введенный в адресную строку браузера.
     */
    public function __construct(Matcher $matcher)
    {
        $this->_matches = $matcher->getMatches();
        $this->_url = $matcher->getUrl();
    }

    /**
     * Вернет сегмент из массива совпадений
     *
     * @param $key
     * @return mixed
     */
    private function getMatch($key){
        $matches = $this->_matches;
        return @$matches[$key]; //@ аналог if(!empty) - проверка на пустоту
    }

    /**
     * Вернет имя контроллера или null если файл или класс еще не созданны.
     *
     * @return bool|string
     */
    private function getController(){
        $controllerFolder = $this->getMatch('folder');      //Папка с файлом контроллером
        $controllerModule = $this->getMatch('module');      //Если это модуль, то его название
        $controllerName = $this->getMatch('controller');    //Имя класса контроллера
        //Если этот контроллер, является частью программного модуля:
        if ($controllerModule){
            if(file_exists($controllerFolder.'/'.$controllerModule.'/controllers/'.$controllerName.'.class.php')){
                $controller = $controllerFolder.'\\'.$controllerModule.'\controllers\\' . ucfirst($controllerName);
                return $controller;
            }
            else {
                return null;
            }
        //Иначе если контроллер, часть основной программы:
        }else{
            if(file_exists($controllerFolder.'/controllers/'.$controllerName.'.class.php')){
                $controller = $controllerFolder.'\controllers\\'.$controllerName;
                return $controller;
            }
            else {
                return null;
            }
        }
    }

    /**
     * Вернет имя экшена контроллера
     *
     * @return string
     */
    private function getAction(){
        $actionName = $this->getMatch('action');
        $action = 'action'.$actionName;
        return $action;
    }

    private function getArguments(){
        $arguments_count = $this->getMatch('arguments');
        //Если используются аргументы
        if ($arguments_count !== null){
            //Разбиваю Урл на сегменты
            $segments = explode('/', $this->_url);
            //Вычисляю сегмент, начиная с которого начинаются аргументы функции
            $n = count($segments) - $arguments_count;
            //Получаю массив с аргументоми метода
            $arguments = array_slice($segments, $n);
        }
        else{
            $arguments = null;
        }
        return $arguments;
    }

    /**
     * Запуск роутинга, для приложения
     */
    public function Start(){
        $controller = $this->getController();
        $action = $this->getAction();
        $arguments = $this->getArguments();
        //Проверю. Если метод вернет не пустой контроллер то создам экзмепляр класса и запущу его
        if ($controller !=null) {
            //Далее решаем, отдавать ли в конструктор первый параметр или нет?
            if ($this->_matches['construct']){
                $object = new $controller($arguments[0]);
                if (method_exists($object, $action)) {
                    array_shift($arguments);
                    $object->$action($arguments);
                }
            }
            else{
                $object = new $controller;
                if (method_exists($object, $action)) {
                    $object->$action($arguments);
                }
            }
        }
        else {
            $controller = 'core\controllers\ControllerError';
            $action = 'actionError404';
            $object = new $controller;
            $object->$action();
        }
    }
}