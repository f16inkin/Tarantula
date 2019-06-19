<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 25.04.2019
 * Time: 19:56
 */

namespace application\parser\models;


use application\parser\interfaces\StorageChecker;

class StorageInspector
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

    /**
     * Вычисляет количество страниц для навигации, с учетом количества файлов и их лимита на страницу
     * ----------------------------------------------------------------------------------------------
     * @return int
     */
    public function getPagesCount() : int {
        $pagesCount = ceil($this->_storage_checker->getFilesCount() / $this->_files_per_page);
        return $pagesCount;
    }

    /**
     * Загружает файлы указанной страницы.
     * @param int $current_page
     * @return array
     */
    public function loadPage(int $current_page) : array {
        //Получаю файлы из хранилища в массив
        $files = $this->_storage_checker->scanStorage();
        //Вычисляю первый файл в массиве
        $startFile = ($current_page - 1) * $this->_files_per_page;
        //Получаю массив файлов, которые будут выведены на страницу
        $page = array_slice($files, $startFile, $this->_files_per_page);
        return $page;
    }

    /**
     * Метод получает файлы которые нужно подгрузить. Расчет происходит исходя из количества удаляемых файлов и текущей
     * страницы, с которой происодит удаление
     * ----------------------------------------------------------------------------------------------------------------
     * @param int $quantity - количество удаленных файлов
     * @param int $current_page - страница с которой удалялись файлы
     * @return array
     */
    public function loadFiles(int $quantity, int $current_page) : array {
        //Массив с именами подгружаемых файлов
        $loadedFiles = [];
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
        $lastPage = key($stack);
        //Если файлы удаляются с последней страницы
        if ($current_page == $lastPage){
            //Если количество удаляемых файлов = количеству имеющихся на странице / полная очистка страницы
            if ($quantity == count($stack[$lastPage])){
                $nextPageNumber = $current_page-1;
                //И если страница не первая, так как 1 - 1 = 0
                if ($nextPageNumber > 0){
                    $next_page = $stack[$nextPageNumber];
                    $loadedFiles['data'] = array_slice($next_page, 0, count($next_page));
                    $loadedFiles['page'] = $nextPageNumber;//
                    $loadedFiles['build'] = true;
                }
            }
            //Если удаляются не все файлы со страницы, а лишь часть, то остаюсь на этой же странице
            else{
                $loadedFiles['page'] = $current_page;
            }
        }
        //Удаление с любой не первой и последней страницы
        else{
            $previousPage = $stack[$current_page+1];
            //Если осталось на предэдущей странице 3 файла, а я удаляю 4, тоесть больше чем может подгрузиться.
            // То навигатор нужн оперестроить
            if (count($previousPage) <= $quantity ){
                $loadedFiles['build'] = true;
            }
            $loadedFiles['data'] = array_slice($previousPage, 0, $quantity);
            $loadedFiles['page'] = $current_page;
        }
        return $loadedFiles;
    }

    /**
     * Удаляет выбранные файлы из указанной директории
     * -----------------------------------------------
     * @param string $storage
     * @param array $files
     * @return bool
     */
    public function deleteFiles(string $storage, array $files) : bool {
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