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

    public function __construct(StorageChecker $storage_checker)
    {
        $this->_storage_checker = $storage_checker;
    }

    public function build($per_page){
        $files_count = count($this->_storage_checker->scanFolder());
        $pages_count = ceil($files_count/$per_page);
        return $pages_count;
    }

    public function getPage(int $page, int $per_page){
        $files = $this->_storage_checker->scanFolder();
        $files_count = count($files);
        $pages_count = ceil($files_count/$per_page);
        $start=abs($page*$per_page);

        //$arr=array_slice($files,$start_element,$finish_element);
    }

}