<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 12.06.2018
 * Time: 11:10
 */

namespace core\models;


use core\base\Model;
use core\libs\Configurator;
use core\libs\Session;

class Login extends Model
{
    protected $_cookie_live;
    protected $_cookie_name;

    /**
     * Login constructor.
     *
     * Подгружает информацию по куки
     */
    public function __construct()
    {
        parent::__construct();
        $this->_cookie_name = Configurator::getSysConfiguration()['cookie_name'];
        $this->_cookie_live = Configurator::getSysConfiguration()['cookie_live'];
    }

    /**
     * Валидация входящего значения
     *
     * Если проверка по регулярному выражению прошла успешно, вернет входное значение иначе null
     *
     * @param $value
     * @return null|string
     */
    private function validate($value){
        $value = @trim($value);
        if(preg_match("(^[A-Za-z0-9]+$)", $value) === 1){
            return $value;
        }
        return null;
    }

    /**
     * Авторизация пользователя в системе
     *
     * Проверяет наличие учетной записи с введенными логином и паролем, после чего обновляет в случае успеха
     * обновляет соль. Записывает в сессию необходимые для дальнейшей работе данные о пользователе и создает куку
     * для дальнейшего подключения к сервису через нее
     *
     * @return bool
     */
    public function doLogin($user_name, $password){
        //Валидация пришедших значений
        $validated_user_name = $this->validate($user_name);
        $validated_password = sha1($this->validate($password));
        //Поиск пользователя по значениям
        $query = ("SELECT `id`, `login`  FROM `users` WHERE `login` = :user_name AND `password` = :password");
        $result = $this->_db->prepare($query);
        $result->execute([
            'user_name' => $validated_user_name,
            'password' => $validated_password,
        ]);
        //Если такой найден, обновляем соль, для того чтобы после следующей авторизации при украденной куке она
        //была не действительна
        if($result->rowCount()>0){
            $user = $result->fetchAll();
            $secret_key = uniqid();
            $query = "UPDATE `users` SET `secret_key` = :secret_key WHERE `id`= :id";
            $result = $this->_db->prepare($query);
            $result->execute([
                'id' => $user[0]['id'],
                'secret_key' => $secret_key,
            ]);
            //Инициализирую сессию. Заполняю ее дапнными пользователя чей идентификатор найден по логину и паролю
            Session::initUserSession($user[0]['id']);
            //Устанавливаю куки и возвращаю true в случае успеха
            setcookie(
                $this->_cookie_name,                                                    //Имя куки
                $user[0]['id']."-".sha1($secret_key.":".$_SERVER['HTTP_USER_AGENT']),   //Значение куки хэш(соль + UAgent)
                time() + (int)$this->_cookie_live,                                      //Время жизни куки
                '/');                                                                   //
            return true;
        }
        else{
            return false;
        }
    }

    /**
     * Уничтожение куки и сессии конкретного пользователя
     */
    public function doLogout(){
        setcookie(
            $this->_cookie_name,             //Имя куки
            "",                              //Значение куки
            time() - $this->_cookie_live,    //Время жизни куки
            '/');
            session_unset();
            session_destroy();
    }

}