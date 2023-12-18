<?php

require __DIR__ . '/vendor/autoload.php';

//Vérifier le chemin du fichier de log
/*use Mathieu\ProjetPhpNoel\Controller\App_config;

$LOG_PATH = App_config::get('LOG_PATH', '');

echo "[LOG_PATH]: $LOG_PATH";*/



//Mise en place des fichier de logs
/*use Mathieu\ProjetPhpNoel\Controller\Logger;

Logger::enableSystemLogs();
$log_msg = Logger::getInstance();
$log_msg->info('Hello World');*/


// Appel d'une seule fonction, qui lancera l'appel à tous les autres
use Mathieu\ProjetPhpNoel\Controller\App;
App::run();