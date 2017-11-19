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
    } // when a new instance of this object is caled the value of the key "db" is stored in the config varible
    //then the we store a new pdo object in the handler variable (the pdo object takes 3 arguments 
    //dsn adress to the database, username and password for the database, 
    //which all are provided by looking in the saved value  in the config variable)
    // the default way to fetch a result from a query is set to be an associative array

}