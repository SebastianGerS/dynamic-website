<?php

namespace Blog\Models;
use Blog\Utils\Connection;


abstract class AbstractModel 
{
    protected $db;
    public function __construct() 
    {
        $this->db = Connection::getInstance()->handler;
    }
}


?>