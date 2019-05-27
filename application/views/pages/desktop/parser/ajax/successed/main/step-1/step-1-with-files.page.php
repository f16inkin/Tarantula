<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 11:25
 */
use application\parser\controllers\ControllerPagination;
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
<div id="parser-workplace">
    <div style="text-align: center;">Шаг №1. проверка файлов.</div>
    <div class="card">
        <div class="alert alert-warning" style="padding: 5px;" role="alert">
            В директории найдены файлы: <b></b>
        </div>
        <div class="card-header" id="headingOne" style="margin-top: -1rem;">
            <h5 class="mb-0">
                <button class="btn btn-secondary btn-sm" data-toggle="collapse" data-target="#files_collapse_container">
                    Показать файлы  <i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="deleteFilesFomDirectory(); return false;">
                    <i class="fa fa-trash" aria-hidden="true"></i> Удалить</a>
                </button>
                <button class="btn btn-primary btn-sm">
                    <i class="fa fa-chevron-circle-right" aria-hidden="true"></i> Обработать</a>
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
                <?php if ($content['allow_pagination']) :?>
                <div id="pagination">
                   <?php (new ControllerPagination($content['storage_checker_id']))->actionBuild();?>
                </div>
                <?php endif; ?>
            </div>
        </div>
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

