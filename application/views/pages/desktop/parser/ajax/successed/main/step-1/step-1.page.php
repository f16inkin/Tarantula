<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 12:35
 */
?>
<div id="parser-timeline">
    <div style="text-align: center;">Загрузка файлов</div>
    <div class="bulletWrap">
        <!--<div class="before"></div>-->
        <div class="after"></div>
        <div class="bulletTrack table">
            <div  class="slide tableCell">
                <span id="stage_1" class="active"><i class="fa fa-folder"></i></span>
            </div>
            <div class="slide tableCell">
                <span id="stage_2"><i class="fa fa-file-code"></i></span>
            </div>
            <div class="slide tableCell">
                <span id="stage_3"><i class="fa fa-database"></i></span>
            </div>
            <div class="slide tableCell">
                <span id="stage_4"><i class="fa fa-user"></i></span>
            </div>
            <div class="slide tableCell">
                <span id="stage_5"><i class="fa fa-check-circle"></i></span>
            </div>
        </div>
    </div>
</div>
<form id="upload-reports-form" method="POST" enctype="multipart/form-data">
    <div class="parser-nav-bar">
        <div class="parser-nav-bar-container">
            <label for="upload-reports" class=" btn btn-warning btn-sm">
                <span class="fa fa-folder-open"></span> Выбрать файлы</label>
            <input id="upload-reports" type="file" name="reports[]" multiple="multiple" onchange="fillFileContainer(); return false;"/>
        </div>
        <div class="parser-nav-bar-container">
            <button class="btn btn-success btn-sm" onclick="reportsUpload(); return false;">
                <i class="fa fa-upload" aria-hidden="true"></i> Загрузить</button>
        </div>
        <?php if(isset($content['files'])):?>
        <div class="parser-nav-bar-container">
            <button class='btn btn-primary btn-sm' onclick='secondStep(); return false;'>
                <i class='fa fa-chevron-circle-right' aria-hidden='true'></i> Обработать</button>
        </div>
        <?php endif; ?>
    </div>
</form>
<div id="files_container">
    <!--<div class='single_file' title="CloseSession_2019-05-03_09-00-59.xml">
        <i class="fa fa-folder" ></i>CloseSession_2019-05-03_09-00-59.xml
    </div>
    <div class='single_file'>
        <i class="fa fa-folder"></i>c.txt
    </div>-->
</div>
<div id="parser-workplace">
    <div style="text-align: center;">Шаг №1. Загрузка файлов.</div>
    <div class="card" style="padding: 5px;">
        <?php if(isset($content['files'])):?>
            <div class="alert alert-primary" role="alert">
                В директории есть файлы. Файл(ов): <b><?=$content['files']['files_count'];?></b>
            </div>
            <div class="alert alert-warning" role="alert">
                Лимит разовой загрузки состоявляет <b><?=$content['upload_limit'];?> файл(ов).</b>
                Максимальный размер загружаемого файла равен: <b><?=$content['max_file_size']/1000;?> Кб.</b>
            </div>
        <?php else: ?>
            <div class="alert alert-danger" role="alert">
                В директории отсутствуют файлы. Лимит разовой загрузки состоявляет <b><?=$content['upload_limit'];?> файл(ов)</b>
                Максимальный размер загружаемого файла равен: <b><?=$content['max_file_size']/1000;?> Кб.</b>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    /**
     * Текущие функции будут работать только при загрузке этой страницы.
     * -----------------------------------------------------------------
     */
    //Установка титула старницы
    title.text('Загрузка файлов');
    //Переключаю состояния этапов
    toggleStage('stage_1');
    //Инициализирую линию прогресса
    initProgressLine();
</script>
