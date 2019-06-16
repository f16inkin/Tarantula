<?php


namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;

class ControllerReportsUploader extends ControllerParserBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function actionUploadReports(){
        $files = $_FILES;
    }

}