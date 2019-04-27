<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 11:25
 */
?>
<div class="alert alert-primary" role="alert">
    Шаг №1. Проверка директории и файлов
</div>
<div class="card">
    <div class="alert alert-danger" role="alert">
        Количество файлов в директории превышает допустимое. Найдено <b><?=$content['files_count'];?> шт.</b> из <b><?=$content['files_limit'];?> шт.</b> допустимых.
        Для продолжения работы парсера, необходимо удалить <b><u>лишние файлы.</u></b>
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
                        <i class="fa fa-broom" aria-hidden="true"></i> Очистить папку</a>
                </div>
                <div class="parser-nav-bar-container">
                    <a href="" class="btn btn-warning btn-sm" style="color: white">
                        <i class="fa fa-trash" aria-hidden="true"></i> Удалить</a>
                </div>
            </div>
            <div id="pagination-content"> </div>
            <div id="pagination">
                <ul class="pagination">
                    <li class="page-item disabled">
                        <span class="page-link">Предыдущая</span>
                    </li>
                    <?php for ($i = 1; $i< $content['pagination']+1; $i++) :?>
                        <li class="page-item"><a class="page-link" onclick="showPaginationPageData(<?=$i;?>); return false;"><?=$i;?></a></li>
                    <?php endfor ;?>
                    <li class="page-item"><a class="page-link" href="#">Следующая</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>





