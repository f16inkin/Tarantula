<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 12.03.2019
 * Time: 20:30
 */

namespace core\libs;


class Matcher
{
    private $_routes;
    private $_url;
    private $_matches;

    /**
     * Matcher constructor.
     * @param array $routes
     *
     * Инициализирует совпадения правил маршрутизации со строкой браузера
     */
    public function __construct(array $routes)
    {
        $this->_routes = $routes;
        $this->initUrl();
        $this->initiateMatches();
    }

    /**
     * Вернет URI, с удаленными с начала и сконца / - слэшами
     */
    private function initUrl(){
        $this->_url = @trim($_SERVER['REQUEST_URI'], '/');
    }

    /**
     * Метод инициализирует совпадения между Урл и заданными маршрутами.
     * Устанавливает совпадения в случае успеха в соответсвии с маршрутами иначе, устанавливает значения по умолчанию.
     */
    private function initiateMatches(){
        $matches = [];
        //Если не пустой УРЛ
        if ($this->_url){
            //Проверяю соответствие правил роутинга, адресу в строке бразуера
            for ($i = 0; $i < count($this->_routes); $i++){
                $urlPattern = (key($this->_routes[$i]));
                if (preg_match("~$urlPattern~", $this->_url)) { //"~$uriPattern~" тильда это важно, иначе будет ругаться если поставить / - слэш
                    $matches = $this->_routes[$i][$urlPattern];
                }
            }
        }
        //Иначе, определяю совпадения для стратового модуля самостоятельно
        else{
            $matches = [
                'construct' => false,
                'module' => 'start',
                'folder' => 'application',
                'controller' => 'ControllerStart',
                'action' => 'Index'
            ];
        }
        $this->_matches = $matches;
    }

    /**
     * Вернет массив с совпадаениями, для маршрутизации
     *
     * @return array
     */
    public function getMatches(){
        return $this->_matches;
    }

    /**
     * Вернет строку Url
     *
     * @return string
     */
    public function getUrl(){
        return $this->_url;
    }

}