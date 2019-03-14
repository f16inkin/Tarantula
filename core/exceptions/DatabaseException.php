<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 30.06.2018
 * Time: 12:59
 */

namespace core\exceptions;

use Exception;

class DatabaseException extends Exception
{
    function __construct()
    {
        echo '<br>';
        echo 'Database Exception has been catched';
        echo '<br>';
    }

}