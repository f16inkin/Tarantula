<?php
?>
<div id="accordion">
    <!--Смены-->
    <div style="text-align: center;"><h6>Смена</h6></div>
    <div>
        <p><?=$content['session']['Number'];?></p>
        <p><?=$content['session']['StartDateTime'];?></p>
        <p><?=$content['session']['EndDateTime'];?></p>
        <p><?=$content['session']['Operator'];?></p>
    </div>
    <!--Смены-->
    <!--------------------------------------------------------------------------------------------------------------------->
    <!--Емкости-->
    <div class="card">
        <div class="card-header" id="headingOne">
            <h6 class="mb-0">
                <button class="btn btn-outline-dark" style="width: 100%" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <i class="fa fa-fill-drip"></i> Емкости
                </button>
            </h6>
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
    <div class="card">
        <div class="card-header" id="headingTwo">
            <h5 class="mb-0">
                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                    <div style="text-align: center;"><h6>Счетчики рукавов</h6></div>
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
    <div class="card">
        <div class="card-header" id="headingThree">
            <h5 class="mb-0">
                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                    <div style="text-align: center;"><h6>Отпуск топлива</h6></div>
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
    <!--Отпуск-->
    <!--------------------------------------------------------------------------------------------------------------------->
</div>
