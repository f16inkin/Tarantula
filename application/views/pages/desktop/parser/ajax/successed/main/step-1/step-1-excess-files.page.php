<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 11:25
 */
use application\parser\controllers\ControllerPagination;
?>
<div class="card">
    <div class="alert alert-danger" role="alert">
        Количество файлов в директории превышает допустимое. Найдено <b><?=$content['files_count'];?> шт.</b> из <b><?=$content['files_limit'];?> шт.</b> допустимых.
        Для продолжения работы парсера, необходимо удалить <b><u>лишние файлы.</u></b>
    </div>
    <div class="card-header" id="headingOne" style="margin-top: -1rem;">
        <h5 class="mb-0">
            <button class="btn btn-secondary btn-sm" data-toggle="collapse" data-target="#Step_1">
                Показать файлы  <i class="fa fa-chevron-circle-down" aria-hidden="true"></i>
            </button>
        </h5>
    </div>
    <div id="Step_1" class="collapse" aria-labelledby="headingOne" data-parent="#parser-content">
        <div class="card-body">
            <div class="parser-nav-bar">
                <div class="parser-nav-bar-container">
                    <a href="" class="btn btn-danger btn-sm">
                        <i class="fa fa-broom" aria-hidden="true"></i> Очистить</a>
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





