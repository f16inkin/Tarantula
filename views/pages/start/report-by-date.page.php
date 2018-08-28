<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 28.08.2018
 * Time: 11:21
 */
?>
<table id="report-by-date" cellspacing="0" cellpadding="0" border="1">
    <thead>
        <th>Дата</th>
        <th>Топливо</th>
        <th>Начальный объем</th>
        <th>Расчетный остаток</th>
        <th>Фактический остаток</th>
        <th>Излишек</th>
        <th>Принято</th>
        <th>Реализовано</th>
        <th>Фактически реализовано</th>
        <th>Плотность</th>
        <th>Температура</th>
        <th>Масса</th>
        <th>RPM</th>
    </thead>
    <tbody>
    <?php foreach($content['report']['data'] as $row):?>
        <tr>
            <td><?=date("d.m.Y", strtotime($row['date']));?></td>
            <td><?=$row['fuel_id']?></td>
            <td><?=$row['start_volume'];?></td>
            <td><?=$row['end_volume'];?></td>
            <td><?=$row['fact_volume'];?></td>
            <td><?=$row['overage'];?></td>
            <td><?=$row['income'];?></td>
            <td><?=$row['outcome'];?></td>
            <td><?=isset($row['fact_outcome']) ? $row['fact_outcome'] : 0;?></td>
            <td><?=$row['density'];?></td>
            <td><?=$row['temperature'];?></td>
            <td><?=$row['mass'];?></td>
            <td><?=isset($row['rpm']) ? $row['rpm'] : 0;?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
