<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 10:20
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\models\StorageInspector;
use application\parser\models\XmlReportsHandler;
use application\parser\models\XmlSectionHandlersFactory;

class ControllerMain extends ControllerParserBase
{
    public function __construct()
    {
        parent::__construct();
    }

    private function scanStorage(){
        $storageInspector = new StorageInspector($this->_settings->getStorage(), $this->_settings->getFilesPerPage());
        if($storageInspector->checkFolder()){
            $files_count = $storageInspector->getFilesCount();//scanStorage();
            if ($files_count > 0){
                //Временная переменная для обозначения лимита файлов в пользовательской директории
                $files_limit = $this->_settings->getFilesLimit();
                //$files_count = count($files);
                $files_per_page = $this->_settings->getFilesPerPage();
                $content['files_count'] = $files_count;
                $content['allow_pagination'] = ($files_count > $files_per_page) ? true : false;
                //$content['storage_checker_id'] = 1;
                $content['files_limit'] = $files_limit;
                return $content; //success and data
            }
            return null; //warning and message
        }
        return 'Ошибка создания папки пользователя'; //error and message
    }

    /**
     *
     */
    public function actionIndex(){
        $this->loadPage('/parser/ajax/successed/main/main.page');
    }

    public function actionFirstStep(){
        $content['files'] = $this->scanStorage();
        $content['upload_limit'] = ini_get('max_file_uploads');
        $content['max_file_size'] = $this->_settings->getUploadFileMaxSize();
        $this->loadPage('/parser/ajax/successed/main/step-1/step-1.page', $content);

    }
    public function actionSecondStep(){
        $content = $this->scanStorage();
        if (isset($content)){
            $this->loadPage('/parser/ajax/successed/main/step-2/step-2.page', $content);
        }else{
            $content['upload_limit'] = ini_get('max_file_uploads');
            $content['max_file_size'] = $this->_settings->getUploadFileMaxSize();
            $this->loadPage('/parser/ajax/successed/main/step-1/step-1.page', $content);
        }
    }

    public function actionThirdStep(){
        $this->loadPage('/parser/ajax/successed/main/step-3/step-3.page');
    }

    /**
     *
     */
    /*public function actionGetStarted(){
        $content = $this->scanStorage();
        if (isset($content)){
            $this->loadPage('/parser/ajax/successed/main/step-2/step-2.page', $content);
        }else{
            $content['upload_limit'] = ini_get('max_file_uploads');
            $this->loadPage('/parser/ajax/successed/main/step-1/step-1.page', $content);
        }
    }*/

    public function actionGetSessionData(){
        $subdivisionId = $_POST['subdivision_id'];
        $fileName = $_POST['file_name'];
        $xmlSectionHandlesFactory = new XmlSectionHandlersFactory($subdivisionId);
        //Файл SXE
        $SXE = (new XmlReportsHandler($this->_settings->getStorage()))->loadXmlFile($fileName);
        //Обработка одно файла
        if ($SXE){
            $handled = $xmlSectionHandlesFactory->handle($SXE);
            $content['session'] = $handled->_sessions;
            $content['tanks'] = $handled->_tanks;
            $content['hoses'] = $handled->_hoses;
            $content['outcomes'] = $handled->_outcomes;
            $this->loadPage('/parser/ajax/successed/main/step-2/correct.page', $content);
        }else{
            $this->loadPage('/parser/ajax/successed/main/step-2/incorrect.page');
        }


    }
}