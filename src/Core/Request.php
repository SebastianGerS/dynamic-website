<?php

namespace Blog\Core;
use Blog\Core\FilterdMap;

class Request {

    const GET = 'GET';
    const POST = 'POST';


    private $domain;
    private $path;
    private $method;
    private $params;
    private $cookies;

    public function __construct() {
        $this->domain = $_SERVER['HTTP_HOST'];// contains the host adress like localhost:1234 or http://sebastiangerstelsollerman.chas.academy
        $this->path = explode('?', $_SERVER['REQUEST_URI'])[0]; // path is set to be everyting after the host adress untill the first ? (the fist get parameter)
        $this->method = $_SERVER['REQUEST_METHOD']; // sets method to be eithe get or post depending on which method was used
        $this->params = new FilterdMap(array_merge($_POST, $_GET)); // creates a new instance of filterd map storing the information from post post and get requests(or from just on od them if just one was made which is usaly the case)
        $this->cookies = new FilterdMap($_COOKIE); //creates a new instance of filterdmap contaning all cookies and stores it in the cookie variable 
    } 

    public function getUrl(): string 
    {
        return $this->domain . $this->path;
    }

    public function getDomain(): string 
    {
        return $this->domain;
    }

    public function getPath(): string 
    {
        return $this->path;
    }

    public function getMethod():string 
    {
        return $this->method;
    }

    public function getParams(): FilterdMap 
    {
        return $this->params;
    }

    public function getCookies():FilterdMap 
    {
        return $this->cookies;
    }

    public function isPost(): bool 
    {
        return $this->method === self::POST;
    }

    public function isGet(): bool 
    {
        return $this->method === self::GET;
    }
}

