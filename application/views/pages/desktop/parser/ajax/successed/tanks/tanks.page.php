<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 13.03.2019
 * Time: 10:32
 */
?>
<div class="parser-content-header">
    Добавление данных
</div>
<div class="parser-nav-bar">
    <div class="parser-nav-bar-container">
        <select id="subdivisions" style="width: 250px;">
            <option value="0">Выберите подраздедление...</option>
            <?php foreach ($content['subdivisions'] as $singleSubdivision) :?>
                <option value="<?=$singleSubdivision['id'];?>"><?=$singleSubdivision['name'];?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="parser-nav-bar-container">
        <a href="" id="handbook-button" class="btn btn-primary btn-sm" onclick="">
            <i class="fa fa-plus-circle" aria-hidden="true"></i> Добавить в БД</a>
    </div>
</div>
<!---->
<?php foreach ($content['files'] as $singleSession) :?>
    <div class="card">
        <div class="card-header" id="headingOne">
            <h5 class="mb-0">
                <input type="text" value="<?=$singleSession['data']['SessionInformation']['Number'];?>" hidden>
                <button class="btn btn-primary" style="width: 500px;" data-toggle="collapse" data-target="#Session_<?=$singleSession['data']['SessionInformation']['Number'];?>">
                    <div>
                        <?='№ Смены: '.$singleSession['data']['SessionInformation']['Number'];?>
                        <?='Оператор: '.$singleSession['data']['SessionInformation']['Operator'];?>
                    </div>
                </button>
                <button  class="btn btn-danger">Удалить</button>
            </h5>
        </div>
        <div id="Session_<?=$singleSession['data']['SessionInformation']['Number']; ?>" class="collapse" aria-labelledby="headingOne" data-parent="#parser-content">
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
                    <?php foreach ($singleSession['data']['SessionData'] as $tankFuel) :?>
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
    </div>
<?php endforeach; ?>
<!---->
