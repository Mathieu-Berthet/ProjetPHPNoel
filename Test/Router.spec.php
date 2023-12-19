<?php 

namespace Mathieu\ProjetPhpNoel\Test;

/*use Mathieu\ProjetPhpNoel\Controller\Router;
use function Eloquent\Phony\stub;

describe("Application\\Controller\\Router", function () {
    describe("->get", function () {

        it("match regex and execute the callback", function () {
            // Mock Request
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/post';

            $stub = stub(function () { });
            Router::get('/post', $stub);

            $stub->called();
        });


        it("shouldn't execute the callback if not GET request method", function () {
            // Mock Request
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_SERVER['REQUEST_URI'] = '/post';

            $stub = stub(function () { });
            Router::get('/post', $stub);

            expect($stub->checkCalled())->to->be->null();
        });

        it("match regex and get params", function () {
            // Mock Request
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/post/12';

            $stub = stub(function ($req) { });
            Router::get('/post/([0-9]*)', $stub);

            $stub->called();
            $request = $stub->firstCall()->argument();
            expect($request->parameters[0])->to->be->equal("12");
        });
    });

    describe("->post", function () {

        it("match regex and execute the callback", function () {
            // Mock Request
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_SERVER['REQUEST_URI'] = '/post';

            $stub = stub(function () { });
            Router::post('/post', $stub);

            $stub->called();
        });

        it("shouldn't execute the callback if not POST request method", function () {
            // Mock Request
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/post';

            $stub = stub(function () { });
            Router::post('/post', $stub);

            expect($stub->checkCalled())->to->be->null();
        });
    });
});*/