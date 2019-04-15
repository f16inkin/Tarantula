<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 29.03.2019
 * Time: 11:13
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\models\Tanks;

class ControllerTanks extends ControllerParserBase
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Загружает страницу емкостей, а так же доступные пользователю подразделения
     */
    public function actionIndex(){
        $content = $this->_subdivisions;
        $this->loadPage('/parser/ajax/successed/tanks/tanks.page', $content);
    }

    /**
     * Загружает данные собранные из XML файлов
     */
    public function actionGetData(){
        $subdivision = $_POST['subdivision'];
        $content = (new Tanks($subdivision))->getTanksData($this->_storage);
        $this->loadPage('/parser/ajax/successed/tanks/tanks-data.page', $content);
    }

    public function actionInsertData(){
        $subdivision = $_POST['subdivision'];
        $tanks = new Tanks($subdivision);
        $data = $tanks->getTanksData($this->_storage);
        foreach ($data as $singleFile) {
            if($tanks->insertTanksData($singleFile)){
                echo 'Файл обработан';
                echo '<br>';
            }
            else{
                echo 'Ошибка загрузки файла';
                echo '<br>';
            }
        }
    }

    /**
     * Подгружает подразделения в комбо боксе
     */
    public function actionGetSubdivisions(){
        $content = $this->_subdivisions;
        $this->loadPage('/parser/ajax/successed/tanks/select.page', $content);
    }

}