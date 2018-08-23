<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 23.08.2018
 * Time: 9:58
 */

namespace models;


use base\ModelTarantula;

class XmlParser //extends ModelTarantula
{
    private $_arrPayments;
    public function __construct()
    {
        //parent::__construct();
        $this->_arrPayments = [
            'Наличные',
            'Ведомость',
            'Без скидки',
            'Банк.карта',
            'Карты ТОПДОН',
            'Дисконтная карта',
            'Со скидкой'
        ];
    }

    public function calcVolumeByPayment($path){
        $arrVolume = [];
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $arrVolume[$TankNum]['Info']['FuelName'] = $FuelName;
            for ($i = 0; $i < 7; $i++){
                $arrVolume[$TankNum]['Payment'][$this->_arrPayments[$i]] = 0;
            }
        }
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $PaymentModeName = (string)$item['PaymentModeName'];
            $Amount = str_replace(',', '.', (string) $item['Volume']);
            $arrVolume[$TankNum]['Info']['FuelName'] = $FuelName;
            $arrVolume[$TankNum]['Payment'][$PaymentModeName] += $Amount;
        }
        return $arrVolume;
    }

    public function calcAmountByPayment($path){
        $arrAmount = [];
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $arrAmount[$TankNum]['Info']['FuelName'] = $FuelName;
            for ($i = 0; $i < 7; $i++){
                $arrAmount[$TankNum]['Payment'][$this->_arrPayments[$i]] = 0;
            }
        }
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $PaymentModeName = (string)$item['PaymentModeName'];
            $Amount = str_replace(',', '.', (string) $item['Amount']);
            $arrAmount[$TankNum]['Info']['FuelName'] = $FuelName;
            $arrAmount[$TankNum]['Payment'][$PaymentModeName] += $Amount;
        }
        return $arrAmount;
    }

    public function calcElementsByPayment($path, $element){
        $arrAmount = [];
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $arrAmount[$TankNum]['Info']['FuelName'] = $FuelName;
            for ($i = 0; $i < 7; $i++){
                $arrAmount[$TankNum]['Payment'][$this->_arrPayments[$i]] = 0;
            }
        }
        foreach ($path->Sessions->Session->OutcomesByRetail->OutcomeByRetail as $item){
            $TankNum = (string)$item['TankNum'];
            $FuelName = (string)$item['FuelName'];
            $PaymentModeName = (string)$item['PaymentModeName'];
            $Amount = str_replace(',', '.', (string) $item[$element]);
            $arrAmount[$TankNum]['Info']['FuelName'] = $FuelName;
            $arrAmount[$TankNum]['Payment'][$PaymentModeName] += $Amount;
        }
        return $arrAmount;
    }


    public function calcHosesCountersValues($path){
        $hosesCountersValues = [];
            foreach ($path->Sessions->Session->Hoses->Hose as $item) {
                $hoseNum = (string)$item['HoseNum'];
                $startCounter = (string)$item['StartCounter'];
                $endCounter = (string)$item['EndCounter'];
                $hosesCountersValues[$hoseNum]['HoseNum'] = $hoseNum; //Номер рукава
                $hosesCountersValues[$hoseNum]['StartCounter'] = $startCounter; //Начальный счетчик
                $hosesCountersValues[$hoseNum]['EndCounter'] = $endCounter;   //Конечный счетчик
            }
        return $hosesCountersValues;
    }

    /**
     * @return array
     */
    public function getArrPayments(): array
    {
        return $this->_arrPayments;
    }

}