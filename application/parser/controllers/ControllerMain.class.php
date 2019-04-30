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

    public function actionIndex(){
        //Обозначаю хранилище
        $storageChecker = new FolderChecker($this->_settings->getStorage());
        if($storageChecker->checkFolder()){
            $files = $storageChecker->scanStorage();
            if (!empty($files)){
                //Временная переменная для обозначения лимита файлов в пользовательской директории
                $files_limit = $this->_settings->getFilesLimit();
                $files_count = count($files);
                $content['files_array'] = $files;
                $content['files_count'] = $files_count;
                /**
                 * Везде где мне нужно вывести пагинацию, я параметром передаю чекер хранилища Folder/MySQL - checker
                 * Теперь я могу на одной странице иметь два разных пагинатора, с разными источниками (хранилищами) данных
                 */
                $content['storage_checker'] = $storageChecker;
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