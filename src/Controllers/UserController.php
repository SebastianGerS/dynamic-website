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
     
        setcookie('user', $user->getId());
       
        $newController = new BlogpostsController($this->request);
        return $newController->getAll(); 
    }

    public function getAll(): string
    {
        $userModel = new UserModel();

        $users = $userModel->getAll();

        $properties = [
            'customers' => $user
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

        $properties = ['customer' => $user];
        return $this->render('views/user.php',$properties);
    }


}
