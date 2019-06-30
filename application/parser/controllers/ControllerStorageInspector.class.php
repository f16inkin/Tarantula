<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 26.04.2019
 * Time: 22:32
 */

namespace application\parser\controllers;


use application\parser\base\ControllerParserBase;
use application\parser\models\StorageInspector;
use application\parser\models\XmlSessionsSectionHandler;

/**
 * Класс инспектирует хранилище
 * ---------------------------------------
 * Class ControllerStorageInspector
 * @package application\parser\controllers
 */
class ControllerStorageInspector extends ControllerParserBase
{
    private $_inspector;    //объект инспектора
    private $_storage;      //путь к  хранилищу

    /**
     * ControllerPagination constructor.
     * --------------------------------
     * @param string $storageCheckerId
     */
    public function __construct()
    {
        parent::__construct();
        $this->_storage = $this->_settings->getStorage();
        $this->_inspector = new StorageInspector($this->_storage, $this->_settings->getFilesPerPage());
    }

    /**
     * Подгужает информацию о файлах условно находящихся на указанной странице
     */
    public function actionLoadPage(){
        $currentPage = $_POST['current_page']; //Текущая выбрана страница
        $files = $this->_inspector->loadPage($currentPage); //файлы которые будут отображены
        $sessionHandler = new XmlSessionsSectionHandler();
        $i = 0;
        foreach ($files as $file){
            $i ++;
            //Важно проверить наличии SXE, иначе если файл битый или некорректный будет выбрасывать ошибку
            if (isset($file['simpleXmlElement'])){
                $data[$i]['session'] = $sessionHandler->get($file['simpleXmlElement']);
                $data[$i]['session']['Status'] = 'correct';
            }
            else{
                $data[$i]['session'] = null;
                $data[$i]['session']['Status'] = 'incorrect';
            }

            $data[$i]['file_name'] = $file['file_name'];
        }
        if (!empty($files)){
            $filesCount = $this->_inspector->getFilesCount();
            $filesLimit = $this->_settings->getFilesLimit();
            $content['status'] = 'success';
            $content['message'] = 'Страница загружена';
            $content['page_data'] = $data;
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
            $pagesCountBefore = $this->_inspector->getPagesCount();
            $currentPage = $_POST['current_page'];
            $deleted_quantity = $_POST['quantity'];
            //Вычисляю файлы которые нужно загрузить на страницу
            $loadedFiles = $this->_inspector->loadFiles($currentPage, $deleted_quantity);
            $loadedFiles['build'] = false;
            $data = null;//$loadedFiles['data'];
            //Если все файлы успешно удалены с хранилища
            if ($this->_inspector->deleteFiles($this->_settings->getStorage(), $files)){
                //Определяю количество страниц после удаления и в случае если их стало меньще посылаю маркер о том,
                // что нужно перегрузить навигатор
                $pagesCountAfter = $this->_inspector->getPagesCount();
                if ($pagesCountAfter < $pagesCountBefore){
                    $loadedFiles['build'] = true;
                }
                //------------------------------------------------------------------------------------------------
                $sessionHandler = new XmlSessionsSectionHandler();
                $i = 0;
                if (isset($loadedFiles['data'])){
                    foreach ($loadedFiles['data'] as $file){
                        $i ++;
                        //Важно проверить наличии SXE, иначе если файл битый или некорректный будет выбрасывать ошибку
                        if (isset($file['simpleXmlElement'])){
                            $data[$i]['session'] = $sessionHandler->get($file['simpleXmlElement']);
                            $data[$i]['session']['Status'] = 'correct';
                        }
                        else{
                            $data[$i]['session'] = null;
                            $data[$i]['session']['Status'] = 'incorrect';
                        }
                        $data[$i]['file_name'] = $file['file_name'];
                    }

                }
                /**
                 * Загрузка
                 */
                $filesCount = $this->_inspector->getFilesCount();
                $filesLimit = $this->_settings->getFilesLimit();
                $content['status'] = 'success';
                $content['message'] = 'Страница загружена';
                $content['page_data'] = $data;
                $content['files_limit'] = $filesLimit;
                $content['files_count'] = $filesCount;
                $content['page'] = $loadedFiles['page'];
                $content['build'] = $loadedFiles['build'];
            }
            /**
             * Если файлы удалить не удалось, знач
             */
            else{
                //Примерный вид ответа
                $content['status'] = 'fail';
                $content['message'] = 'Не удалось удалить файлы';
            }
        }else{
            //Примерный вид ответа
            $content['status'] = 'fail';
            $content['message'] = 'Ошибка при обработке хранилища. Файлы не найдены';
        }
        //В итоге верну такой ответ в виде JSON
        echo json_encode($content);

    }
}