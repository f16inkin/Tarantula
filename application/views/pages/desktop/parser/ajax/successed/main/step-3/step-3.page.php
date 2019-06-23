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
                <span id="stage_1" class="active" href=""><i class="fa fa-folder"></i></span>
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
<div class="parser-nav-bar">
    <div class="parser-nav-bar-container">
        <button class="btn btn-success btn-sm" onclick="">
            <i class="fa fa-database" aria-hidden="true"></i> Загрузить в БД</button>
    </div>
    <div class="parser-nav-bar-container">
        <button class="btn btn-danger btn-sm" onclick="">
            <i class="fa fa-broom" aria-hidden="true"></i> Удалить все</button>
    </div>
</div>
<div id="parser-workplace">
    <div class="card">
        <div class="alert alert-primary" style="padding: 5px;" role="alert">
            Файлы обработаны. Ознакомтесь с содержимым.
        </div>
        <div style="margin-top: -1rem;">
            <?php foreach ($content as $single) :?>
                    <div class="card-header">
                        <h5 class="mb-0">
                            <input type="text" value="<?=$single['session']['Number'];?>" hidden>
                            <button class="btn btn-primary btn-sm" style="width: 500px;" data-toggle="collapse" data-target="#Session_<?=$single['session']['Number'];?>">
                                <div>
                                    <?//='ID: '.$singleSession['RecordId'];?>
                                    <?='№ Смены: '.$single['session']['Number'];?>
                                    <?='Оператор: '.$single['session']['Operator'];?>
                                </div>
                            </button>
                            <button  class="btn btn-danger btn-sm">Удалить</button>
                            <span class="badge badge-success"><i class="fa fa-check-circle"></i> Корректный</span>
                        </h5>
                    </div>
                    <div id="Session_<?=$single['session']['Number']; ?>" class="collapse" data-parent="#parser-content">
                        <div class="card-body">
                            <table cellpadding="1" cellspacing="1" border="0" class="table-mine  full-width box-shadow--2dp">
                                <thead>
                                <tr class="tr-table-header">
                                    <th width="3%">Емкость</th>
                                    <th width="">Топливо</th>
                                    <th width="">Начальный объем</th>
                                    <th width="">Принято</th>
                                    <th width="">Отпуск</th>
                                    <th width="">Расчетный остаток</th>
                                    <th width="">Фактический остаток</th>
                                    <th width="">Излишки</th>
                                    <th width="">Температура</th>
                                    <th width="">Плотность</th>
                                    <th width="">Масса</th>
                                </tr>
                                </thead>
                                <tbody id="table-content">
                                <?php foreach ($single['tanks'] as $tankFuel) :?>
                                    <tr class="tr-table-content">
                                        <td><?=$tankFuel['TankNum'];?></td>
                                        <td><?=$tankFuel['Fuel'];?></td>
                                        <td><?=$tankFuel['StartFuelVolume'];?></td>
                                        <td><?=$tankFuel['Income'];?></td>
                                        <td><?=$tankFuel['Outcome'];?></td>
                                        <td><?=$tankFuel['EndFuelVolume'];?></td>
                                        <td><?=$tankFuel['EndFactVolume'];?></td>
                                        <td><?=$tankFuel['Overage'];?></td>
                                        <td><?=$tankFuel['EndTemperature'];?></td>
                                        <td><?=$tankFuel['EndDensity'];?></td>
                                        <td><?=$tankFuel['EndMass'];?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
            <?php endforeach; ?>
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
    title.text('Проверка хранилища');
    /**
     * Работа с линией прогресса.
     * --------------------------
     */
    //Переключаю состояния этапов
    toggleStage('stage_3');
    //Инициализирую линию прогресса
    initProgressLine();
</script>
