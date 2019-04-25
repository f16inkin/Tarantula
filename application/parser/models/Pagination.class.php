<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 25.04.2019
 * Time: 19:56
 */

namespace application\parser\models;


class Pagination
{
    private $_storage_checker;

    public function __construct($storage_checker)
    {
        $this->_storage_checker = $storage_checker;
    }

    public function build(){
        $files_count = count($this->_storage_checker->scanFolder());
        $pages_count = ceil($files_count/5);
        return $pages_count;
    }

    public function getPage(int $page){
        $files = $this->_storage_checker->scanFolder();
        $files_count = count($files);

    }

}