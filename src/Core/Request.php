<?php

namespace Blog\Core;

class Request {

    const GET = 'GET';
    const POST = 'POST';


    private $domain;
    private $path;
    private $method;
    private $params;
    private $cookies;

    public function __construct() {
        $this->domain = $_SERVER['HTTP_HOST'];
        $this->path = explode('?', $_SERVER['REQUEST_URI'])[0];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->params = new filterdMap(array_merge($_POST, $_GET));
        $this->cookies = new FilterdMap($_COOKIE);
    }

    public function getUrl(): string {
        return $this->domain . $this->path;
    }

    public function getDomain(): string {
        return $this->domain;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getMethod():string {
        return $this->method;
    }

    public function getParams(): FilterdMap {
        return $this->params;
    }

    public function getCookies():FilterdMap {
        return $this->cookies;
    }
    public function isPost(): bool {
        return $this->method === self::POST;
    }

    public function isGet(): bool {
        return $this->method === self::GET;
    }

    

}

