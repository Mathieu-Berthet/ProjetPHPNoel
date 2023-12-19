<?php 

namespace Mathieu\ProjetPhpNoel\Controller;


class Request
{
    public $parameters;
    public $req_method;
    public $content_type;

    public function __construct($parameters = [])
    {
        $this->parameters = $parameters;
        $this->req_method = trim($_SERVER['REQUEST_METHOD']);
        $this->content_type = !empty($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    }


    public function getBody()
    {
        if($this->req_method !== 'POST')
        {
            return '';
        }

        $post_body = [];
        foreach($_POST as $key => $value)
        {
            $post_body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $post_body;
    }


    public function getJSON()
    {
        if($this->req_method !== 'POST')
        {
            return [];
        }

        if(strcasecmp($this->content_type, 'application/json') !== 0)
        {
            return [];
        }


        $post_content = trim(file_get_contents("php://input"));

        $p_decoded = json_decode($post_content);

        return $p_decoded;
    }
}