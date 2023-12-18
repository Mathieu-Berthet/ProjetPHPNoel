<?php

require __DIR__ . '/vendor/autoload.php';

//Vérifier le chemin du fichier de log
use Mathieu\ProjetPhpNoel\Controller\App_config;

$LOG_PATH = App_config::get('LOG_PATH', '');

echo "[LOG_PATH]: $LOG_PATH";