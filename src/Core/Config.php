<?php

namespace Blog\Core;

use Blog\Utils\Singelton; 

class Config extends Singelton 
{
    private $data;
    private static $instance;

    protected function __construct()
    {
        $json =file_get_contents(__DIR__ . '/../../config/dbinfo.json');
        $this->data = json_decode($json, true);
    } // when a new instance of this function is caled the content of dbinfo.json is turnd into a string and stored in the data varible 

    public function get($key) 
    {
        if(!isset($this->data[$key])) {

            throw new Exception('Key ' . $key .'not found in file');
        }
        
        return $this->data[$key];
    } // this function returns the value of a given key if that key exists in the data varible
}
?>