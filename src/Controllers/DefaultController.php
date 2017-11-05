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

    public function blogpostEditPage():string 
    {   
        
        $link = $this->request->getParams();
        var_dump($link);
        var_dump("hej hej");
        die;
        
        $properties =[
            'title' => 'Här kan du editera dina post'

        ];

        return $this->render('views/blogpostEditPage.php', $properties);
    }
}