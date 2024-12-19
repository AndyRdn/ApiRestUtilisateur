<?php

namespace App\Service;

class ResponseService
{

    public static function getJSONTemplate(string $etat, array $message){
        if($etat == "success"){
            return [
                "status" => "success",
                "data" => $message,
                "error" => null
            ];
        } else {
            return [
                "status" => "error",
                "data" => null,
                "error" => $message
            ];
        }
    }

}