<?php 

namespace Mathieu\ProjetPhpNoel\Controller;

class App_config
{
    private static $app_config;

    //Créer la configuration du serveur
    public static function get($key, $default = null)
    {
        if(is_null(self::$app_config))
        {
            self::$app_config = require_once(__DIR__.'/../../app_config.php');
        }

        return !empty(self::$app_config[$key])? self::$app_config[$key] : $default;
    }
}