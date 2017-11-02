<?php

namespace Blog\Domain\User;

use Blog\Domain\User;
use Blog\Domain\User\Admin;
use Blog\Domain\User\BasicUser;

class UserFactory 
{
    public static function factory($type, $id, $firstname, $surename, $username, $password, $email) {
     
        $classname =  __NAMESPACE__ . '\\' . ucfirst($type);
        if (!class_exists($classname)) {
          
            throw new Exception('Wrong type.');
        }
        
        return new $classname ($type, $id, $firstname, $surename, $username, $password, $email);
    }
   
}