<?php


namespace proxy\utils;


class LoginJWT extends JWT
{

    /**
     * @param string $arrayJWT
     * @return string
     */
    public static function fromArray(string $arrayJWT) : string{
         $i = 0;
         foreach($arrayJWT["chain"] as $chain){
             $arrayJWT["chain"][$i] = self::b64UrlEncode($chain);
             $i++;
         }
         return json_encode($arrayJWT);
    }

}