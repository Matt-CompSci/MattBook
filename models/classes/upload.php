<?php

class upload
{
    public static function uploadFile($fileToUpload, $allowedExtensions) {
        if(isset($fileToUpload) && !$fileToUpload["error"] == UPLOAD_ERR_NO_FILE) {
            $fileType = strtolower(pathinfo($fileToUpload["name"], PATHINFO_EXTENSION));
            $fileName = uniqid() . "." . $fileType;
            $uploadPath = "files/" . $fileName;

            if (!in_array($fileType, $allowedExtensions)) {
                return false;
            } elseif ($fileToUpload["size"] > 500000) {
                return false;
            } else {
                move_uploaded_file($fileToUpload["tmp_name"], $uploadPath);
                return $fileName;
            }
        } else {
            return false;
        }
    }
}