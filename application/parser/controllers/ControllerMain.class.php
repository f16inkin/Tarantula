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
       $storageBuilder = new StorageChecker();
       if($storageBuilder->checkFolder()){
           $content = $storageBuilder->scanFolder();
           if (!empty($content)){
               $this->loadPage('/parser/ajax/successed/main/step-1-with-files.page', $content);
           }else{
               $this->loadPage('/parser/ajax/successed/main/step-1-without-files.page', $content);
           }
       }
    }

}