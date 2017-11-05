<?php
namespace Blog\Controllers;

use Blog\Models\BlogpostModel;

class BlogpostsController extends AbstractController 
{

    const PAGE_LENGTH = 5;

    public function getAllWithPage($page):string
    {   
       $page = (int)$page;
       $blogpostModel = new BlogpostModel();
        
        $blogposts = $blogpostModel->getAllBlogposts($page, self::PAGE_LENGTH);
        $properties = [
            'blogposts' => $blogposts,
            'userId' => $this->userId
        ];

        return $this->render('views/blogposts.php', $properties);
    }
    
    public function getAll():string
    {   
        return $this->getAllWithPage(1);
    }

    public function getBlogpost($id):string
    {   
        $blogpostModel = new BlogpostModel();
        $blogposts = $blogpostModel->getBlogpost($id);
        $properties = [
            'blogposts' => $blogposts,
            'userId' => $this->userId
        ];

        return $this->render('views/blogpost.php', $properties);
    }

    public function getByUserWithPage($page): string {
        $page = (int)$page;
        $blogpostModel = new BlogpostModel();
        $blogposts = $blogpostModel->getByUser($this->userId, $page, self::PAGE_LENGTH);
       
        $properties = [
            'blogposts' => $blogposts,

        ];

        return $this->render('views/blogposts.php', $properties);
    }
    public function getByUser(): string {
       
        return $this->getByUserWithPage(1);   
    }

    public function insertBlogPostToDb() {

        if(!$this->request->isPost()) 
        {   
            
            return $this->render('views/createBlogpostPage.php');
        }
        

        $params = $this->request->getParams();
      
        if (!$params->has('post_name')) {
            $params = ['errorMessage' => 'Du måste ge dit inlägg en titel'];
            return $this->render('views/createBlogpostPage.php', $params);
        } else if (!$params->has('tagname')) {
            $params = ['errorMessage' => 'Du måste ge dit inlägg minst en tag'];
            return $this->render('views/createBlogpostPage.php', $params);
        } else if (!$params->has('content')) {
            $params = ['errorMessage' => 'Ditt inlägg måste ha innehåll för att kunna skapas'];
            return $this->render('views/createBlogpostPage.php', $params);
        }

      
        
        $postName = $params->getString('post_name');
        $content = $params->getString('content');
        $tags = explode(" ", $params->getString('tagname'));
        
        $blogpostModel = new BlogpostModel();
       
        $blogpostModel->insertBlogPostToDb($this->userId, $postName, $content, $tags);
       
        header("Location: /start/logedin");
    }

    public function editBlogpost() {
        $blogpostModel = new BlogpostModel();
        $blogpostModel->editBlogpost($this->userId, $postName, $content, $tags);
    }
}