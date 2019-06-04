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
        //Приравниваю количество требуемых к подгрузке файлов к текущему лимиту на странице, если оно равно 0.
        if ($quantity == 0){$quantity = $this->_files_per_page;}
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


        /*if ($quantity == 0){$quantity = $this->_files_per_page;}
        //Получаю файлы из хранилища в массив
        $files = $this->_storage_checker->scanStorage();
        //Формирую массив разбитый по страницам
        $stack = array_chunk($files, $this->_files_per_page);
        //Если в папке нету файлов, то стэк будет пустым, а значит загружать на страницу нечего
        if (empty($stack)){
            return $uploaded_files = [];
        }else{
            //Формирую ключи для массива начиная с 1. $keys = [1, 2, ...., N];
            $keys = range(1, count($stack));
            //Обновляю ключи
            $stack = array_combine($keys, $stack);
            //Нахожу последний инлекс в массиве равный последней странице
            end($stack);
            //Нахожу последнюю страницу в стеке
            $last_page = key($stack);
            //Если текущая страница существует после удаления файлов
            if(array_key_exists($current_page, $stack)){
                //Выбираю ее из стека
                $page = $stack[$current_page];
                //Если текущая страница и есть последняя
                if ($current_page == $last_page){
                   //Здесь я доложен определить нету ли еще файлов для подгрузки
                    if ($current_page == 1){
                        $start_file_index = count($page) - $quantity;
                    }else{
                        return $uploaded_files = [];
                    }
                }else{
                    $start_file_index = count($page) - $quantity;
                }
            }
            //Если файлов для подгрузки нет, так как была очещена полностью страница
            else{
                $page = $stack[$current_page-1];
                $start_file_index = 0;
                $quantity = $this->_files_per_page;
            }
            $uploaded_files = array_slice($page, $start_file_index, $quantity);
            return $uploaded_files;
        }*/
    }

}