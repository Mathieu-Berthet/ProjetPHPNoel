<?php
require __DIR__ . '/vendor/autoload.php';

use Mathieu\ProjetPhpNoel\Controller\App;
use Mathieu\ProjetPhpNoel\Controller\Router;
use Mathieu\ProjetPhpNoel\Controller\Request;
use Mathieu\ProjetPhpNoel\Controller\Response;
use Mathieu\ProjetPhpNoel\Controller\GeneratorPDF;
use Mathieu\ProjetPhpNoel\Model\Posts;

use mikehaertl\pdftk\Pdf;

Posts::load();

Router::get('/post', function (Request $request, Response $response) {

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