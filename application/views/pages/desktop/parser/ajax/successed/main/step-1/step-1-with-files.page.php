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
        <div class="before1"></div>
        <div class="after"></div>
        <div class="bulletTrack table">
            <div class="slide tableCell">
                <a class="active" href=""><i class="fa fa-folder"></i></a>
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
        <div class="alert alert-warning" role="alert">
            В директории найдены файлы: <b><?=$content['files_count'].'/'. $content['files_limit'];?> шт.</b>
        </div>
        <div class="card-header" id="headingOne" style="margin-top: -1rem;">
            <h5 class="mb-0">
                <button class="btn btn-secondary btn-sm" data-toggle="collapse" data-target="#files_collapse_container">
                    Показать файлы  <i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
                </button>
            </h5>
        </div>
        <div id="files_collapse_container" class="collapse" aria-labelledby="headingOne" data-parent="#parser-content">
            <div class="card-body">
                <div class="parser-nav-bar">
                    <div class="parser-nav-bar-container">
                        <a href="" class="btn btn-danger btn-sm">
                            <i class="fa fa-broom" aria-hidden="true"></i> Очистить</a>
                    </div>
                    <div class="parser-nav-bar-container">
                        <a href="" class="btn btn-primary btn-sm">
                            <i class="fa fa-chevron-circle-right" aria-hidden="true"></i> Обработать</a>
                    </div>
                </div>
                <div id="pagination-content"></div>
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
        activeDist = activeDist.left;
        $(".after").stop().animate({width: activeDist + "px"});
    }
    initProgress();
    $("a").click(function(e){
        e.preventDefault();
        var slide = $(".slide a");
        //$(this).toggleClass('active').siblings().removeClass('active');
        slide.removeClass("active").siblings().addClass("inactive");
        $(this).removeClass("inactive").addClass("active");
        initProgress();
    });
</script>

