<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 18.04.2019
 * Time: 16:03
 */

namespace application\parser\models;


class FileUploader
{
    private $_valid_types = ['xml', 'txt', 'json']; //Допустимые для обработки парсером форматы.

    public function uploadFiles(){
        //$files = array_filter($_FILES['upload']['name']); something like that to be used before processing files.
        // Count # of uploaded files in array
        $total = count($_FILES['upload']['name']);
        // Loop through each file
        for($i=0; $i<$total; $i++) {
            //Get the temp file path
            $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
            //Make sure we have a filepath
            if ($tmpFilePath != ""){
                //Setup our new file path
                $newFilePath = "./uploadFiles/" . $_FILES['upload']['name'][$i];
                //Upload the file into the temp dir
                if(move_uploaded_file($tmpFilePath, $newFilePath)) {
                    //Handle other code here
                }
            }
        }
    }

}