<?php


namespace application\parser\models;


class ReportsUploader
{
    private $_valid_types = ['xml', 'txt']; //белый список
    private $_upload_folder;                //пользовательская директория
    private $_file_size_limit = 32000;      //максимальный размер загружаемого файла

    /**
     * ReportsUploader constructor.
     * @param string $storage
     */
    public function __construct(string $storage)
    {
        $this->_upload_folder = $storage.'/'.$_SESSION['user']['id'].'-'.$_SESSION['user']['login'];
    }

    /**
     * Проверяет есть ли файл в белом списке
     * -------------------------------------
     * @param string $extension
     * @return bool
     */
    private function isValidExtension(string $extension) : bool {
        if (in_array($extension, $this->_valid_types)){
            return true;
        }
        return false;
    }

    /**
     * Проверяет размер загружаемого файла
     * ----------------------
     * @param int $size
     * @return bool
     */
    private function isValidSize(int $size) : bool {
        if ($size < $this->_file_size_limit){
            return true;
        }
        return false;
    }

    /**
     * Загружает в директорию пользователя файлы переданные
     * ----------------------------------------------------
     * @param array $files_array
     * @return array
     */
    public function upload(array $files_array) : array {
        $data = []; //выходной массив
        $executionResult = [];  //массив с сообщениями и статусом выполенения загрузки файла
        if (empty($files_array)){
            $executionResult['status'] = 'fail';
            $executionResult['message'] = 'Файлы для загрузки отсутсвуют';
        }
        //Подсчет количества файлов
        $totalFiles = count($files_array['name']);
        //Прохожу по каждому файлу из массива и провожу его валидацию
        for ($i = 0; $i < $totalFiles; $i ++){
            $tmpFilePath = $files_array['tmp_name'][$i];
            $fullFileName = $files_array['name'][$i];
            $fileSize = $files_array['size'][$i];
            //Получаю расширение файла, полный путь к файлу
            $fileName = pathinfo($fullFileName, PATHINFO_FILENAME);
            $fileExtension = pathinfo($fullFileName, PATHINFO_EXTENSION);
            $fullFilePath =$this->_upload_folder.'/'.$fullFileName;
            //Если расширение файла есть в белом списке
            if ($this->isValidExtension($fileExtension)){
                //Если размер файла не превышает текущий лимит
                if ($this->isValidSize($fileSize)){
                    //Если каким то образом, происходит загрузка файла уже имеющегося в папке, то я затираю предыдущий
                    $j = 0;
                    while (file_exists($fullFilePath)){
                        unlink($fullFilePath);
                        $executionResult['status'] = 'warning';
                        $executionResult['message'] = 'Файл - двойник '.$fileName.' земенен новым';
                        $j++;
                    }
                    //Если загрузка прошла успешно
                    if (@move_uploaded_file($tmpFilePath, $fullFilePath)){
                        if (empty($executionResult['status'])){
                            $executionResult['status'] = 'success';
                            $executionResult['message'] = 'Файл '.$fileName.' загружен';
                        }
                    }
                }
                //Недопустимо большой файл по размеру
                else{
                    $executionResult['status'] = 'fail';
                    $executionResult['message'] = 'Файл размера больше допустимого';
                }
            }
            //Расширение не в белом списке
            else{
                $executionResult['status'] = 'fail';
                $executionResult['message'] = 'Файл имеет не верное расширение';
            }
            //Наполнение выходного массива результатами загрузки файлов
            $data['executionResult'][$i] = $executionResult;
            $executionResult = [];
        }
        return $data;
    }

}