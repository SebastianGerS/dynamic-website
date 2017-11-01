<?php
namespace Blog\Controllers;

use Blog\Models\BlogpostModel;

class BlogpostsController extends AbstractController 
{
    public function getAllBlogposts():string
    {   
       
        $blogpostModel = new BlogpostModel();
        
        $blogposts = $blogpostModel->getAllBlogposts();
       
        $properties = [
            'blogposts' => $blogposts
        ];

        return $this->render('views/blogposts.php', $properties);
    }
}