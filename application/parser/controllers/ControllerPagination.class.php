<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 26.04.2019
 * Time: 22:32
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
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
    public function __construct(string $storageCheckerId)
    {
        parent::__construct();
        switch ($storageCheckerId){
            case 'folder' : $this->_storage_checker = new  FolderChecker($this->_settings->getStorage()); break;
            case 'mysql' : $this->_storage_checker = new  MySQLChecker(); break;
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
        echo json_encode($content);
    }

    public function actionGetPagesCount(){
        $pages = $this->_pagination->getPagesCount();
        echo json_encode($pages);
    }

    public function actionDeleteAndUpload(){
        $files = $_POST['file_names'];
        if (!empty($files)){
            //Тут вся движуха по удалению и подгрузке
        }

        $current_page = $_POST['current_page'];
        $quantity = $_POST['quantity'];
        $uploaded_files = $this->_pagination->getCustomPageData($quantity, $current_page);


        //Если файлы для подгрузки определены, то должен удалить указанные файлы
        $storage = $this->_settings->getStorage();
        $folder = $storage.'/'.$_SESSION['user']['id'].'-'.$_SESSION['user']['login'];
        foreach ($files as $singleFile){
            unlink($folder.'/'.$singleFile);
        }
        $files_count = $this->_storage_checker->getFilesCount();
        $files_limit = $this->_settings->getFilesLimit();
        $content['uploaded_files'] = $uploaded_files;
        $content['files_limit'] = $files_limit;
        $content['files_count'] = $files_count;
        echo json_encode($content);
    }
}