<?php
namespace Blog\Domain;
use \JsonSerializable;

class User implements JsonSerializable
{   
    protected $type;
    protected $id;
    protected $firstname;
    protected $surname;
    protected $username;
    protected $email;
    

    public function __construct($type, $id, $firstname, $surname, $username, $email) {
        $this->type = $type;
        $this->id = $id;
        $this->firstname = $firstname;
        $this->surname = $surname;
        $this->username = $username;
        $this->email = $email;  
    }

    public function jsonSerialize() 
    {
        return get_object_vars($this);
    }
    
    public function getType() {
        return $this->type;
    }

    public function getId() {
        return $this->id;
    }

    public function getFirstname() {
        return $this->firstname;
    }

    public function getSurname() {
        return $this->surename;
    }
   
    public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return $this->email;
    }
}