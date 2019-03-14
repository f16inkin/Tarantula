<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 30.06.2018
 * Time: 12:56
 */

namespace core\controllers;


use core\libs\Configurator;
use core\models\Menu;

class ControllerMenu
{
    /**
     * Отображает меню системы
     *
     * @param int $menu
     */
    public static function Show(string $section, int $menu){
        //Определяю тип устройства
        $device = Configurator::getDevice();
        //Подгружаю ссылки
        $content['menu'] = (new Menu())->getAvailableLinks($menu);
        if ($content['menu']){}
        //Загрузка страницы
        include (ROOT.'/'.$section.'/views/pages/'.$device.'/menu/menu.page.php');
    }

}