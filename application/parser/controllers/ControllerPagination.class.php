<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 26.04.2019
 * Time: 22:32
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\models\Pagination;
use application\parser\models\FolderChecker;

class ControllerPagination extends ControllerParserBase
{
    private $_pagination;

    public function __construct()
    {
        parent::__construct();
        $storageChecker = new FolderChecker($this->_settings->getStorage());
        $this->_pagination = new Pagination($storageChecker);
    }

    public function actionOpenPage(){
        $current_page = $_POST['current_page']; //Текущая выбрана страница
        $content['page_data'] = $this->_pagination->getPageData($current_page); //файлы которые будут отображены
        $this->loadPage('/parser/ajax/successed/pagination/pagination-content.page', $content);
    }
}