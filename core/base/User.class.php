<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 10.06.2018
 * Time: 15:44
 */

namespace core\base;


use core\libs\Auth;
use core\exceptions\DatabaseException;
use core\libs\Session;

class User extends Model
{
    protected $_id;             //Уникальный идентификатор
    protected $_login;          //Имя учетной записи
    protected $_surname;        //Фамилия
    protected $_firstname;      //Имя
    protected $_secondname;     //Отчество
    protected $_shortname;      //Сокращенное имя
    protected $_foto;           //Ссылка на фотографи.
    protected $_roles;          //Массив ролей данного пользователя

    /**
     * User constructor.
     *
     * Создает класс пользователя с базовой для всех наследников конфигурацией
     * Если подается true в конструктор, то объект обязательн опройдет аутентификацию и авторизацию
     * Если подается false, то объект будет использоватся для манипуляции связанными с ним данными в БД
     *
     */
    public function __construct(bool $doAuthorize)
    {
        parent::__construct();
        if ($doAuthorize){
            Auth::authorize($this);
        }
    }

    /**
     * Получает данные по конкретному пользователю в виде массива
     *
     * @param int $user_id
     * @return array
     */
    public function get($user_id){
        try{
            $query = ("SELECT * FROM `users` WHERE `id` = :user_id");
            $result = $this->_db->prepare($query);
            $result->execute([
                'user_id' => $user_id,
            ]);
            if ($result->rowCount() > 0){
                return $result->fetchAll();
            }
            return null;
        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }

    }

    /**
     * Получает список всех пользователей системы
     *
     * @return array
     */
    public function getAll(){
        try{
            $query = ("SELECT * FROM `users`");
            $result = $this->_db->prepare($query);
            $result->execute();
            if ($result->rowCount() > 0){
                return $result->fetchAll();
            }
            return null;
        }catch (DatabaseException $e){
            echo 'Выброшено исключение: ',  $e->getMessage();
            echo '<br>';
            echo 'Выброшено исключение на строке: ',  $e->getLine();
            echo '<br>';
            echo 'Выброшено исключение в файле: ',  $e->getFile();
        }
    }

    /**
     * Добавляет запись о новом польователе в БД
     */
    public function insert(){

    }

    /**
     * Обновляет данные для выбранного пользователя
     *
     * @param $user_id
     */
    public function update($user_id){
        Session::setState('need_to_update');
    }

    public function delete($user_id){

    }

    /**
     * Getter
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->_id;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getLogin(): string
    {
        return $this->_login;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->_surname;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->_firstname;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getSecondname()
    {
        return $this->_secondname;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getShortname()
    {
        return $this->_shortname;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getFoto()
    {
        return $this->_foto;
    }

    /**
     * Getter
     *
     * Возвращает все роли, а так же привелегигии доступные текущему пользователю
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->_roles;
    }

    /**
     * Setter
     *
     * @param int $id
     */
    public function setId($id){
        $this->_id = $id;
    }

    /**
     * Setter
     *
     * @param string $login
     */
    public function setLogin($login){
        $this->_login = $login;
    }

    /**
     * Setter
     *
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->_surname = $surname;
    }

    /**
     * Setter
     *
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->_firstname = $firstname;
    }

    /**
     * Setter
     *
     * @param string $secondname
     */
    public function setSecondname($secondname)
    {
        $this->_secondname = $secondname;
    }

    /**
     * Setter
     *
     * @param string $shortname
     */
    public function setShortname($shortname)
    {
        $this->_shortname = $shortname;
    }

    /**
     * Setter
     *
     * @param string $foto
     */
    public function setFoto($foto)
    {
        $this->_foto = $foto;
    }

    /**
     * Setter
     *
     * @param string $roles
     */
    public function setRoles($roles)
    {
        $this->_roles = $roles;
    }

}