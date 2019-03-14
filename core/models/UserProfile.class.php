<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 12.11.2018
 * Time: 21:03
 */

namespace core\models;


use core\base\Model;
use core\libs\Session;

class UserProfile extends Model
{
    private $_name;
    private $_foto;

    public function __construct()
    {
        parent::__construct();
        $this->_name = Session::getShortName();
        $this->_foto = Session::getFoto();
    }

    public function Initiate(){
        $foto = $this->_foto;
        $name = $this->_name;
        include (ROOT.'/application/views/pages/desktop/userprofile/user-profile.page.php');
    }

}