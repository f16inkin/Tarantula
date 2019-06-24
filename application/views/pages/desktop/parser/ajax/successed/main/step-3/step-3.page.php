<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 21.06.2019
 * Time: 9:33
 */
?>
<div id="parser-timeline">
    <div style="text-align: center;">Просмотр загруженных файлов. Подтверждение на обработку</div>
    <div class="bulletWrap">
        <!--<div class="before"></div>-->
        <div class="after"></div>
        <div class="bulletTrack table">
            <div  class="slide tableCell">
                <span id="stage_1"><i class="fa fa-folder"></i></span>
            </div>
            <div class="slide tableCell">
                <span id="stage_2"><i class="fa fa-file-code"></i></span>
            </div>
            <div class="slide tableCell">
                <span id="stage_3"><i class="fa fa-user"></i></span>
            </div>
            <div class="slide tableCell">
                <span id="stage_4"><i class="fa fa-database"></i></span>
            </div>
            <div class="slide tableCell">
                <span id="stage_5"><i class="fa fa-check-circle"></i></span>
            </div>
        </div>
    </div>
</div>
<!---->
<script>
    /**
     * Текущие функции будут работать только при загрузке этой страницы.
     * -----------------------------------------------------------------
     */
    //Установка титула старницы
    title.text('Валидация пользователем');
    /**
     * Работа с линией прогресса.
     * --------------------------
     */
    //Переключаю состояния этапов
    toggleStage('stage_3');
    //Инициализирую линию прогресса
    initProgressLine();
</script>
