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

    public function createBlogpostPage():string 
    {   
        $properties =[
            'title' => 'en blog post'

        ];

        return $this->render('views/createBlogpostPage.php', $properties);
    }

    
}