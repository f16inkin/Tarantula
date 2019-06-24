<?php
?>
<div style="text-align: center;"><h5>Смена</h5></div>
<div>
    <p><?=$content['session']['Number'];?></p>
    <p><?=$content['session']['StartDateTime'];?></p>
    <p><?=$content['session']['EndDateTime'];?></p>
    <p><?=$content['session']['Operator'];?></p>
</div>

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
<div>
    <div style="text-align: center;"><h5>Отпуск топлива</h5></div>
    <table cellpadding="1" cellspacing="1" border="0" class="table-mine  full-width box-shadow--2dp">
        <thead>
            <tr class="tr-table-header">
                <th>Вид оплаты</th>
                <th>Аи-92</th>
                <th>Аи-95</th>
                <th>Аи-98</th>
                <th>Дт Евро</th>
            </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
