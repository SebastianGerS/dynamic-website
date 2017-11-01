<?php
namespace Blog\Controllers;

class DefaultController extends AbstractController
{
    public function start(): string
    {
        
;        $properties = [
            'title' => 'Blog bloggen'
        ];
      

        return $this->render('views/start.php', $properties);
    }

    public function createUserPage():string 
    {   
        $properties =[
            'title' => 'skapa en användare'

        ];

        return $this->render('views/createUserPage.php', $properties);
    }
}