<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 10:00
 */

namespace application\parser\base;


use application\base\ControllerApplication;

class ControllerParserBase extends ControllerApplication
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Подгружает переданную в параметрах страницу, загружает в нее переданные массив данных. AJAX.
     *
     * @param string $page
     * @param array $content
     */
    protected function loadPage(string $page, array $content){
        include $this->_view->returnPagePath('application', $this->_device.$page);
    }

}