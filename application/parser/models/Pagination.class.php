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
        $files_count = count($this->_storage_checker->scanStorage());
        $pages_count = ceil($files_count / $this->_files_per_page);
        return $pages_count;
    }

    public function getPageData(int $current_page = 1){
        //Получаю файлы из хранилища в массив
        $files = $this->_storage_checker->scanStorage();
        //Подсчитываю количество файлов
        $files_count = count($files);
        //Вычисляю количество страниц
        $pages_count = ceil($files_count / $this->_files_per_page);
        //Вычисляю первый файл в массиве
        $start_file = ($current_page - 1) * $this->_files_per_page;
        //Получаю массив файлов, которые будут выведены на страницу
        $files_in_page = array_slice($files, $start_file, $this->_files_per_page);
        return $files_in_page;
    }

}