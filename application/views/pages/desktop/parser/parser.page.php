<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 13.03.2019
 * Time: 10:32
 */
?>
<div class="container-fluid">
    <div class="row padding-left15">
        <div class="col module-wrapper">
            <div id="parser-menu">
                <ul class="nav nav-pills nav-justified" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link"  id="parser-main" data-toggle="pill" href="" role="tab">
                            <i class="fa fa-home" aria-hidden="true"></i> Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="parser-reports" data-toggle="pill" href="" role="tab">
                            <i class="fa fa-calendar" aria-hidden="true"></i> Отчеты</a>
                    </li>
                   <li class="nav-item">
                        <a class="nav-link"  id="parser-controls" data-toggle="pill" href="" role="tab">
                            <i class="fa fa-cog" aria-hidden="true"></i> Управление</a>
                    </li>
                </ul>
            </div>
            <div id="parser-content">
                <!--Контент AJAX-->
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="/application/parser/assets/parser.css"/>
<script src="/application/parser/assets/parser.js"></script>