<?php


namespace application\parser\models;


class ReportsUploader
{
    private $_valid_types = ['xml', 'txt'];
    private $_upload_folder;
    private $_file_size_limit = 32000;

    public function __construct(string $storage)
    {
        $this->_upload_folder = $storage.'/'.$_SESSION['user']['id'].'-'.$_SESSION['user']['login'];
    }

    private function isValidExtension(string $extension) : bool {
        if (in_array($extension, $this->_valid_types)){
            return true;
        }
        return false;
    }

    private function isValidSize(int $size) : bool {
        if ($size < $this->_file_size_limit){
            return true;
        }
        return false;
    }



    public function upload(array $files_array){
        $data = [];
        $executionResult = [];
        if (empty($files_array)){
            $executionResult['status'] = 'fail';
            $executionResult['message'] = 'Файлы для загрузки отсутсвуют';
        }
        //Подсчет количества файлов
        $totalFiles = count($files_array['file']['name']);
        //Прохожу по каждому файлу из массива и провожу его валидацию
        for ($i = 0; $i < $totalFiles; $i ++){
            $tmpFilePath = $_FILES['file']['tmp_name'][$i];
            $fullFileName = $_FILES['file']['name'][$i];
            $fileSize = $_FILES['file']['size'][$i];
            //Если размер файла превышает текущий лимит

            //Получаю расширение файла, полный путь к файлу
            $fileName = pathinfo($fullFileName, PATHINFO_FILENAME);
            $fileExtension = pathinfo($fullFileName, PATHINFO_EXTENSION);
            $fullFilePath =$this->_upload_folder.'/'.$fullFileName;
            //Если расширение файла есть в белом списке
            if ($this->isValidExtension($fileExtension)){
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
                }else{
                    $executionResult['status'] = 'fail';
                    $executionResult['message'] = 'Файл размера больше допустимого';
                }
            }else{
                $executionResult['status'] = 'fail';
                $executionResult['message'] = 'Файл имеет не верное расширение';
            }
            $data['executionResult'][$i] = $executionResult;
            $executionResult = [];
        }
        return $data;
    }

}