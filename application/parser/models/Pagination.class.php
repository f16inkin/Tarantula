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
    const LIMIT = 10; //Количесво записей выводимы на странице

    public function __construct(StorageChecker $storage_checker)
    {
        $this->_storage_checker = $storage_checker;
    }

    public function build($per_page = self::LIMIT){
        $files_count = count($this->_storage_checker->scanStorage());
        $pages_count = ceil($files_count/$per_page);
        return $pages_count;
    }

    public function getPageData(int $current_page = 1, int $per_page = self::LIMIT){
        //Получаю файлы из хранилища в массив
        $files = $this->_storage_checker->scanStorage();
        //Подсчитываю количество файлов
        $files_count = count($files);
        //Вычисляю количество страниц
        $pages_count = ceil($files_count / $per_page);
        //Вычисляю первый файл в массиве
        $start_file = ($current_page - 1) * $per_page;
        //Получаю массив файлов, которые будут выведены на страницу
        $files_in_page = array_slice($files, $start_file, $per_page);
        return $files_in_page;
    }

}