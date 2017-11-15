<?php

namespace Blog\Models;
use Blog\Core\Connection;


abstract class AbstractModel 
{
    protected $db;
    public function __construct() 
    {   
        $this->db = Connection::getInstance()->handler;
    }
}


?>