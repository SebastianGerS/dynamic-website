<?php
namespace Blog\Domain;

class User
{   
    protected $type;
    protected $id;
    protected $firstname;
    protected $surname;
    protected $username;
    protected $password;
    protected $email;
    

    public function __construct($type, $id, $firstname, $surname, $username, $password, $email) {
        $this->type = $type;
        $this->id = $id;
        $this->firstname = $firstname;
        $this->surname = $surname;
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

    public function getSurname() {
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