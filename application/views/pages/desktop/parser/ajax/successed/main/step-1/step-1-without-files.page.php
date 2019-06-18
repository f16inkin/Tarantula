<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 12:35
 */
?>
<div id="parser-timeline">
    <div style="text-align: center;">Этапы исполнения: Шаг №1. Проверка пользовательской директории.</div>
    <div class="bulletWrap">
        <!--<div class="before"></div>-->
        <div class="after"></div>
        <div class="bulletTrack table">
            <div  class="slide tableCell">
                <a id="bullet1" class="active" href=""><i class="fa fa-folder"></i></a>
            </div>
            <div class="slide tableCell">
                <a class="" href=""><i class="fa fa-file-code"></i></a>
            </div>
            <div class="slide tableCell">
                <a class="" href=""><i class="fa fa-database"></i></a>
            </div>
            <div class="slide tableCell">
                <a class="" href=""><i class="fa fa-user"></i></a>
            </div>
            <div class="slide tableCell">
                <a class="" href=""><i class="fa fa-check-circle"></i></a>
            </div>
        </div>
    </div>
</div>
<form id="upload-reports-form" method="POST" enctype="multipart/form-data">
    <input id="upload-reports" type="file" name="reports[]" multiple="multiple" onchange="showname(); return false;"/>
    <div class="parser-nav-bar">
        <div class="parser-nav-bar-container">
            <button class="btn btn-success btn-sm" onclick="reportsUpload(); return false;">
                <i class="fa fa-upload" aria-hidden="true"></i> Загрузить</button>
        </div>
    </div>
</form>
<div id="files_container">
    <div class='single_file'>
        <i class="fa fa-folder"></i>Имя
    </div>
</div>
<div id="parser-workplace">
    <div style="text-align: center;">Шаг №1. Загрузка файлов.</div>
    <div class="card" style="padding: 5px;">
        <div class="alert alert-danger" role="alert">В директории отсутствуют файлы</div>
    </div>
</div>


<script>
    function initProgress(){
        var activeDist = $(".slide a.active").position();
        var activeDist1 = ($("#bullet1").position()).left;
        activeDist = activeDist.left-activeDist1;
        $(".after").stop().animate({width: activeDist + "px"});
    }
    initProgress();
    $(".slide a").click(function(e){
        e.preventDefault();
        var slide = $(".slide a");
        slide.removeClass("active").siblings().addClass("inactive");
        $(this).removeClass("inactive").addClass("active");
        initProgress();
    });
</script>
