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
        if ($size > $this->_file_size_limit){
            return true;
        }
        return false;
    }



    public function upload(array $files_array){
        if (empty($files_array)){
            return false;
        }
        //Подсчет количества файлов
        $totalFiles = count($files_array['file']['name']);
        //Прохожу по каждому файлу из массива и провожу его валидацию
        for ($i=0; $i< $totalFiles; $i++){
            $tmpFilePath = $_FILES['file']['tmp_name'][$i];
            $fullFileName = $_FILES['file']['name'][$i];
            $fileSize = $_FILES['file']['size'][$i];
            //Если размер файла превышает текущий лимит
            if ($this->isValidSize($fileSize)){

            }
            //Получаю расширение файла, полный путь к файлу
            $fileExtension = pathinfo($fullFileName, PATHINFO_EXTENSION);
            $serverFilePath =$this->_upload_folder.'/'.$fullFileName;
            //Если расширение файла есть в белом списке
            if ($this->isValidExtension($fileExtension)){
                //Если каким то образом, происходит загрузка файла уже имеющегося в папке, то я затираю предыдущий
                $j = 0;
                while (file_exists($serverFilePath)){
                    unlink($serverFilePath);
                    $j++;
                }
                //Если загрузка прошла успешно
                if (@move_uploaded_file($tmpFilePath, $serverFilePath)){

                }
            }
        }
    }

}