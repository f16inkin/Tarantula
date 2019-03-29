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
use application\parser\models\XmlParser;

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

    /**
     * Подгружает подразделения в комбо боксе
     */
    public function actionGetSubdivisions(){
        $content = $this->_subdivisions;
        $this->loadPage('/parser/ajax/successed/tanks/select.page', $content);
    }

}