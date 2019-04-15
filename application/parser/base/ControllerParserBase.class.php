<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 29.03.2019
 * Time: 11:18
 */

namespace application\parser\base;


use application\base\ControllerApplication;
use application\parser\models\XmlReportsHandler;
use core\models\Subdivision;

class ControllerParserBase extends ControllerApplication
{
    protected $_subdivisions;
    protected $_storage = ROOT.'/application/parser/storage';

    public function __construct()
    {
        parent::__construct();
        $this->_subdivisions = (new Subdivision())->getUserSubdivisions($this->_user->getId());
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