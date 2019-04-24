<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 10:20
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\models\StorageChecker;

class ControllerMain extends ControllerParserBase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function actionIndex(){
        //Обозначаю хранилище
        $storageBuilder = new StorageChecker($this->_settings->getStorage());
        if($storageBuilder->checkFolder()){
            $files = $storageBuilder->scanFolder();
            if (!empty($files)){
                //Временная переменная для обозначения лимита файлов в пользовательской директории
                $files_limit = $this->_settings->getFilesLimit();
                $files_count = count($files);
                $content['files_array'] = $files;
                $content['files_count'] = $files_count;
                if ($files_count > $files_limit){
                    $content['files_limit'] = $files_limit;
                    $this->loadPage('/parser/ajax/successed/main/step-1-excess-files.page', $content);
                }else{
                    $this->loadPage('/parser/ajax/successed/main/step-1-with-files.page', $content);
                }
            }else{
                $this->loadPage('/parser/ajax/successed/main/step-1-without-files.page');
            }
        }
    }

}