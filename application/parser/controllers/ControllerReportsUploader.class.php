<?php


namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\models\FolderChecker;
use application\parser\models\ReportsUploader;

class ControllerReportsUploader extends ControllerParserBase
{
    private $_reports_uploader;

    public function __construct()
    {
        parent::__construct();
        $this->_reports_uploader = new ReportsUploader($this->_settings->getStorage());
    }

    private function scanStorage(){
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
                $content['files_limit'] = $files_limit;
                return $content; //success and data
            }
            return null; //warning and message
        }
        return 'Ошибка создания папки пользователя'; //error and message
    }

    public function actionUploadReports(){
        $files = $_FILES;
        $this->_reports_uploader->upload($files);
        $content = $this->scanStorage();
        $this->loadPage('/parser/ajax/successed/main/step-1/step-1-with-files.page', $content);
    }

}