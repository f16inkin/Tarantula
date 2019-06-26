<?php


namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\models\ReportsUploader;

class ControllerReportsUploader extends ControllerParserBase
{
    private $_reports_uploader; //модель загрузчика отчетов

    /**
     * Создает объект загрузчика отчетов. Используется как единственная модель по работе с загрузкой отчетов.
     * ------------------------------------------------------------------------------------------------------
     * ControllerReportsUploader constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_reports_uploader = new ReportsUploader($this->_settings->getStorage(), $this->_settings->getUploadFileMaxSize());
    }

    /**
     * Обработчик загрузки файлов на сервер
     */
    public function actionUploadReports(){
        $files = $_FILES['file'];
        //Переданный массив files обработает класс ReportsUploader
        $result  = $this->_reports_uploader->upload($files);
        /**
         * Вернет массив с сообщениями и статусом выполнения процедуры загрузки
         * executionResult = [
         *      [0]=>[status => 'success', message => 'Файл загружен'],
         *      [1]=>[status => 'warning', message => 'Файл - двойник заменен'],
         *      [2]=>[status => 'fail', message => 'Файл имеет не верное расширение']
         * ];
         */
        echo json_encode($result);
    }

}