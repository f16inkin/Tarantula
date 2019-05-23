<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 10:20
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\models\Pagination;
use application\parser\models\FolderChecker;

class ControllerMain extends ControllerParserBase
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function actionIndex(){
        $this->loadPage('/parser/ajax/successed/main/main.page');

    }

    public function actionFirstStep(){
        //Обозначаю хранилище
        $folderChecker = new FolderChecker($this->_settings->getStorage());
        if($folderChecker->checkFolder()){
            $files = $folderChecker->scanStorage();
            if (!empty($files)){
                //Временная переменная для обозначения лимита файлов в пользовательской директории
                $files_limit = $this->_settings->getFilesLimit();
                $files_count = count($files);
                $files_per_page = $this->_settings->getFilesPerPage();
                $content['files_count'] = $files_count;
                $content['allow_pagination'] = ($files_count > $files_per_page) ? true : false;
                $content['storage_checker_id'] = 1;
                $content['files_limit'] = $files_limit;
                if ($files_count > $files_limit){
                    $this->loadPage('/parser/ajax/successed/main/step-1/step-1-excess-files.page', $content);
                }else{
                    $this->loadPage('/parser/ajax/successed/main/step-1/step-1-with-files.page', $content);
                }
            }else{
                $this->loadPage('/parser/ajax/successed/main/step-1/step-1-without-files.page');
            }
        }
    }
}