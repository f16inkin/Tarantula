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

    /**
     * ControllerPagination constructor.
     * --------------------------------
     * @param int $storageCheckerId
     */
    public function __construct(int $storageCheckerId)
    {
        parent::__construct();
        switch ($storageCheckerId){
            case 1 : $this->_storage_checker = new  FolderChecker($this->_settings->getStorage()); break;
            case 2 : $this->_storage_checker = new  MySQLChecker(); break;
        }
        $this->_pagination = new Pagination($this->_storage_checker, $this->_settings->getFilesPerPage());
    }

    public function actionOpenPage(){
        $current_page = $_POST['current_page']; //Текущая выбрана страница
        $files = $this->_pagination->getPageData($current_page); //файлы которые будут отображены
        $files_count = $this->_storage_checker->getFilesCount();
        $files_limit = $this->_settings->getFilesLimit();
        $content['page_data'] = $files;
        $content['files_limit'] = $files_limit;
        $content['files_count'] = $files_count;
        //$this->loadPage('/parser/ajax/successed/main/pagination/pagination-content.page', $content);
        echo json_encode($content);
    }

    public function actionBuild(){
        $content['pagination'] = $this->_pagination->build();
        $this->loadPage('/parser/ajax/successed/main/pagination/pagination.page', $content);
    }
}