<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 10.06.2018
 * Time: 16:25
 */

namespace core\libs;


use extensions\mobiledetect\Mobile_Detect;

class Configurator
{
    private static $sys_config_path = ROOT.'/core/configs/system_configuration.php';
    private static $db_config_path = ROOT.'/core/configs/database_configuration.php';
    private static $routes_path = ROOT.'/core/configs/routes.php';

    /**
     * Вренет массив с системными конфигурациями
     *
     * @return array
     */
    public static function getSysConfiguration(){
        return include self::$sys_config_path;
    }

    /**
     * Вернет массив с конфигурацией подключения к БД
     *
     * @return array
     */
    public static function getDbConfiguration(){
        return include self::$db_config_path;
    }

    /**
     * Вернет массив маршрутов приложения
     *
     * @return array
     */
    public static function getRoutes(){
        return include self::$routes_path;
    }

    /**
     * Вернет информацию о типе устройства, которое подключается к системе
     *
     * @return string
     */
    public static function getDevice(){
        $detect = new Mobile_Detect();
        if ($detect->isMobile()) {
            $device = 'mobile';
        }elseif($detect->isTablet()){
            $device = 'tablet';
        }else{
            $device = 'desktop';
        }
        return $device;
    }

}