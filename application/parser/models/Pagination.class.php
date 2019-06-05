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

    public function getPagesCount(){
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

    public function getCustomPageData(int $quantity, int $current_page) : array {
        //Массив с именами подгружаемых файлов
        $uploaded_files = [];
        //Сканирую хранилище на наличие файлов
        $files = $this->_storage_checker->scanStorage();
        /**
         * Разбиваю массив файлов, на страницы
         * $stack = [0 => [file1, file2, file3], 1 => [file4, file5, file6]];
         */
        $stack = array_chunk($files, $this->_files_per_page);
        /**
         * Ключи для формирования новых индексов массива в соответсвии со страницами
         * $stack = [1 => [file1, file2, file3], 2 => [file4, file5, file6];
         */
        $keys = range(1, count($stack));
        //Переиндексирую ключи
        $stack = array_combine($keys, $stack);
        //Нахожу последний инлекс в массиве равный последней странице
        end($stack);
        $last_page = key($stack);
        if ($current_page == $last_page){
            if ($quantity == count($stack[$last_page])){
                $next_page_number = $current_page-1;
                if ($next_page_number >0){
                    $next_page = $stack[$next_page_number];
                    $uploaded_files = array_slice($next_page, 0, count($next_page));
                    //return $uploaded_files;
                }//else{
                 //   return $uploaded_files = [];
                //}
            }//else{
                //return $uploaded_files = [];
            //}

        }else{
            $previous_page = $stack[$current_page+1];
            $uploaded_files = array_slice($previous_page, 0, $quantity);
            //return $uploaded_files;
        }
        return$uploaded_files;
    }

}