<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 30.06.2018
 * Time: 12:55
 */

namespace core\models;


use core\base\Model;
use core\libs\DatabaseException;

class Menu extends Model
{

    /**
     * Menu constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Выполнив метод на выходе он вернет массив с идентифкаторами ссылок, доступных текущему пользователю.
     * Массив имеет вид: [0 => 1, 1 => 2]
     *
     * @param int $menu
     * @return array
     */
    private function getAvailableLinksIds(int $menu){
        //Для начала получаю идентификаторы всех ролей, которые есть у пользователя
        foreach ($_SESSION['user']['roles'] as $role){
            $role_ids[] = $role['id'];
        }
        try{
            //Потом нахожу идентифкаторы(id) ссылок, которые доступны для ролей пользователя в конкретном меню
            $query = ("SELECT `link_id` FROM `role_menu` 
                      INNER JOIN `menu_links` ON `role_menu`.`link_id` = `menu_links`.`id`
                       WHERE `role_id` = :role_id AND `menu` = :menu");
            $links_id = [];
            foreach ($role_ids as $role_id){
                $result = $this->_db->prepare($query);
                $result->execute([
                    'role_id' => $role_id,
                    'menu' => $menu
                ]);
                $links = $result->fetchAll();
                //Обьявлю, чтоб вернул хотя бы пустой массив в случае пустых таблиц
                foreach ($links as $link){
                    $links_id[] = $link['link_id'];
                }
            }
            return $links_id;
        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

    /**
     * Метод вернет массив с данными о всех ссылках для выбранного меню.
     * Массив имеет вид:
     * 1 => array[id => 1, link => "/start/", title => "Старт", menu => 1, style => fa fa-cube fa-lg]
     * 2 => array[id => 2, link => "/monitoring/", title => "Мониторинг", menu => 1, style => fa fa-clipboard-list fa-lg]
     *
     * @param int $menu
     * @return mixed || array || null
     */
    private function getMenuLinks(int $menu){
        try{
            //Выбираю все ссылки которые доступны в искомом меню
            $query = ("SELECT * FROM `menu_links` WHERE `menu` = :menu");
            $result = $this->_db->prepare($query);
            $result->execute([
                'menu' => $menu,
            ]);
            if ($result->rowCount() > 0) {
                //Наполняю массив присваивая ключу массива идентифкатор(id) ссылки
                while ($row = $result->fetch()){
                    $menuLinks[$row['id']] = $row;
                }
                return $menuLinks;
            }
            return null;
        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

    /**
     * Метод возвращает только, те ссылки, которые доступны пользователю в соответствии с его ролью / ролями.
     * В итоге в случае успешного выполнения метода, я получу отсортиртиртированный ассоциативный массив, с уникальными
     * ссылками.
     *
     * @param int $menu
     * @return array
     */
    public function getAvailableLinks(int $menu){
        try{
            $availableLinksIds = $this->getAvailableLinksIds($menu);
            //Если для ролей, есть доступные ссылки меню, то оставляю только уникальные, ибо у пользователя может быть
            //несколько ролей, а для разных ролей ссылки могут повторяться.
            if (!empty($availableLinksIds)){
                $unique_ids = array_unique($availableLinksIds);
                //Сортирую по возрастанию массив, для корректного отображения ссылок в конце
                sort($unique_ids);
            }else{
                return null;
            }
            //Теперь когда у меня есть массив где ключи аналогичны идентификаторам ссылок,
            //я формирую новый массив с доступными ссылками id которых равен id уникальных доступных ссылок на основе
            //ролей
            $menuLinks = $this->getMenuLinks($menu);
            //Если таблица меню не пустая
            if (isset($menuLinks)){
                $availableLinks = [];
                foreach ($unique_ids as $key => $value){
                    $availableLinks[] = $menuLinks[$value];
                }
                return $availableLinks;
            }
            return null;
        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

}