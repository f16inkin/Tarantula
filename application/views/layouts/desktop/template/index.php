<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 19.06.2018
 * Time: 20:07
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <!--СТИЛИ НАЧАЛО-->
        <link rel="stylesheet" type="text/css" href="/application/views/layouts/desktop/template/css/style.css"/>
        <link rel="stylesheet" type="text/css" href="/core/views/includes/bootstrap/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="/core/views/includes/fontawesome/css/all.css"/>
        <!--СТИЛИ КОНЕЦ-->
        <!--JQUERY-->
        <script src="/core/views/includes/jquery/jquery-3.3.1.min.js"></script>
        <!--JQUERY-->
        <title id="title"><?=$this->_title?></title>
    </head>
    <body>
    <div id="wrapper">
        <section id="header">
            <div id="logo-container">
                <div id="logo-icon-container">
                    <i id="logo-icon" class="fa fa-life-ring fa-spin"></i>
                </div>
                <div id="logo-caption-container">
                    <i id="logo-caption">WIZARD'S TEAR</i>
                </div>
            </div>
            <?php //\core\controllers\ControllerUserbar::Show(); ?>
            <?php //\controllers\ControllerNotice::Show(); ?>
        </section>
        <section id="sidebar"><?php \core\controllers\ControllerMenu::Show('application', 1); ?></section>
        <script>
            function onloadHere() {
                if (localStorage.getItem("openMenu") == "true"){
                    $("#logo-container").width(250);
                    $( "#sidebar" ).width(250);
                    $(".main-menu").width(250);
                }
            }
            onloadHere();
        </script>
        <section id="content">
            <div id="cpcontent">
                <?php include $this->_pages.$page.'.php'; ?>
            </div>
        </section>
        <section id="footer">
            <div id="copyright"><p>Создатель - Коваленко М.Ф.</p></div>
        </section>
    </div>
    <!--СКРИПТЫ НАЧАЛО-->
    <script src="/core/views/includes/bootstrap/js/bootstrap.min.js"></script>
    <script src="/core/views/includes/jquery-validate/jquery.validate.min.js"></script>
    <script src="/core/views/includes/jquery-autosize/autosize.js"></script>
    <script>autosize(document.querySelectorAll('textarea'))</script>
    <script>
        $( "#logo-icon-container" ).click(function() {
            if  ($("#sidebar").width() > 190){
                $("#logo-container").width(55);
                $("#sidebar").width(55);
                $(".main-menu").width(55);
                localStorage.setItem("openMenu", "false");
            }else{
                $("#logo-container").width(250);
                $( "#sidebar" ).width(250);
                $(".main-menu").width(250);
                localStorage.setItem("openMenu", "true");
            }
        });
    </script>
    <!--СКРИПТЫ КОНЕЦ-->
    </body>
</html>
