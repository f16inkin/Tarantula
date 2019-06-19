<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 11:25
 */
?>
<div id="parser-timeline">
    <div style="text-align: center;">Просмотр загруженных файлов. Подтверждение на обработку</div>
    <div class="bulletWrap">
       <!--<div class="before"></div>-->
        <div class="after"></div>
        <div class="bulletTrack table">
            <div  class="slide tableCell">
                <span id="stage_1" class="active" href=""><i class="fa fa-folder"></i></span>
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
<div id="parser-workplace">
    <div style="text-align: center;">
        Внимательно посмотрите на загруженные файлы. Если какие либо на ваш взгляд являются лишними. Их можно удалить
    </div>
    <div class="card">
        <div class="alert alert-primary" style="padding: 5px;" role="alert">
            В директории найдены файлы: <b></b>
        </div>
        <div class="card-header" id="headingOne" style="margin-top: -1rem;">
            <h5 class="mb-0">
                <button class="btn btn-secondary btn-sm" data-toggle="collapse" data-target="#files_collapse_container">
                    Показать файлы  <i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="deleteFiles(); return false;">
                    <i class="fa fa-trash" aria-hidden="true"></i> Удалить
                </button>
                <button class="btn btn-primary btn-sm">
                    <i class="fa fa-chevron-circle-right" aria-hidden="true"></i> Обработать все
                </button>
            </h5>
        </div>
        <div id="files_collapse_container" class="collapse" aria-labelledby="headingOne" data-parent="#parser-content">
            <div class="card-body" style="padding: 0.25rem;">
                <!----->
                <div id="pagination-content">
                    <table cellpadding="1" cellspacing="1" border="0" style="margin-bottom: 5px;" class="table-striped table-mine full-width box-shadow--2dp">
                        <thead>
                        <tr class="tr-table-header">
                            <th>
                                <input id="check_start" class="hidden-checkbox" type="checkbox"/>
                                <label for="check_start">
                                    <div><i class="fa fa-check"></i></div>
                                </label>
                            </th>
                            <th>Имя файла</th>
                        </tr>
                        </thead>
                        <tbody id="table-pagination-content"></tbody>
                    </table>
                </div>
                <!---->
                <div id="pagination">
                    <ul id="pagination-list" class="pagination"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /**
     * Текущие функции будут работать только при загрузке этой страницы.
     * -----------------------------------------------------------------
     */
     //Загружаю строки файлов в таблицу
     loadPage(1, FOLDER_CHECKER_ID);
     //Активирую первую кнопку навигатора
     buildPagination(FOLDER_CHECKER_ID, 1);
     //Установка титула старницы
     title.text('Проверка хранилища');
    /**
     * Работа с линией прогресса.
     * --------------------------
     */
    //Переключаю состояния этапов
    toggleStage('stage_2');
    //Инициализирую линию прогресса
    initProgressLine();
</script>

