<?php
?>
<div style="text-align: center;"><h5>Смена</h5></div>
<div>
    <p><?=$content['session']['Number'];?></p>
    <p><?=$content['session']['StartDateTime'];?></p>
    <p><?=$content['session']['EndDateTime'];?></p>
    <p><?=$content['session']['Operator'];?></p>





</div>
<div style="text-align: center;"><h5>Емкости</h5></div>
<div>
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
