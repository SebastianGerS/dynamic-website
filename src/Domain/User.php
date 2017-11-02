<?php
namespace Blog\Domain;

class User
{   
    protected $type;
    protected $id;
    protected $firstname;
    protected $surename;
    protected $username;
    protected $password;
    protected $email;
    

    public function __construct($type, $id, $firstname, $surename, $username, $password, $email) {
        $this->type = type;
        $this->id = $id;
        $this->firstname = $firstname;
        $this->surename = $surename;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;  
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

    public function getSurename() {
        return $this->surename;
    }
   
    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getEmail() {
        return $this->email;
    }
}