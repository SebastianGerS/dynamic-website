<?php
namespace Blog\Controllers;

class DefaultController extends AbstractController
{
    public function start()
    {
        header('location: /start/blogposts');
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
            'title' => 'skapa en blog post'
        ];

        return $this->render('views/createBlogpostPage.php', $properties);
    }

    public function createCommentPage():string 
    {   
        $params = $this->request->getParams();
        $blogpostId = $params->getInt('blogpost_id');

        $properties =[
            'title' => 'skapa en kommentar',
            'blogpostId' => $blogpostId, // this is set to perserve keep track of which blogpost the user is trying to comment
            'rootPage' => $params->getString('rootPage')  //this passes along the information about which page the user came from to get to the spicific blogpost
        ];

        return $this->render('views/createCommentPage.php', $properties);
    }

    
}