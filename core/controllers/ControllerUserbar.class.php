<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 12.11.2018
 * Time: 21:10
 */

namespace core\controllers;


use core\models\UserBar;
use core\models\UserProfile;

class ControllerUserbar
{
    private static function InitComponents(){
        $components['profile'] = new UserProfile();
        $components['profile1'] = new UserProfile();
        $userBar = new UserBar($components);
        return $userBar;

    }

    public static function Show(){
        $userBar = self::InitComponents();
        include (ROOT.'/application/views/pages/desktop/userbar/userbar.page.php');
    }

}