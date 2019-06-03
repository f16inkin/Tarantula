<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 25.04.2019
 * Time: 19:56
 */

namespace application\parser\models;


use application\parser\interfaces\StorageChecker;

class Pagination
{
    private $_storage_checker;
    private $_files_per_page;

    /**
     * Pagination constructor.
     * Создает объект постраничного навигатора.
     * --------------------------------------
     * @param StorageChecker $storage_checker
     * @param int $files_per_page
     */
    public function __construct(StorageChecker $storage_checker, int $files_per_page)
    {
        $this->_storage_checker = $storage_checker; //Объект который сканирует хранилище данных (БД или папка)
        $this->_files_per_page = $files_per_page;   //Количество выводимых на страницу файлов
    }

    public function build(){
        $pages_count = ceil($this->_storage_checker->getFilesCount() / $this->_files_per_page);
        return $pages_count;
    }

    public function getPageData(int $current_page = 1){
        //Получаю файлы из хранилища в массив
        $files = $this->_storage_checker->scanStorage();
        //Вычисляю первый файл в массиве
        $start_file = ($current_page - 1) * $this->_files_per_page;
        //Получаю массив файлов, которые будут выведены на страницу
        $files_in_page = array_slice($files, $start_file, $this->_files_per_page);
        return $files_in_page;
    }

    public function getCustomPageData(int $quantity, int $current_page = 1){
        //Получаю файлы из хранилища в массив
        $files = $this->_storage_checker->scanStorage();
        //Формирую массив разбитый по страницам
        $stack = array_chunk($files, 10);
        //
        $p = $current_page - 1;
        //Выбираю нужную страницу
        @$page = $stack[$p];
        if (isset($page)){
            $start_file_index = count($page) - $quantity;
        }else{
            $p = $current_page - 2;
            $page = $stack[$p];
            $start_file_index = 0;
            $quantity = 9;
        }
        //Выбираю страницу с которой нужно подгрузить файлы, это следующая страница
        $uploaded_files = array_slice($page, $start_file_index, $quantity);
        //Загрузка
        return $uploaded_files;
    }

}