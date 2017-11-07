<?php

namespace Blog\Controllers;

use Blog\Models\UserModel;

class UserController extends AbstractController
{

    public function login(): string 
    {    
        if(!$this->request->isPost()) 
        {
            
            return $this->render('views/start.php');
        }

        $params = $this->request->getParams();
        
        if (!$params->has('username')) {
           
            $params = ['errorMessage' => 'Du måste fylla i dit användarnamn för att kuna logga in'];
            return $this->render('views/start.php', $params);
        } else if (!$params->has('password')) {
            
            $params = ['errorMessage' => 'Du måste fylla i ditt lösenor för att kunna logga in'];
            return $this->render('views/start.php', $params);
           
        }
        
        $username = $params->getString('username');
        $password = $params->getString('password');
       
        $userModel = new UserModel();
        try {
            $user = $userModel->getByUsername($username);
        } catch (Exception $e) {
            $params = ['errorMessage' => 'Fel användarnamn, försök igen'];
            return $this->render('views/start.php', $params);
        }

        
        if ($user->getPassword() !== $password) {
            $params = ['errorMessage' => 'Felaktigt lösenord'];
            return $this->render('views/start.php', $params);
        }
        
        setcookie('user', $user->getId(), time()+86400);
        header("Location: /start/logedin");
    }

    public function logout(): string 
    {
      
        $this->unsetUserId();
        setcookie('user', "", time() -3600);
        header("Location: /start");
    }

    public function getAll(): string
    {
        $userModel = new UserModel();

        $users = $userModel->getAll();

        $properties = [
            'users' => $user
        ];
        return $this->render('views/users.php');
    }

    public function get(int $userId): string
    {
        $userModel = new userModel();

        try {
            $user = $customerModel->get($customerId);
        } catch (\Exception $e) {
            $properties = ['errorMessage' => 'User not found!'];
            return $this->render('views\user.php', $properties);
        }

        $properties = ['user' => $user];
        return $this->render('views/user.php',$properties);
    }

    public function createUser() {

        if(!$this->request->isPost()) 
        {
            
            return $this->render('views/createUserPage.php');
        }

        $params = $this->request->getParams();
       

        if (!$params->has('firstname')) {
            $params = ['errorMessage' => 'Du måste fylla i dit förnamn'];
            return $this->render('views/createUserPage.php', $params);
        } else if (!$params->has('surname')) {
            $params = ['errorMessage' => 'Du måste fyll i ditt efternamn'];
            return $this->render('views/createUserPage.php', $params);
        } else if (!$params->has('email')) {
            $params = ['errorMessage' => 'Du måste fylla i en epost adress'];
            return $this->render('views/createUserPage.php', $params);
        } else if (!$params->has('username')) {
            $params = ['errorMessage' => 'Du måste välja ett användarnamn'];
            return $this->render('views/createUserPage.php', $params);
        } else if (!$params->has('password')) {
            $params = ['errorMessage' => 'Du måste välja ett lösenord'];
            return $this->render('views/createUserPage.php', $params);
        }

        $firstname = $params->getString('firstname');
        $surname = $params->getString('surname');
        $email = $params->getString('email');
        $username = $params->getString('username');
        $password = $params->getString('password');
        
        $userModel = new userModel();
        
        $userModel->addUser($firstname, $surname, $email, $username, $password);
       
        header("Location: /start");
    }


}
