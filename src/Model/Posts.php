<?php 

namespace Mathieu\ProjetPhpNoel\Model;


use Mathieu\ProjetPhpNoel\Controller\App_config;

class Posts
{
    private static $P_DATA = [];

    //Renvoie toutes les données du fichier
    public static function all()
    {
        return self::$P_DATA;
    }

    //Ajoute une donnée et la sauvegarde dans un fichier
    public static function add($b_post)
    {
        $b_post->id = count(self::$P_DATA) + 1;
        self::$P_DATA[] = $b_post;
        self::save();
        return $b_post;
    }

    //Permet de trouver les données correspondant à un id particulier
    public static function findById(int $id)
    {
        foreach(self::$P_DATA as $b_post)
        {
            if($b_post->id === $id)
            {
                return $b_post;
            }
        }

        return [];
    }

    //Charge toutes les données du fichier json
    public static function load()
    {
        $DB_PATH = App_config::get('DB_PATH', __DIR__ . '/../../db.json');
        self::$P_DATA = json_decode(file_get_contents($DB_PATH));
    }

    //Récupère le fichier json ou sont stockés les données et insère une nouvelle donnée 
    public static function save()
    {
        $DB_PATH = App_config::get('DB_PATH', __DIR__ . '/../../db.json');
        file_put_contents($DB_PATH, json_encode(self::$P_DATA, JSON_PRETTY_PRINT));
    }
}