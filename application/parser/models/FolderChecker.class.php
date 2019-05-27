<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 10:11
 */

namespace application\parser\models;

use application\parser\interfaces\StorageChecker;

/**
 * Класс занимается проверкой хранилища (папки: storage)
 * ----------------------------------------------
 * Class FolderChecker
 * @package application\parser\models
 */
class FolderChecker implements StorageChecker
{
    private $_folder;

    public function __construct(string $storage)
    {
        $this->_folder = $storage.'/'.$_SESSION['user']['id'].'-'.$_SESSION['user']['login']; //Пользовательская папка
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
    private function createUserDirectory(string $folder){
        return mkdir($folder) ? true : false;
    }

    /**
     * Проверяет хранилище storage и в случае отсутсвия пользовательской директории создает ее
     * ---------------------------------------------------------------------------------------
     * @return bool
     */
    public function checkFolder(){
        if (!file_exists($this->_folder)){
            $this->createUserDirectory($this->_folder);
        }
        return true;
    }

    /**
     * Метод проверяет хранилище (папку/базу данных) и возвращает список файлов находящихся в нем в виде массива:
     * ----------------------------------------------------------------------------------------------------------
     * array = [0 => file_1, 1 => file_2, 2 => file_3]
     * @return array
     */
    public function scanStorage():array{
        $files = array_slice(scandir($this->_folder), 2);
        $converted_files = [];
        foreach ($files as $file){
            if (is_file($this->_folder.'/'.$file)){
                $converted_files[] = mb_convert_encoding($file, "UTF8", "Windows-1251");
            }else{
                rmdir($this->_folder.'/'.$file);
            }
        }
        return $converted_files;
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
}