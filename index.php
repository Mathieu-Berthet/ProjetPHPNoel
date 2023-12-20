<?php
require __DIR__ . '/vendor/autoload.php';

use Mathieu\ProjetPhpNoel\Controller\App;
use Mathieu\ProjetPhpNoel\Controller\Router;
use Mathieu\ProjetPhpNoel\Controller\Request;
use Mathieu\ProjetPhpNoel\Controller\Response;
use Mathieu\ProjetPhpNoel\Model\Posts;

use mikehaertl\pdftk\Pdf;

Posts::load();

Router::get('/post', function (Request $request, Response $response) {

    $pdf = new Pdf(__DIR__ . '/PDF/cerfaParticulier.pdf');


    //CAC1 => True pour les case à cocher
    //1 seule case coché possible
    $result = $pdf->fillForm([
        'a1' => 1,
        'a2' => "SPA",
        /*'a3' => "",
        'a4' => 14579856234,
        'a6' => 26,
        'a7' => "Rue du don",
        'a8' => 26000,
        'a9' => "Valence",
        'a10' => "France",
        'a11' => "Protection des animaux",
        'a12' => "2000-05-25",
        'a19' => "15",
        'a20' => "Rue du donateur",
        'a21' => "26000",
        'a22' => "Valence",
        'a23' => 15,
        'a24' => "Quinze",
        'a25' => "",
        'a26' => "",
        'a27' => 15,
        'a28' => "Quinze",
        'a29' => "",
        'a30' => 15,
        'a31' => "Quinze",
        'a32' => "",
        'a33' => "14/12/2022",
        'Dénomination' => "test",
        'CAC1' => true,
        'CAC2' => true,
        'CAC15' => true,*/

    ])
    ->needAppearances()
    ->saveAs(__DIR__ . '/PDF/filled.pdf');

    $nameBase64 = base64_encode(__DIR__ . '/PDF/filled.pdf');
    var_dump($nameBase64);

//Test unitaire pour la génération d'un pdf en base64


    // Always check for errors
    if ($result === false) {
        $error = $pdf->getError();
    }

    // Get form data fields
    /*$pdf2 = new Pdf(__DIR__ . '/PDF/filled.pdf');
    $data = $pdf2->getData();
    if ($data === false) {
        $error = $pdf2->getError();
    }

    
    $txt = (string) $data;
    $txt = $data->__toString();

    //echo $txt;
    var_dump($txt);*/

    $response->toJSON(Posts::all());
});

Router::post('/post', function (Request $request, Response $response) {
    $b_post = Posts::add($request->getJSON());
    $response->p_status(201)->toJSON($b_post);
});

Router::get('/post/([0-9]*)', function (Request $request, Response $response) {
    $b_post = Posts::findById($request->parameters[0]);
    if ($b_post) {
        $response->toJSON($b_post);
    } else {
        $response->p_status(404)->toJSON(['error' => "Not Found"]);
    }
});

App::run();