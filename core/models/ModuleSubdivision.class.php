<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 10.11.2018
 * Time: 18:38
 */

namespace core\models;


use core\base\Model;
use core\exceptions\DatabaseException;

class ModuleSubdivision extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     * Метод вернет все подразделения, с которыми может работать запрашиваемый модуль
     *
     * @param string $module_name
     * @return array|null
     */
    private function getModuleSubdivisions(string $module_name){
        try{
            $query = ("SELECT `subdivision` 
                       FROM `module_subdivisions` 
                       WHERE `module_name` = :module_name");
            $result = $this->_db->prepare($query);
            $result->execute([
                'module_name' => $module_name
            ]);
            if ($result->rowCount() > 0){
                $module_subdivisions = $result->fetchAll();
                return $module_subdivisions;
            }
            return null;
        }
        catch (DatabaseException $e){

        }
    }

    /**
     *
     * Метод вернет идентификаторы подразделений, который доступны для работы в данном модуле для текущего пользователя
     *
     * @param array $user_subdivisions
     * @param array $module_subdivisions
     * @return array
     */
    private function getAvailableIds(array $user_subdivisions, array $module_subdivisions){
        try{
            foreach ($user_subdivisions as $single){
                $user_subdivision_ids[] = $single['id'];
            }
            foreach ($module_subdivisions as $single){
                $module_subdivisions_id[] = $single['subdivision'];
            }
            $availableIds = array_intersect($user_subdivision_ids, $module_subdivisions_id);
            return $availableIds;
        }
        catch (\Exception $e){

        }
    }

    /**
     *
     * Метод вернет полную информацию (id, имя, адресс) подразделений, которые доступны текущему пользователю в текущем
     * модуле
     *
     * @param int $user_id
     * @param string $module_name
     * @return array|null
     */
    public function getAvailableSubdivisions(int $user_id, string $module_name){
        try{
            $user_subdivisions = (new Subdivision())->getUserSubdivisions($user_id);
            if (isset($user_subdivisions)){
                $module_subdivisions = $this->getModuleSubdivisions($module_name);
                $available_subdivision_ids = $this->getAvailableIds($user_subdivisions, $module_subdivisions);
                foreach ($available_subdivision_ids as $key => $value){
                    $available_subdivisions[] = $user_subdivisions[$key];
                }
                return $available_subdivisions;
            }
            return null;
        }
        catch(\Exception $e){

        }
    }

}