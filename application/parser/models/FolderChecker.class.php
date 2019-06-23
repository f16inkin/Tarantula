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
 * Класс занимается проверкой хранилища (папки: storage):
 * - Проверяет наличие пользовательской директории.
 * - Сканирует пользовательскую директорию на наличие файлов. Возвращает конвертированныев UTF-8 файлы.
 * - Возвращает количество файлов в директории.
 * ----------------------------------------------
 * Class FolderChecker
 * @package application\parser\models
 */
class FolderChecker implements StorageChecker
{
    private $_folder;
    private $_storage;

    public function __construct(string $storage)
    {
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
    private function createUserDirectory(string $folder){
        return mkdir($folder);
    }

    /**
     * Проверяет хранилище storage и в случае отсутсвия пользовательской директории создает ее.
     * Если папка уже создана, то вернет true. Иначе попробует создать папку и вернет результат операции true/false
     * ------------------------------------------------------------------------------------------------------------
     * @return bool
     */
    public function checkFolder(){
        if (!file_exists($this->_folder)){
            return $this->createUserDirectory($this->_folder);
        }
        return true;
    }

    public function scanStorage():array{
        $files = (new XmlReportsHandler($this->_storage))->loadCorrectXml();
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
}