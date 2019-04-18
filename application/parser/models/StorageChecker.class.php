<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 10:11
 */

namespace application\parser\models;

/**
 * Класс занимается проверкой хранилища (storage)
 * ----------------------------------------------
 * Class StorageChecker
 * @package application\parser\models
 */
class StorageChecker
{
    private $_storage;
    private $_folder;

    public function __construct()
    {
        $this->_storage = ROOT.'/application/parser/storage'; //Путь к хранилищу файлов
        $this->_folder = $this->_storage.'/'.$_SESSION['user']['id'].'-'.$_SESSION['user']['login']; //Пользовательская папка
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
     * Возвращает список файлов в пользовательской директории либо пустой массив
     * -------------------------------------------------------------------------
     * @return array
     */
    public function scanFolder(){
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

}