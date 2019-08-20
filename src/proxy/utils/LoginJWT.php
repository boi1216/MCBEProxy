<?php


namespace proxy\utils;


class LoginJWT
{

    /**
     * @param string $arrayJWT
     * @return string
     */
    public static function fromArray(string $arrayJWT) : string{
         $i = 0;
         foreach($arrayJWT["chain"] as $chain){
             $arrayJWT["chain"][$i] = JWT::b64UrlEncode($chain);
             $i++;
         }
         return json_encode($arrayJWT);
    }

}