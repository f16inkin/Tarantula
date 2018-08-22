<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 21.08.2018
 * Time: 16:37
 */?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
    <!--CSS-ки-->
    <link rel="stylesheet" type="text/css" href="/views/layouts/template/css/style.css"/>
    <script src="/views/includes/jquery/jquery-3.3.1.min.js"></script>
    <title><?=$this->_title?></title>
</head>
<body>
<div id="wrapper">
    <section id="header">Заглавная часть</section>
    <section id="sidebar">Сайдбар</section>
    <section id="content">
        <div id="cpcontent">
            <?php include $this->_pages.$page.'.php'; ?>
        </div>
    </section>
    <section id="footer">Подвал</section>
</div>
</body>
</html>
