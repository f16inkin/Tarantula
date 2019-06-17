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


    public function actionUploadReports(){
        $files = $_FILES;
        $result  = $this->_reports_uploader->upload($files);
        echo json_encode($result);
    }

}