<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 06.09.2018
 * Time: 10:12
 */
?>
<table id="report-by-xml" cellspacing="1" cellpadding="1" border="1">
    <thead>
    <th>Дата</th>
    <th>Топливо</th>
    <th>Начальный объем</th>
    <th>Расчетный остаток</th>
    <th>Фактический остаток</th>
    <th>Принято</th>
    <th>Реализовано</th>
    <th>Плотность</th>
    <th>Температура</th>
    <th>Масса</th>
    </thead>
    <tbody>
    <?php foreach($data as $singleData):?>
        <?php foreach ($singleData as $row) :?>
            <tr>
                <td><?=$row['StartDate'];?></td>
                <td><?=$row['Fuel'];?></td>
                <td><?=$row['StartFuelVolume'];?></td>
                <td><?=$row['EndFuelVolume'];?></td>
                <td><?=$row['EndFactVolume'];?></td>
                <td><?=$row['Income'];?></td>
                <td><?=isset($row['Outcome']) ? $row['Outcome'] : 0;?></td>
                <td><?=$row['EndDensity'];?></td>
                <td><?=$row['EndTemperature'];?></td>
                <td><?=$row['EndMass'];?></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>
