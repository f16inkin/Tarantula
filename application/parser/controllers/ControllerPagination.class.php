<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 26.04.2019
 * Time: 22:32
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\models\MySQLChecker;
use application\parser\models\Pagination;
use application\parser\models\FolderChecker;

class ControllerPagination extends ControllerParserBase
{
    private $_pagination;
    private $_storage_checker;

    /**
     * ControllerPagination constructor.
     * --------------------------------
     * @param int $storageCheckerId
     */
    public function __construct(string $storageCheckerId)
    {
        parent::__construct();
        switch ($storageCheckerId){
            case 'folder' : $this->_storage_checker = new  FolderChecker($this->_settings->getStorage()); break;
            case 'mysql' : $this->_storage_checker = new  MySQLChecker(); break;
        }
        $this->_pagination = new Pagination($this->_storage_checker, $this->_settings->getFilesPerPage());
    }

    public function actionOpenPage(){
        $current_page = $_POST['current_page']; //Текущая выбрана страница
        $files = $this->_pagination->getPageData($current_page); //файлы которые будут отображены
        $files_count = $this->_storage_checker->getFilesCount();
        $files_limit = $this->_settings->getFilesLimit();
        $content['page_data'] = $files;
        $content['files_limit'] = $files_limit;
        $content['files_count'] = $files_count;
        echo json_encode($content);
    }

    public function actionGetPagesCount(){
        $pages = $this->_pagination->getPagesCount();
        echo json_encode($pages);
    }

    public function actionDeleteAndUpload(){
        $files = $_POST['file_names'];
        //Если известны удаляемые файлы
        if (!empty($files)){
            //Определяю количество страниц до удаления
            $pages_count_before = $this->_pagination->getPagesCount();
            $current_page = $_POST['current_page'];
            $quantity = $_POST['quantity'];
            //Вычисляю файлы которые нужно загрузить на страницу, номер страницы, а так же макрер для построения навигатора
            $uploaded_files = $this->_pagination->getCustomPageData($quantity, $current_page);
            //Если все файлы успешно удалены с хранилища
            if ($this->_pagination->deleteFiles($this->_settings->getStorage(), $files)){
                //Определяю количество страниц после удаления и в случае если их стало меньще посылаю маркер о том,
                // что нужно перегрузить навигатор
                $pages_count_after = $this->_pagination->getPagesCount();
                if ($pages_count_after < $pages_count_before){
                    $uploaded_files['build'] = true;
                }
                //Определяю количество страниц после удаления

                $files_count = $this->_storage_checker->getFilesCount();
                $files_limit = $this->_settings->getFilesLimit();
                $content['status'] = 'success';
                $content['message'] = 'Удаление прошло успешно';
                $content['data']['uploaded_files'] = $uploaded_files;
                $content['data']['files_limit'] = $files_limit;
                $content['data']['files_count'] = $files_count;

            }else{
                //Примерный вид ответа
                $content['status'] = 'failed';
                $content['message'] = 'Не удалось удалить файлы';
                $content['data'] = [];
            }
        }else{
            //Примерный вид ответа
            $content['status'] = 'failed';
            $content['message'] = 'Ошибка при обработке хранилища. Файлы не найдены';
            $content['data'] = [];
        }
        //В итоге верну такой ответ в виде JSON
        echo json_encode($content);
    }
}