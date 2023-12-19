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
/*use Mathieu\ProjetPhpNoel\Controller\App;
App::run();*/


use Mathieu\ProjetPhpNoel\Controller\App;
use Mathieu\ProjetPhpNoel\Controller\Router;
use Mathieu\ProjetPhpNoel\Controller\Request;
use Mathieu\ProjetPhpNoel\Controller\Response;



Router::get('/', function() { echo 'Hello World'; });

Router::get('/post/([0-9]*)', function (Request $request, Response $response)
{
    $response->toJSON([
        'post' => ['id' => $request->parameters[0]],
        'status' => 'ok'
    ]);
});

App::run();