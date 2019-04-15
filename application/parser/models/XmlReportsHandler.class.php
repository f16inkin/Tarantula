<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 30.03.2019
 * Time: 10:59
 */

namespace application\parser\models;


use application\parser\base\AbstractFileHandler;

class XmlReportsHandler extends AbstractFileHandler
{
    /**
     * Параметр - строка путь с хранилищем XML отчетов
     * -----------------------------------------------
     * XmlReportsHandler constructor.
     * @param string $storage
     */
    public function __construct(string $storage)
    {
        parent::__construct($storage);
    }

    /**
     * Метод сканирует указанную директорию(хранилище) и определяет какие файлы пригодны для работы с парсером
     * в дальнейшем. Если это XML он отнесет их к корректным, иначе это будут некорректные файлы.
     * -------------------------------------------------------------------------------------------------------
     * @return array|null
     */
    public function scanStorage(){
        //Сканирую директорию на наличие XML отчетов
        $files = array_slice(scandir($this->_storage),2);
        //Если директория пуста, верну null
        if (empty($files)){
            return null;
        }
        $dividedFiles= [];
        //Отключаю ошибки libxml и беру полномочия на обработку ошибок на себя.
        libxml_use_internal_errors(true);
        /**
         * Получаю имена всех файлов находящихся в директории storage. Затем проверяю являются ли эти файлы
         * в формате XML. Если файлы попадают под такое определение то они сортируются в секцию correct files, иначе
         * определяются как incorrect
         *
         */
        for ($i = 0; $i < count($files); $i++){
            $fileName = $files[$i];
            $isCorrect = simplexml_load_file($this->_storage.'/'.$files[$i]) ? true : false;
            $dividedFiles[$i]['fileName'] = $fileName;
            $dividedFiles[$i]['isCorrect'] = $isCorrect ? 1 : 0;

        }
        //Возвращаю обработку ошибок в стандартное положение.
        libxml_use_internal_errors(false);
        return $dividedFiles;
    }

}