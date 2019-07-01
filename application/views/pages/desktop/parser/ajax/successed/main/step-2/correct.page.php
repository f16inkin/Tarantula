<?php
?>
<!--Смены-->
<div style="text-align: center;"><h5>Смена</h5></div>
<div>
    <p><?=$content['session']['Number'];?></p>
    <p><?=$content['session']['StartDateTime'];?></p>
    <p><?=$content['session']['EndDateTime'];?></p>
    <p><?=$content['session']['Operator'];?></p>
</div>
<!--Смены-->
<!--------------------------------------------------------------------------------------------------------------------->
<!--Емкости-->
<div>
    <div style="text-align: center;"><h5>Емкости</h5></div>
    <table cellpadding="1" cellspacing="1" border="0" class="table-mine  full-width box-shadow--2dp">
        <thead>
            <tr class="tr-table-header">
                <th>Емкость</th>
                <th>Топливо</th>
                <th>Начальный объем</th>
                <th>Принято</th>
                <th>Отпуск</th>
                <th>Расчетный остаток</th>
                <th>Фактический остаток</th>
                <th>Излишки</th>
                <th>Температура</th>
                <th>Плотность</th>
                <th>Масса</th>
            </tr>
        </thead>
        <tbody id="table-content">
        <?php foreach ($content['tanks'] as $tankFuel) :?>
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
<!--Емкости-->
<!--------------------------------------------------------------------------------------------------------------------->
<!--Счетчики рукавов-->
<div>
    <div style="text-align: center;"><h5>Счетчики рукавов</h5></div>
    <table cellpadding="1" cellspacing="1" border="0" class="table-mine  full-width box-shadow--2dp">
        <thead>
            <tr class="tr-table-header">
                <th>Номер рукава</th>
                <th>Начальный счетчик</th>
                <th>Конечный счетчик</th>
                <th>Номер колонки</th>
                <th>Номер руква в колонке</th>
                <th>Разница</th>
            </tr>
        </thead>
        <tbody id="table-content">
        <?php foreach ($content['hoses'] as $hose) :?>
            <tr class="tr-table-content">
                <td><?=$hose['HoseNum']; ?></td>
                <td><?=$hose['StartCounter']; ?></td>
                <td><?=$hose['EndCounter']; ?></td>
                <td><?=$hose['PumpNum']; ?></td>
                <td><?=$hose['NumInPump']; ?></td>
                <td><?=$hose['Outcomes']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!--Счетчики рукавов-->
<!--------------------------------------------------------------------------------------------------------------------->
<!--Отпуск-->
<div>
    <div style="text-align: center;"><h5>Отпуск топлива</h5></div>
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="pills-amount-tab" data-toggle="pill" href="#pills-amount" role="tab" aria-controls="pills-amount" aria-selected="true">Отпуск топлива деньги</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="pills-volume-tab" data-toggle="pill" href="#pills-volume" role="tab" aria-controls="pills-volume" aria-selected="false">Отпуск топлива литры</a>
        </li>

    </ul>
    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane show active" id="pills-amount" role="tabpanel" aria-labelledby="pills-amount-tab">
            <table cellpadding="1" cellspacing="1" border="0" class="table-mine  full-width box-shadow--2dp">
                <thead>
                <tr class="tr-table-header">
                    <th></th>
                    <?php foreach ($content['outcomes']['payments'] as $payment) :?>
                        <th><?=$payment; ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php for($i = 0; $i < count($content['outcomes']['byAmount']) ; $i++ ) :?>
                    <tr class="tr-table-content">
                        <td><?=@$content['outcomes']['byAmount'][$i]['Info']['FuelName'];?></td>
                        <?php foreach ($content['outcomes']['byAmount'][$i]['Payment'] as $key => $value) :?>
                            <td><?=$value; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
        </div>
        <div class="tab-pane" id="pills-volume" role="tabpanel" aria-labelledby="pills-volume-tab">
            <table cellpadding="1" cellspacing="1" border="0" class="table-mine  full-width box-shadow--2dp">
                <thead>
                <tr class="tr-table-header">
                    <th></th>
                    <?php foreach ($content['outcomes']['payments'] as $payment) :?>
                        <th><?=$payment; ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php for($i = 0; $i < count($content['outcomes']['byVolume']) ; $i++ ) :?>
                    <tr class="tr-table-content">
                        <td><?=@$content['outcomes']['byVolume'][$i]['Info']['FuelName'];?></td>
                        <?php foreach ($content['outcomes']['byVolume'][$i]['Payment'] as $key => $value) :?>
                            <td><?=$value; ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!--Отпуск-->
<!--------------------------------------------------------------------------------------------------------------------->
