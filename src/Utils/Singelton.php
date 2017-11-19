<?php

namespace Blog\Utils;

abstract class Singelton 
{
    private static $instances = array();

    public static function getInstance() 
    {   
        $class = get_called_class();
        
        if (!isset(self::$instances[$class])) {

            self::$instances[$class] = new static();
        }
        
        return self::$instances[$class];
    } // makes this function looks in the instances array to check if the class thats been caled is in that array
    // if not it creates a new instance of that class and stores it the array if it's alredy in the array it just 
    //returns the instances of that class stored in the array

}
?>