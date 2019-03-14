<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 19.08.2018
 * Time: 16:56
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <!--СТИЛИ НАЧАЛО-->
    <link rel="stylesheet" type="text/css" href="/administrator/views/layouts/desktop/template/css/style.css"/>
    <link rel="stylesheet" type="text/css" href="/core/views/includes/bootstrap/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="/core/views/includes/fontawesome/css/all.css"/>
    <!--СТИЛИ КОНЕЦ-->
    <!--JQUERY-->
    <script src="/core/views/includes/jquery/jquery-3.3.1.min.js"></script>
    <!--JQUERY-->
    <title><?=$this->_title?></title>
</head>
<body>
<div id="wrapper">
    <section id="header"></section>
    <section id="sidebar"><?php \core\controllers\ControllerMenu::Show('administrator', 2); ?></section>
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
</body>
</html>
