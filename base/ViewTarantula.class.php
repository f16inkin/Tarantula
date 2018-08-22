<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 21.08.2018
 * Time: 15:35
 */

namespace base;


class ViewTarantula
{
    protected $_layout;     //Layout
    protected $_title;      //Заголовок страниц
    protected $_pages;      //Динамически формируемы страницы

    /**
     * Устанавливает layout системы
     *
     * @param $layout
     */
    public function setLayout($layout){
        $this->_layout = ROOT.'/views/layouts/'.$layout.'/index.php';
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
    public function returnPagePath($page_path){
        return ROOT.'/views/pages/'.$page_path.'.php';
    }

}