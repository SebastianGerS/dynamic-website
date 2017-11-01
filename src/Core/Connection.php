<?php

namespace Blog\Core;

use \PDO;
use Blog\Utils\Singelton;
use Blog\Core\Config;

class Connection extends Singelton 
{
    public $handler;
    protected function __construct() {

        try {
            $config = Config::getInstance()->get("db");
            $this->handler = new PDO($config["dsn"],$config["username"],$config["password"]);
            $this->handler->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    }

}