<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 09.05.2018
 * Time: 13:12
 */

namespace core\base;


class View
{
    protected $_layout;     //Layout
    protected $_title;      //Заголовок страниц
    protected $_pages;      //Динамически формируемы страницы

    /**
     * Устанавливает layout системы
     *
     * @param $layout
     */
    public function setLayout($folder, $layout){
        $this->_layout = ROOT.'/'.$folder.'/views/layouts/'.$layout.'/index.php';
    }

    /**
     * Загрузка layout'а системы, контента и динамической страницы
     *
     * @param $page
     * @param null $content
     */
    public  function  render($page, $content = NULL){
        include $this->_layout;
    }

    /**
     * Установка заголовка страницы
     *
     * @param $title
     */
    public function setTitle($title) {
        $this->_title = $title;
    }

    /**
     * Устанавливает страницы для layout'а
     *
     * @param $pages
     */
    public function setPages($pages){
        $this->_pages = $pages;
    }

    /**
     * Возвращает путь к указанной странице
     *
     * @param $page_path
     * @return string
     */
    public function returnPagePath($folder, $page_path){
        return ROOT.'/'.$folder.'/views/pages/'.$page_path.'.php';
    }
}