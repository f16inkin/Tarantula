<?php


namespace application\parser\models;


use core\base\Model;

class XmlReportsHandler extends Model
{
    private $_folder;

    public function __construct(string $storage)
    {
        parent::__construct();
        $this->_folder = $storage.'/'.$_SESSION['user']['id'].'-'.$_SESSION['user']['login']; //пользовательская папка
        //Если отсутсвует папка хранилище создаст ее. Пока пусть будет, но в планах удалить эту проверку
        //При автоматической установке модуля создавать нужную директорию
        if (!file_exists($storage)){
            mkdir($storage);
        }
    }

    /**
     * Метод сканирует указанную директорию(хранилище) и определяет какие файлы пригодны для работы с парсером
     * в дальнейшем. Если это XML он отнесет их к корректным, иначе это будут некорректные файлы.
     * -------------------------------------------------------------------------------------------------------
     * @return array|null
     */
    private function scanStorage(){
        //Сканирую директорию на наличие XML отчетов
        $files = array_slice(scandir($this->_folder),2);
        //Если директория пуста, верну null
        if (empty($files)){
            return null;
        }
        $dividedFiles= [];
        //Отключаю ошибки libxml и беру полномочия на обработку ошибок на себя.
        libxml_use_internal_errors(true);
        /**
         * Получаю имена всех файлов находящихся в директории storage. Затем проверяю являются ли эти файлы
         * в формате XML. Если файлы попадают под такое определение то они сортируются в секцию correct files, иначе
         * определяются как incorrect
         *
         */
        for ($i = 0; $i < count($files); $i++){
            $fileName = $files[$i];
            $isCorrect = simplexml_load_file($this->_folder.'/'.$files[$i]) ? true : false;
            $dividedFiles[$i]['fileName'] = $fileName;
            $dividedFiles[$i]['isCorrect'] = $isCorrect ? 1 : 0;

        }
        //Возвращаю обработку ошибок в стандартное положение.
        libxml_use_internal_errors(false);
        return $dividedFiles;
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
     * Метод получает иформацию о файлах в хранилище и заносит ее в таблицу, для дальнейшей обработки парсером
     * -------------------------------------------------------------------------------------------------------
     * @return bool
     */
    private function fillTable(){
        //Получаю имена файлов из хранилища
        $files = $this->scanStorage();
        //Если в хранилище присутсвуют файлы, очищаю сначала
        if (isset($files)){
            try{
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
    public function scanDataBase(){
        try{
            //Если таблица временных файлов заполнена
            if ($this->fillTable()){
                //Считываю данные о файлах из БД
                $query = ("SELECT * FROM `tarantula_temporary`
                       WHERE `user` = :user_id");
                $result = $this->_db->prepare($query);
                $result->execute([
                    'user_id' => $_SESSION['user']['id']
                ]);
                if ($result->rowCount() > 0){
                    $files = $result->fetchAll();
                    return $files;
                }
            }
            return null;
        }catch (\Exception $e){
            echo "Db Error";
        }
    }

    public function loadXmlFile(string $file_name){
        libxml_use_internal_errors(true);
        $simpleXmlElement= simplexml_load_file($this->_folder.'/'.$file_name);
        libxml_use_internal_errors(false);
        return $simpleXmlElement;
    }

    /**
     * Метод возвращает массив с объектами simpleXML для дальнейшей их обработки.
     * ---------------------------------------------------------------
     * @return array
     */
    public function loadCorrectXml(){
        $files = $this->scanDataBase();
        $simpleXmlElements = [];
        libxml_use_internal_errors(true);
        if (isset($files)){
            for ($i = 0; $i < count($files); $i++){
                $simpleXmlElements[$i]['record_id'] = $files[$i]['id']; //id файла в таблцие временных файлов
                $simpleXmlElements[$i]['file_name'] = $files[$i]['file_name']; //имя файла в таблице временных файлов
                $simpleXmlElements[$i]['simpleXmlElement'] = simplexml_load_file($this->_folder.'/'.$files[$i]['file_name']) ? simplexml_load_file($this->_folder.'/'.$files[$i]['file_name']) : null;
            }
        }
        libxml_use_internal_errors(false);
        return $simpleXmlElements;
    }

    //Test method
    public function loadXmlPage($start_file, $end_file){
        //Выбираю только нужные 10 файлов не подгружая всю директорию
        $query = ("SELECT * FROM `tarantula_temporary`
                       WHERE `user` = :user_id LIMIT :start_file,:end_file");
        $result = $this->_db->prepare($query);
        $result->execute([
            'user_id' => $_SESSION['user']['id'],
            'start_file' => $start_file,
            'end_file' => $end_file
        ]);
        if ($result->rowCount() > 0){
            $files = $result->fetchAll();
            //return $files;
        }
        $simpleXmlElements = [];
        libxml_use_internal_errors(true);
        if (isset($files)){
            for ($i = 0; $i < count($files); $i++){
                $simpleXmlElements[$i]['record_id'] = $files[$i]['id']; //id файла в таблцие временных файлов
                $simpleXmlElements[$i]['file_name'] = $files[$i]['file_name']; //имя файла в таблице временных файлов
                $simpleXmlElements[$i]['simpleXmlElement'] = simplexml_load_file($this->_folder.'/'.$files[$i]['file_name']) ? simplexml_load_file($this->_folder.'/'.$files[$i]['file_name']) : null;
            }
        }
        libxml_use_internal_errors(false);
        return $simpleXmlElements;
    }

}