<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 23.08.2018
 * Time: 12:41
 */
?>
--------------------ДЕНЬГИ-------------------
<table id="amount-by-payment" cellpadding="1" cellspacing="1" border="1">
    <thead>
        <th></th>
    <?php foreach ($arrPayments as $single) :?>
        <th><?=$single;?></th>
    <?php endforeach ?>
    </thead>
    <tbody>
    <?php for ($i = 1; $i < count($amountByPayment) + 1; $i++) :?>
        <tr>
            <td><?=$amountByPayment[$i]['Info']['FuelName'];?></td>
            <?php foreach ($amountByPayment[$i]['Payment'] as $key => $value) :?>
            <td><?=$value?></td>
            <?php endforeach; ?>
        </tr>
    <?php endfor; ?>
    </tbody>
</table>
--------------------ЛИТРЫ--------------------
<table id="volume-by-payment" cellpadding="1" cellspacing="1" border="1">
    <thead>
    <th></th>
    <?php foreach ($arrPayments as $single) :?>
        <th><?=$single;?></th>
    <?php endforeach ?>
    </thead>
    <tbody>
    <?php for ($i = 1; $i < count($volumeByPayment) + 1; $i++) :?>
        <tr>
            <td><?=$volumeByPayment[$i]['Info']['FuelName'];?></td>
            <?php foreach ($volumeByPayment[$i]['Payment'] as $key => $value) :?>
                <td><?=$value?></td>
            <?php endforeach; ?>
        </tr>
    <?php endfor; ?>
    </tbody>
</table>
