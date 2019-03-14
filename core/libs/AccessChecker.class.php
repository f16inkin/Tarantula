<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 10.07.2018
 * Time: 19:04
 */

namespace core\libs;


use core\base\Model;
use core\models\Subdivision;

class AccessChecker extends Model
{
    private $_subdivisions;   //Подразделения

    /**
     * AccessChecker constructor.
     *
     * Выбирает все доступные пользователю подразделения в массив
     *
     * @param $user_id
     */
    public function __construct($user_id)
    {
        parent::__construct();
        $this->_subdivisions = (new Subdivision())->getUserSubdivisions($user_id);
    }

    /**
     *
     * Вернет ответ на вопрос: "Имеет ли пользователь доступ к этому подразделению?"
     *
     * @param int $subdivision_id
     * @return bool
     */
    public function hasAccessToSubdivision(int $subdivision_id){
        if (isset($this->_subdivisions)){
            foreach ($this->_subdivisions as $subdivision){
                if ($subdivision_id === $subdivision['id']){
                    return true;
                }
            }
        }
        return false;
    }
}