<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 09.05.2018
 * Time: 15:57
 */

namespace libs;


class Router
{
    private $_routes;           //Маршруты
    private $_segments;         //Разбитые на сегменты

    /**
     * Router constructor.
     *
     * Получает сверенные с заданными в конфигурации сегменты, для маршрутизации
     */
    public function __construct()
    {
        $this->_routes = Configurator::getRoutes();
        $this->getSegmentsFromUrl();
    }

    /**
     * Вернет URI, с удаленными с начала и сконца / - слэшами
     *
     * @return string
     */
    private function getUrl()
    {
        return @trim($_SERVER['REQUEST_URI'], '/');
    }

    /**
     * Вернет совпадение маршрутов из файла конфигурации или установит по умолчанию сегменты для стартового контроллера
     *
     * @return array
     */
    private function  getSegmentsFromUrl()
    {
        //Получаю УРЛ
        $url = $this->getUrl();
        //Если не пустой УРЛ
        if ($url){
            //Получаю роуты для всего приложения
            $routing_rules = $this->_routes;
            //Проверяю соответствие правил роутинга, адресу в строке бразуера
                for ($i = 0; $i < count($routing_rules); $i++){
                    $urlPattern = (key($this->_routes[$i]));
                    $path = $this->_routes[$i][$urlPattern];
                    $construct = $this->_routes[$i]['construct'];
                if (preg_match("~$urlPattern~", $url)) { //"~$uriPattern~" тильда это важно, иначе будет ругаться если поставить / - слэш
                    $internalRoute = preg_replace("~$urlPattern~", $path, $url);
                    //Разбиваю внутренние роуты на сегменты
                    $segments = explode('/', $internalRoute);
                    //Поучаю сегменты сверенные с роутами
                    $this->_segments['matches'] = $segments;
                    $this->_segments['construct'] = $construct;
                }
            }
        }
        //Иначе, определяю роуты стратового модуля самостоятельно
        else{
            $this->_segments['matches'] = ['start', 'index'];
            $this->_segments['construct'] = false;
            //header("location:/start"); можно открыть редирект, но работает и без него
        }
    }

    /**
     * Вернет сегмент
     *
     * @param $n
     * @return mixed
     */
    private function getSegment($n)
    {
        $segments = $this->_segments;
        return @$segments['matches'][$n]; //@ аналог if(!empty) - проверка на пустоту
    }

    /**
     * Вернет имя контроллера или null если файл или класс еще не созданны
     *
     * @return bool|string
     */
    private function getController(){
        $controllerName = $this->getSegment(0);
        if(file_exists('modules/'.$controllerName.'/'.ucfirst($controllerName).'.class.php')){
            $controller = 'modules\\' . $controllerName .'\\' . ucfirst($controllerName); //Класс модуля с большой буквы должен начинаться
            return $controller;
        }
        elseif(file_exists('controllers/Controller'.ucfirst($controllerName).'.class.php')){
            $controller = 'controllers\Controller' . ucfirst($controllerName);
            return $controller;
        }
        else {
            return null;
        }
    }

    /**
     * Вернет имя экшена контроллера
     *
     * @return string
     */
    private function getAction(){
        $actionName = $this->getSegment(1);
        $action = 'action'.ucfirst($actionName);
        return $action;
    }

    /**
     * Запуск роутинга, для приложения
     */
    public function Start(){
        $controller = $this->getController();
        $action = $this->getAction();
        $parameter1 = $this->getSegment(2);
        $parameter2 = $this->getSegment(3);
        //Проверю. Если метод вернет не пустой контроллер то создам экзмепляр класса и запущу его
        if ($controller !=null) {
            //Далее решаем, отдавать ли в конструктор первый параметр или нет?
            if ($this->_segments['construct']){
                $object = new $controller($parameter1);
                if (method_exists($object, $action)) {
                    $object->$action($parameter2); //Будет всегда два параметра
                }
            }
            else{
                $object = new $controller;
                if (method_exists($object, $action)) {
                    $object->$action($parameter1,$parameter2); //Будет всегда два параметра
                }
            }
        }
        else {
            $controller = 'controllers\ControllerError';
            $action = 'actionIndex';
            $object = new $controller;
            $object->$action();
        }
    }
}