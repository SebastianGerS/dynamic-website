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
            'blogposts' => $blogposts
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
}