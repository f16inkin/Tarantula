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
    private $_files_per_page;
    private $_folder;
    private $_storage;

    /**
     * Pagination constructor.
     * Создает объект постраничного навигатора.
     * --------------------------------------
     * @param StorageChecker $storage_checker
     * @param int $files_per_page
     */
    public function __construct(string $storage, int $files_per_page)
    {
        $this->_files_per_page = $files_per_page;   //Количество выводимых на страницу файлов
        $this->_folder = $storage.'/'.$_SESSION['user']['id'].'-'.$_SESSION['user']['login']; //Пользовательская папка
        $this->_storage = $storage;
        //Если отсутсвует папка хранилище создаст ее. Пока пусть будет, но в планах удалить эту проверку
        //При автоматической установке модуля создавать нужную директорию
        if (!file_exists($storage)){
            mkdir($storage);
        }
    }

    /**
     * Создает папку на основе id-login пользователя
     * ---------------------------------------------
     * @param string $folder
     * @return bool
     */
    private function createUserDirectory(string $folder) : bool {
        return mkdir($folder);
    }

    /**
     * Проверяет хранилище storage и в случае отсутсвия пользовательской директории создает ее.
     * Если папка уже создана, то вернет true. Иначе попробует создать папку и вернет результат операции true/false
     * ------------------------------------------------------------------------------------------------------------
     * @return bool
     */
    public function checkFolder() : bool {
        if (!file_exists($this->_folder)){
            return $this->createUserDirectory($this->_folder);
        }
        return true;
    }

    /**
     * Метод не используется в цикле. А значит достаточно разового создания обработчика файлов по нужде его
     * использования. В конструкторе объект XmlReportsHandler потому и не создается. В метод аргументом он тоже
     * не передается через интерфейс, потому что
     * @return array может быть толькоодин обработчик xml отчетов
     */
    public function scanStorage($start, $end) : array {
        //$files = (new XmlReportsHandler($this->_storage))->loadCorrectXml();
        $files = (new XmlReportsHandler($this->_storage))->loadXmlPage($start,$end);
        return $files;
    }

    /**
     * Метод вернет подсчитаное количество файлов находящихся в хранилище
     * ------------------------------------------------------------------
     * @return int
     */
    public function getFilesCount(): int
    {
        return count(array_slice(scandir($this->_folder), 2));
    }

    /**
     * Вычисляет количество страниц для навигации, с учетом количества файлов и их лимита на страницу
     * ----------------------------------------------------------------------------------------------
     * @return int
     */
    public function getPagesCount() : int {
        $filesCount = $this->getFilesCount();
        $pagesCount = ceil($filesCount / $this->_files_per_page);
        return $pagesCount;
    }

    /**
     * Загружает файлы указанной страницы.
     * @param int $current_page
     * @return array
     */
    public function loadPage(int $current_page) : array {
        //Вычисляю первый файл в массиве
        $startFile = ($current_page - 1) * $this->_files_per_page;
        //Получаю файлы из хранилища в массив
        $page = $this->scanStorage($startFile, $this->_files_per_page);
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
        $files = $this->scanStorage(1,10);
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