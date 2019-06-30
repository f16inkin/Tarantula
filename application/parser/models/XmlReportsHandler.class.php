<?php


namespace application\parser\models;


use core\base\Model;

class XmlReportsHandler extends Model
{
    private $_folder;

    public function __construct(string $storage)
    {
        parent::__construct();
        $this->_folder = $storage.'/'.$_SESSION['user']['id'].'-'.$_SESSION['user']['login']; //пользовательская папка
        //Если отсутсвует папка хранилище создаст ее. Пока пусть будет, но в планах удалить эту проверку
        //При автоматической установке модуля создавать нужную директорию
        if (!file_exists($storage)){
            mkdir($storage);
        }
    }

    public function scanStorage(){
        //Сканирую директорию на наличие файлов. Файлы любого расширения.
        $files = array_slice(scandir($this->_folder),2);
        return $files;

    }

    public function loadXmlFile(string $file_name){
        libxml_use_internal_errors(true);
        $simpleXmlElement = simplexml_load_file($this->_folder.'/'.$file_name);
        libxml_use_internal_errors(false);
        return $simpleXmlElement;
    }

    public function loadXmlFiles(array $files){
        libxml_use_internal_errors(true);
        $i = 0;
        foreach ($files as $file) {
            $i++;
            $simpleXmlElements[$i]['file_name'] = $file;
            $simpleXmlElements[$i]['simpleXmlElement'] = simplexml_load_file($this->_folder.'/'.$file) ? simplexml_load_file($this->_folder.'/'.$file) : null;
        }
        libxml_use_internal_errors(false);
        return $simpleXmlElements;
    }

    public function loadXmlPage(int $current_page, int $files_per_page){
        $files = array_chunk($this->scanStorage(), $files_per_page);
        $keys = range(1, count($files));
        $pages = array_combine($keys, $files);
        $simpleXmlElements = [];
        if (isset($pages)){
            $page = @$pages[$current_page];  //страница наполненная файлами
            if (isset($page)){
                libxml_use_internal_errors(true);
                for ($i = 0; $i < count($page); $i++){
                    $simpleXmlElements[$i]['file_name'] = $page[$i];
                    $simpleXmlElements[$i]['simpleXmlElement'] = simplexml_load_file($this->_folder.'/'.$page[$i]) ? simplexml_load_file($this->_folder.'/'.$page[$i]) : null;
                }
                libxml_use_internal_errors(false);
            }
        }
        return $simpleXmlElements;
    }

}