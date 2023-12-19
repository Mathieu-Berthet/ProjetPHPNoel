<?php 

namespace Mathieu\ProjetPhpNoel\Controller;

class Router
{
    public static function get($app_route, $app_callback)
    {
        if(strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0)
        {
            return;
        }

        self::on($app_route, $app_callback);
    }

    public static function post($app_route, $app_callback)
    {
        if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0)
        {
            return;
        }

        self::on($app_route, $app_callback);
    }

    public static function on($exprr, $callback)
    {
        $parameters = $_SERVER['REQUEST_URI'];
        $parameters = (stripos($parameters, "/") !== 0) ? "/" . $parameters : $parameters;

        $exprr = str_replace('/', '\/', $exprr);
        $matched = preg_match('/^' . ($exprr) . '$/', $parameters, $is_matched, PREG_OFFSET_CAPTURE);


        if($matched)
        {
            //Enlève un élément du tableau, ici, la route
            array_shift($is_matched);


            //On récupère les paramètres qui matches
            $parameters = array_map(function($parameter) {
                return $parameter[0];
            }, $is_matched);

            $callback(new Request($parameters), new Response());
        }
    }
}