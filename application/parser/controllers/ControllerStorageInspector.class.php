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
use application\parser\models\StorageInspector;
use application\parser\models\FolderChecker;

/**
 * Класс инспектирует хранилище
 * Class ControllerStorageInspector
 * @package application\parser\controllers
 */
class ControllerStorageInspector extends ControllerParserBase
{
    private $_inspector;       //объект инспектора
    private $_storage_checker;  //объект проверяющий хранилище, где хранятся файлы

    /**
     * ControllerPagination constructor.
     * --------------------------------
     * @param string $storageCheckerId
     */
    public function __construct(string $storageCheckerId)
    {
        parent::__construct();
        switch ($storageCheckerId){
            case 'folder' : $this->_storage_checker = new  FolderChecker($this->_settings->getStorage()); break;
            case 'mysql' : $this->_storage_checker = new  MySQLChecker(); break;
        }
        $this->_inspector = new StorageInspector($this->_storage_checker, $this->_settings->getFilesPerPage());
    }

    /**
     * Подгужает информацию о файлах условно находящихся на указанной странице
     */
    public function actionLoadPage(){
        $currentPage = $_POST['current_page']; //Текущая выбрана страница
        $files = $this->_inspector->loadPage($currentPage); //файлы которые будут отображены
        if (!empty($files)){
            $filesCount = $this->_storage_checker->getFilesCount();
            $filesLimit = $this->_settings->getFilesLimit();
            $content['status'] = 'success';
            $content['message'] = 'Страница загружена';
            $content['page_data'] = $files;
            $content['files_limit'] = $filesLimit;
            $content['files_count'] = $filesCount;
        }else{
            $content['status'] = 'fail';
            $content['message'] = 'Не найдены файлы для загрузки';
        }
        echo json_encode($content);
    }

    /**
     * Вернет количество страниц навигатора, если в директории есть файлы.
     */
    public function actionGetPagesCount(){
        $pages = $this->_inspector->getPagesCount();
        echo json_encode($pages);
    }

    /**
     * Удаляет файлы из пользовательской директории. Если в директории еще остались файлы, то подгружает их.
     */
    public function actionDisplaceFiles(){
        $files = $_POST['file_names'];
        //Если известны удаляемые файлы
        if (!empty($files)){
            //Определяю количество страниц до удаления
            $pagesCountBefore = $this->_inspector->getPagesCount();
            $currentPage = $_POST['current_page'];
            $quantity = $_POST['quantity'];
            //Вычисляю файлы которые нужно загрузить на страницу, номер страницы, а так же макрер для построения навигатора
            $uploadedFiles = $this->_inspector->loadFiles($quantity, $currentPage);
            //Если все файлы успешно удалены с хранилища
            if ($this->_inspector->deleteFiles($this->_settings->getStorage(), $files)){
                //Определяю количество страниц после удаления и в случае если их стало меньще посылаю маркер о том,
                // что нужно перегрузить навигатор
                $pagesCountAfter = $this->_inspector->getPagesCount();
                if ($pagesCountAfter < $pagesCountBefore){
                    $uploadedFiles['build'] = true;
                }
                //Определяю количество страниц после удаления

                $filesCount = $this->_storage_checker->getFilesCount();
                $filesLimit = $this->_settings->getFilesLimit();
                $content['status'] = 'success';
                $content['message'] = 'Удаление прошло успешно';
                $content['data']['uploaded_files'] = $uploadedFiles;
                $content['data']['files_limit'] = $filesLimit;
                $content['data']['files_count'] = $filesCount;

            }else{
                //Примерный вид ответа
                $content['status'] = 'fail';
                $content['message'] = 'Не удалось удалить файлы';
                $content['data'] = [];
            }
        }else{
            //Примерный вид ответа
            $content['status'] = 'fail';
            $content['message'] = 'Ошибка при обработке хранилища. Файлы не найдены';
            $content['data'] = [];
        }
        //В итоге верну такой ответ в виде JSON
        echo json_encode($content);
    }
}