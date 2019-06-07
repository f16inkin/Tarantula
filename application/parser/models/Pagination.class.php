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
        //Если файлы удаляются с последней страницы
        if ($current_page == $last_page){
            //Если количество удаляемых файлов = количеству имеющихся на странице / полная очистка страницы
            if ($quantity == count($stack[$last_page])){
                $next_page_number = $current_page-1;
                //И если страница не первая, так как 1 - 1 = 0
                if ($next_page_number > 0){
                    $next_page = $stack[$next_page_number];
                    $uploaded_files['data'] = array_slice($next_page, 0, count($next_page));
                    $uploaded_files['page'] = $next_page_number;//
                    $uploaded_files['build'] = true;
                }
            }
            //Если удаляются не все файлы со страницы, а лишь часть, то остаюсь на этой же странице
            else{
                $uploaded_files['page'] = $current_page;
            }
        }
        //Удаление с любой не первой и последней страницы
        else{
            $previous_page = $stack[$current_page+1];
            //Если осталось на предэдущей странице 3 файла, а я удаляю 4, тоесть больше чем может подгрузиться.
            // То навигатор нужн оперестроить
            if (count($previous_page) <= $quantity ){
                $uploaded_files['build'] = true;
            }
            $uploaded_files['data'] = array_slice($previous_page, 0, $quantity);
            $uploaded_files['page'] = $current_page;
        }
        return $uploaded_files;
    }

    public function deleteFiles(string $storage, array $files){
        //Если файлы для подгрузки определены, то должен удалить указанные файлы
        $folder = $storage.'/'.$_SESSION['user']['id'].'-'.$_SESSION['user']['login'];
        if ($folder){
            foreach ($files as $singleFile){
                unlink($folder.'/'.$singleFile);
            }
            return true;
        }
        return false;
    }

}