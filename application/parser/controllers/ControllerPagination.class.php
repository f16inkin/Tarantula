<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 26.04.2019
 * Time: 22:32
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\interfaces\StorageChecker;
use application\parser\models\MySQLChecker;
use application\parser\models\Pagination;
use application\parser\models\FolderChecker;

class ControllerPagination extends ControllerParserBase
{
    private $_pagination;
    private $_storage_checker;

    public function __construct(int $storageCheckerId)
    {
        parent::__construct();
        switch ($storageCheckerId){
            case 1 : $this->_storage_checker = new  FolderChecker($this->_settings->getStorage()); break;
            case 2 : $this->_storage_checker = new  MySQLChecker(); break;
        }
        $this->_pagination = new Pagination($this->_storage_checker);
    }

    public function actionOpenPage(){
        $current_page = $_POST['current_page']; //Текущая выбрана страница
        $content['page_data'] = $this->_pagination->getPageData($current_page); //файлы которые будут отображены
        $this->loadPage('/parser/ajax/successed/main/pagination-content.page', $content);
    }

    public function actionBuild(StorageChecker $storageChecker){
        $pagination = new Pagination($storageChecker);
        $content['pagination'] = $pagination->build();
        $this->loadPage('/parser/ajax/successed/main/pagination.page', $content);
    }
}