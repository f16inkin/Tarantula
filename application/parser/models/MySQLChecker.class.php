<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 27.04.2019
 * Time: 19:35
 */

namespace application\parser\models;


use application\parser\interfaces\StorageChecker;

class MySQLChecker implements StorageChecker
{

    /**
     * Метод проверяет хранилище (папку/базу данных) и возвращает список файлов находящихся в нем в виде массива:
     * array = [0 => file_1, 1 => file_2, 2 => file_3]
     * @return array
     */
    public function scanStorage(): array
    {
        // TODO: Implement scanStorage() method.
    }
}