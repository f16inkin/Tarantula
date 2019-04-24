<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 10:00
 */

namespace application\parser\base;


use application\base\ControllerApplication;
use application\parser\models\ParserSettings;

class ControllerParserBase extends ControllerApplication
{
    protected $_settings;

    public function __construct()
    {
        parent::__construct();
        /**
         * Модель Settings для парсера в целом. Получение необходимых настроек для парсера напрямую из нее в любом
         * контроллере наследующем данный
         */
        $this->_settings = new ParserSettings();
    }

    /**
     * Подгружает переданную в параметрах страницу, загружает в нее переданные массив данных. AJAX.
     *
     * @param string $page
     * @param array $content
     */
    protected function loadPage(string $page, array $content = []){
        include $this->_view->returnPagePath('application', $this->_device.$page);
    }

}