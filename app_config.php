<?php

//Chemin du dossier comprenant les fichiers de log, le fichier json servant à remplir les pdf, et les chemins vers les fichiers pdf vide

return [
    'LOG_PATH' => __DIR__ . '/log-files',
    'DB_PATH' => __DIR__ . '/db.json',
    'CERFA_ENTREPRISE_PATH' => __DIR__ . '/PDF/cerfaEntreprise.pdf',
    'CERFA_PARTICULIER_PATH' => __DIR__ . '/PDF/cerfaParticulier.pdf'
];