<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 11:25
 */
use application\parser\controllers\ControllerPagination;

?>
<div class="alert alert-primary" role="alert">
    Шаг №1. Проверка директории и файлов
</div>
<div class="card">
    <div class="alert alert-warning" role="alert">
        В директории найдены файлы: <b><?=$content['files_count'];?> шт.</b>
    </div>
    <div class="card-header" id="headingOne">
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
                <div class="parser-nav-bar-container">
                    <a href="" class="btn btn-primary btn-sm">
                        <i class="fa fa-chevron-circle-right" aria-hidden="true"></i> Обработать</a>
                </div>
            </div>
            <div id="pagination-content"></div>
            <?php if ($content['files_count'] > 5) :?>
            <div id="pagination">
               <?php (new ControllerPagination(1))->actionBuild($content['storage_checker']);?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>




