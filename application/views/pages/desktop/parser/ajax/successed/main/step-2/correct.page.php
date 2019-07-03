<?php
?>
<div id="accordion">
    <!--Смены-->
    <div class="session-section">
        <div style="text-align: center;"><h6>Смена № <?=$content['session']['Number'];?></h6></div>
        <div>
            <div><b class="fa fa-clock"> Начата:</b> <?=$content['session']['StartDateTime'];?></div>
            <div><b class="fa fa-clock"> Окончена:</b> <?=$content['session']['EndDateTime'];?></div>
            <div><b class="fa fa-user"> Оператор:</b> <?=$content['session']['Operator'];?></div>
        </div>
    </div>
    <!--Смены-->
    <!--------------------------------------------------------------------------------------------------------------------->
    <!--Емкости-->
    <div class="card" style="margin-bottom: 3px;">
        <div class="card-header" id="headingOne">
            <h5 class="mb-0">
                <button class="btn btn-outline-dark" style="width: 100%" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <div class="row">
                        <div class="col-1">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="col-10">
                            <i class="fa fa-fill-drip"> Емкости</i>
                        </div>
                    </div>
                </button>
            </h5>
        </div>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
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
                    <tbody>
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
        </div>
    </div>
    <!--Емкости-->
    <!--------------------------------------------------------------------------------------------------------------------->
    <!--Счетчики рукавов-->
    <div class="card" style="margin-bottom: 3px;">
        <div class="card-header" id="headingTwo">
            <h5 class="mb-0">
                <button class="btn btn-outline-dark" style="width: 100%" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                    <div class="row">
                        <div class="col-1">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="col-10">
                            <i class="fa fa-tachometer-alt"> Счетчики рукавов</i>
                        </div>
                    </div>
                </button>
            </h5>
        </div>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
            <div class="card-body">
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
                    <tbody>
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
        </div>
    </div>
    <!--Счетчики рукавов-->
    <!--------------------------------------------------------------------------------------------------------------------->
    <!--Отпуск-->
    <?php if(!empty($content['outcomes'])) :?>
    <div class="card" style="margin-bottom: 3px;">
        <div class="card-header" id="headingThree">
            <h5 class="mb-0">
                <button class="btn btn-outline-dark" style="width: 100%" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                    <div class="row">
                        <div class="col-1">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="col-10">
                            <i class="fa fa-gas-pump"> Отпуск топлива</i>
                        </div>
                    </div>
                </button>
            </h5>
        </div>
        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
            <div class="card-body">
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
            </div>
        </div>
    <?php endif; ?>
    <!--Отпуск-->
    <!--------------------------------------------------------------------------------------------------------------------->
    <!--Принято-->
    <?php if(!empty($content['incomes'])) :?>
    <div class="card" style="margin-bottom: 3px;">
        <div class="card-header" id="headingFour">
            <h5 class="mb-0">
                <button class="btn btn-outline-dark" style="width: 100%" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                    <div class="row">
                        <div class="col-1">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="col-10">
                            <i class="fa fa-truck-moving"> Принято топлива</i>
                        </div>
                    </div>
                </button>
            </h5>
        </div>
        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
            <div class="card-body">
                <table cellpadding="1" cellspacing="1" border="0" class="table-mine  full-width box-shadow--2dp">
                    <thead>
                    <tr class="tr-table-header">
                        <th>Емкость</th>
                        <th>Топливо</th>
                        <th>Плотность</th>
                        <th>Масса</th>
                        <th>Объем</th>
                        <th>Поставщик</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($content['incomes'] as $income) :?>
                        <tr class="tr-table-content">
                            <td><?=$income['TankNum']; ?></td>
                            <td><?=$income['FuelName']; ?></td>
                            <td><?=$income['Density']; ?></td>
                            <td><?=$income['Mass']; ?></td>
                            <td><?=$income['Volume']; ?></td>
                            <td><?=$income['PartnerName']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!--Принято-->
    <!--------------------------------------------------------------------------------------------------------------------->
</div>
