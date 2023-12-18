<?php 

namespace Mathieu\ProjetPhpNoel\Controller;

use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;

class Logger extends \Monolog\Logger
{
    private static $log_sys = [];

    //Construit le système de log
    //Test si $app_config existe, il le push dans 
    private function __construct($key = "app", $app_config = null)
    {
        parent::__construct($key);

        if(empty($app_config))
        {
            $LOG_PATH = App_config::get('LOG_PATH', __DIR__ . '/../../log-files');
            $app_config = [
                'logFile' => "{$LOG_PATH}/{$key}.log",
                'logLevel' => \Monolog\Logger::DEBUG
            ];
        }

        $this->pushHandler(new StreamHandler($app_config['logFile'], $app_config['logLevel']));
    }

    //Permet de récupérer l'instance du Singleton, car sinon celui ci est privé
    //Test si $app_config est vide ou null. Si c'est le cas, il crée une instance, sinon il renvoie l'instance existante, afin d'avoir qu'une seule et même instance pour les logs
    public static function getInstance($key = "app", $app_config = null)
    {
        if(empty(self::$log_sys[$key]))
        {
            self::$log_sys[$key] = new Logger($key, $app_config);
        }

        return self::$log_sys[$key];
    }

    //Permet de créer les fichiers de logs du projet, pour les erreurs et les requêtes
    public static function enableSystemLogs()
    {
        $LOG_PATH = App_config::get('LOG_PATH', __DIR__ . '/../../log-files');

        //Création du fichier de log pour les erreurs, et le place dans un dossier log-files qui est créer à la racine du projet
        self::$log_sys['error'] = new Logger('errors');
        self::$log_sys['error']->pushHandler(new StreamHandler("{$LOG_PATH}/errors.log"));
        ErrorHandler::register(self::$log_sys['error']);


        //Création du fichier de log pour les requêtes et le place dans un dossier log-files qui est créer à la racine du projet
        $data = [ 
            $_SERVER,
            $_REQUEST,
            trim(file_get_contents("php://input"))
        ];

        self::$log_sys['request'] = new Logger('request');
        self::$log_sys['request']->pushHandler(new StreamHandler("{$LOG_PATH}/request.log"));
        self::$log_sys['request']->info("REQUEST", $data);
    }
}