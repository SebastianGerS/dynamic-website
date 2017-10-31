<?php

namespace Blog\Controllers;

use Blog\Models\UserModel;

class UserController extends AbstractController
{
    public function getAll(): string
    {
        $userModel = new UserModel();

        $users = $userModel->getAll();

        $properties = [
            'customers' => $customers
        ];
        return $this->render('views/customers.php')
    }

    public function get(int $userId): string
    {
        $userModel = new userModel();

        try {
            $user = $customerModel->get($customerId);
        } catch (\Exception $e) {
            $properties = ['errorMessage' => 'Customer not found!'];
            return $this->render('views\customer.php', $properties);
        }

        $properties = ['customer' => $customer];
        return $this->render('views/customer.php',$properties);
    }


}
