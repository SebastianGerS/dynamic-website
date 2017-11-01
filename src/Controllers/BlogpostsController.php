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
            'blogposts' => $blogposts
        ];

        return $this->render('views/blogposts.php', $properties);
    }
    
    public function getAll():string
    {   
        return $this->getAllWithPage(1);
    }
}