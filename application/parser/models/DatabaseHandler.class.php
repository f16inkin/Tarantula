<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 15.04.2019
 * Time: 12:22
 */

namespace application\parser\models;


use application\parser\base\AbstractFileHandler;
use core\base\Model;

class DatabaseHandler extends Model
{
    private $_handler;

    /**
     * Принимает в качестве параметра класс обработчик файлов (XML, JSON, TXT обработчики)
     * -----------------------------------------------------------------------------------
     * DatabaseHandler constructor.
     * @param AbstractFileHandler $handler
     */
    public function __construct(AbstractFileHandler $handler)
    {
        parent::__construct();
        $this->_handler = $handler;
    }

    /**
     * Очищает временную таблицу с информацией о файлах в хранилище
     * ------------------------------------------------------------
     * @return bool
     */
    private function clearTable(){
        try{
            $query = ("DELETE FROM `tarantula_temporary` WHERE `user` = :user_id");
            $result = $this->_db->prepare($query);
            $result->execute([
                'user_id' => $_SESSION['user']['id']
            ]);
            return $result->execute() ? true : false;
        }catch (\Exception $e){
            echo "Db Error";
        }
    }

    /**
     * @return array|null
     */
    private function findExist($files){
        try{
            //Получаю записи с именами файлов из БД
            $query = ("SELECT `file_name` FROM `tarantula_temporary` WHERE `file_name` IN (");
            foreach ($files as $file){
                $query .= sprintf("%s, ", "'".$file['fileName']."'");
            }
            //Обрезаю в конце запроса запятую
            $query = rtrim($query, ' ,');
            $query .= ')';
            $result = $this->_db->prepare($query);
            $result->execute();
            if ($result->rowCount() > 0){
                while($row = $result->fetch()) {
                    $records[] = $row['file_name'];
                }
                return $records;
            }
        }catch (\Exception $e){
            echo 'DataBase Error';
        }
        return null;
    }

    /**
     * Метод получает иформацию о файлах в хранилище и заносит ее в таблицу, для дальнейшей обработки парсером
     * -------------------------------------------------------------------------------------------------------
     * @return bool
     */
    public function fillTable(){
        //Получаю имена файлов из хранилища
        $files = $this->_handler->scanStorage();
        //Если в хранилище присутсвуют файлы, очищаю сначала
        if (isset($files)){
            try{
                //Ищу совпадения по именам файлов во временной таблице
                $exist_files = $this->findExist($files);
                //Если найдены совпадающие файлы в очереди на загрузку
                if (isset($exist_files)){

                }
                //Если таблица с временными данными о хранящихся файлах очищена
                if ($this->clearTable()){
                    //Формирую полузапрос для вставки данных:
                    $query = ("INSERT INTO `tarantula_temporary` (`file_name`, `is_correct`, `user`)
                       VALUES ");
                    //Далее формирую оставшуюся часть до полного запроса
                    foreach ($files as $row){
                        $query .= sprintf("(%s, %s, %s),",
                            "'".$row['fileName']."'", //Не приводится к строчному виду потому выдает ошибку БД
                            $row['isCorrect'],
                            $_SESSION['user']['id']
                        );
                    }
                    //Обрезаю в конце запроса запятую
                    $query = rtrim($query, ',');
                    //Выполняю запрос
                    $result = $this->_db->prepare($query);
                    //Верну ответ об успехе или наоборот
                    return $result->execute() ? true : false;
                }
            }catch (\Exception $e){
                echo 'DataBase Error';
            }
        }
        return false;
    }

    /**
     * Возвращает в случае успеха массив с файлами требуемого типа (корректные/не корректные)
     * array => [
     * 0 => [id => 1, file_name => CloseSession_1, is_correct => 1, user => 1]
     * 1 => [id => 1, file_name => CloseSession_2, is_correct => 1, user => 1]
     * ];
     * 1 - корректные файлы, 0 - некорректные файлы
     * --------------------------------------------------------------------------------------
     * @param int $is_correct
     * @return array|null
     */
    public function scanDataBase(int $is_correct){
        try{
            //Считываю данные о файлах из БД
            $query = ("SELECT * FROM `tarantula_temporary`
                       WHERE `is_correct` = :is_correct AND `user` = :user_id");
            $result = $this->_db->prepare($query);
            $result->execute([
                'is_correct' => $is_correct,
                'user_id' => $_SESSION['user']['id']
            ]);
            if ($result->rowCount() > 0){
                $files = $result->fetchAll();
                return $files;
            }
        }catch (\Exception $e){
            echo "Db Error";
        }
        return null;
    }

    /**
     * Метод возвращает массив с объектами simpleXML для дальнейшей их обработки.
     * --------------------------------------------------------------------------
     * @return array
     */
    public function loadCorrectXml(){
        $files = $this->scanDataBase(1);
        $simpleXmlElements = [];
        $folder = $this->_handler->getStorage().'/'.$_SESSION['user']['id'].'-'.$_SESSION['user']['login'];
        for ($i = 0; $i < count($files); $i++){
            $simpleXmlElements[$i]['record_id'] = $files[$i]['id']; //id файла в таблцие временных файлов
            $simpleXmlElements[$i]['file_name'] = $files[$i]['file_name']; //имя файла в таблице временных файлов
            $simpleXmlElements[$i]['simpleXmlElement'] = simplexml_load_file($folder.'/'.$files[$i]['file_name']) ? simplexml_load_file($folder.'/'.$files[$i]['file_name']) : null;
        }
        //Возвращаю обработку ошибок в стандартное положение.
        libxml_use_internal_errors(false);
        return $simpleXmlElements;
    }

}