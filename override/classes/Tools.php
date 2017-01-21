<?php

class Tools extends ToolsCore
{
    // new method for encrypting passwords
    // we're not overriding the encrypt method because it might break other functionalities
    // dependent on encrypt method using md5
    public static function encryptPass($passwd)
    {
        // we're not using PASSWORD_DEFAULT  since it may change over time and cause problems
        $hash = password_hash($passwd, PASSWORD_BCRYPT);

        // TODO: handle error
        if ($hash === false) {
        }
            
        return $hash;
    }

    public static function verifyPass($passwd, $hash)
    {
        return password_verify($passwd, $hash);
    }
}
