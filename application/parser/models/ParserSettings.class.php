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
    private $_files_limit; //переменная отвечающая за максимально допустимое количество файлов в папке пользователя
    private $_storage; //Папка хранилизе для всех файлов парсера

    public function __construct()
    {
        parent::__construct();
        /**
         * Пока некоторые натсройки будут добавлятся в ручную, но потом будут браться из базы данных
         */
        $this->_files_limit = 20;
        $this->_storage = ROOT.'/application/parser/storage';
    }

    /**
     * @return mixed
     */
    public function getFilesLimit()
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

}