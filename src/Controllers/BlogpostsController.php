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
    
        $blogposts = $blogpostModel->getBlogpostsByPage($page, self::PAGE_LENGTH);
       
        $allBlogposts = $blogpostModel->getAllBlogposts();
     
        $morePages = true;

        if(count($blogposts)*$page >= count($allBlogposts) || count($blogposts) < self::PAGE_LENGTH)
        {
            $morePages = false;
        } 
        
        $coockie = $this->coockie->getInt("user");
        $properties = [
            'blogposts' => $blogposts,
            'userId' => $coockie,
            'page' => $page,
            'morePages' => $morePages
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
        
        $tags = $blogpostModel->getTagsformPost($id);
        $blogposts = $blogpostModel->getBlogpost($id);
        $comments = $blogpostModel->getComments($id);
        $coockie = $this->coockie->getInt("user");
        $properties = [
            'blogposts' => $blogposts,
            'tags'=> $tags,
            'comments' => $comments,
            'userId' => $coockie
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
       
        header("Location: /start/logedin/1");
    }

    public function editPostInDatabase() {

      
        $params = $this->request->getParams();

        if (!$params->has('post_name')) {
            $params = ['errorMessage' => 'Du får ändra title, men du kan inte ta bort titeln helt'];
            return $this->render('views/createBlogpostPage.php', $params);
        } else if (!$params->has('tagname')) {
            $params = ['errorMessage' => 'Du får ändra och lägga till taggar, men du måste ha minst en tag'];
            return $this->render('views/createBlogpostPage.php', $params);
        } else if (!$params->has('content')) {
            $params = ['errorMessage' => 'Du får ändra innehållet, men du kan inte ta bort allt innehåll, önskar du ta bort inlägget var god och använd tabort knappen istället'];
            return $this->render('views/createBlogpostPage.php', $params);
        }
        $blogpostId = $params->getInt('blogpost_id');
        $postName = $params->getString('post_name');
        $content = $params->getString('content');
        $tags = explode(" ", $params->getString('tagname'));
        $blogpostModel = new BlogpostModel();
        
        $blogpostModel->editBlogpost($blogpostId, $postName, $tags, $content);
        header("Location: /blogpost/" . $blogpostId);

    }

    public function blogpostEditPage():string 
    {   
        $params = $this->request->getParams();
        $blogpostId = $params->getInt('blogpost_id');
       
        $blogpostModel = new BlogpostModel();
        $tags = $blogpostModel->getTagsformPost($blogpostId);
      
        $blogposts = $blogpostModel->getBlogpost($blogpostId);
        $properties =[
            'title' => 'Här kan du editera dina post',
            'blogposts' => $blogposts,
            'tags'=> $tags

        ];
        
        return $this->render('views/blogpostEditPage.php', $properties);
    }

    public function searchByTagName() {
       

        $params = $this->request->getParams();

        if (!$params->has('tagname')) {
            $params = ['errorMessage' => 'skriv in den taggen du vill söka efter'];
            return $this->render('views/blogposts/.php', $params);
        }

        $tagname = $params->getString('tagname');  

        $blogpostModel = new BlogpostModel();
        
        $blogposts = $blogpostModel->searchByTagName($tagname);
       if (empty($blogposts)) {
        $params = ['errorMessage' => 'inga sökningar med angivna parametrar hittades'];
        return $this->render('views/blogposts.php', $params);
       }
      
        $properties =[
            'title' => 'Här kan du editera dina post',
            'blogposts' => $blogposts
        ];
        return $this->render('views/blogposts.php', $properties);
    }

    public function deletePostFromDb() 
    {
       
        $params = $this->request->getParams();
        $blogpostId = $params->getInt('blogpost_id');
       
    
        $blogpostModel = new BlogpostModel();
       
        $blogposts = $blogpostModel->deletePostFromDb($blogpostId);
        header("Location: start/logedin/1");
       
    }

    public function deleteCommentFromDb() 
    {
        $params = $this->request->getParams();
        $commentId = $params->getInt('comment_id');
        $blogpostId = $params->getInt('blogpost_id');
       
    
        $blogpostModel = new BlogpostModel();
       
        $blogposts = $blogpostModel->deleteCommentFromDb($commentId);

        header("Location: blogpost/$blogpostId");
    }

    public function commentEditPage():string 
    {   
        
        $params = $this->request->getParams();
        $commentId = $params->getInt('comment_id');
       
        $blogpostModel = new BlogpostModel();
        
        $comment = $blogpostModel->getComment($commentId);
        $comment = $comment[0];
        $properties =[
            'title' => 'Här kan du editera dina post',
            'comment' => $comment

        ];
       
        return $this->render('views/commentEditPage.php', $properties);
    }

    public function editCommentInDatabase() {
        
        
        $params = $this->request->getParams();

        
        if (!$params->has('content')) {
            $params = ['errorMessage' => 'Du får ändra innehållet, men du kan inte ta bort allt innehåll, önskar du ta bort kommentaren var god och använd tabort knappen istället'];
            return $this->render('views/commentEditPage.php', $params);
        }
        $commentId = $params->getInt('comment_id');
        $content = $params->getString('content');
        $blogpostId = $params->getInt('blogpost_id');
        $blogpostModel = new BlogpostModel();
        
        $blogpostModel->editComment($commentId, $content);

        header("Location: /blogpost/$blogpostId");

    }

    public function insertCommentToDb() {
        
        $params = $this->request->getParams();
        
       
        if (!$params->has('content')) {
            $params = ['errorMessage' => 'Ditt inlägg måste ha innehåll för att kunna skapas'];
            return $this->render('views/createCommentPage.php', $params);
        }

        
        $content = $params->getString('content');
        $blogpostId = $params->getInt('blogpost_id');
     
        $blogpostModel = new BlogpostModel();
       
        $blogpostModel->insertCommentToDb($this->userId, $blogpostId, $content);
      
        header("Location: /blogpost/$blogpostId");
    }

}