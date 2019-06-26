<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 24.04.2019
 * Time: 21:03
 */

namespace application\parser\models;


use core\base\Model;

class ParserSettings extends Model
{
    private $_files_limit; //переменная отвечающая за максимально допустимое количество файлов для обработки за 1 раз.
    private $_storage; //Папка хранилище для всех файлов парсера
    private $_files_per_page; //Количество файлов выводимых на странице в навигаторе
    private $_upload_file_max_size;    //Максимальный размер загружаемых файлов

    public function __construct()
    {
        parent::__construct();
        /**
         * Пока некоторые натсройки будут добавлятся в ручную, но потом будут браться из базы данных
         */
        $this->_files_limit = 40;
        $this->_storage = ROOT.'/application/parser/storage';
        $this->_files_per_page = 10;
        $this->_upload_file_max_size = 128000;

    }

    /**
     * @return mixed
     */
    public function getFilesLimit(): int
    {
        return $this->_files_limit;
    }

    /**
     * @return string
     */
    public function getStorage(): string
    {
        return $this->_storage;
    }

    /**
     * @return mixed
     */
    public function getFilesPerPage(): int
    {
        return $this->_files_per_page;
    }

    /**
     * @return int
     */
    public function getUploadFileMaxSize(): int
    {
        return $this->_upload_file_max_size;
    }

}