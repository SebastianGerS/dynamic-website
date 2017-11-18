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

        if (empty($blogposts)) {
            $params = ['errorMessage' => 'sidan du letar efter finns inte'];
            return $this->render('views/error.php', $params);
        }
        
        $allBlogposts = $blogpostModel->getAllBlogposts();
      
        $morePages = $this->morePages($blogposts, $allBlogposts, $page, self::PAGE_LENGTH);
        
        $path = $this->request->getPath();

        $path = $this->pathProcessing($path);
        $nextPage = $path . '/' . ($page+1);
        $previusPage = $path . '/' . ($page-1);

        foreach($blogposts as $blogpost) {
        $blogpost->setTags(preg_replace('~,~',' ',$blogpost->getTags()));
        }
       
         $properties = [
            'blogposts' => $blogposts,
            'page' => $page,
            'morePages' => $morePages,
            'path' => substr($path,0,strlen($path)-1),
            'nextPage' => $nextPage,
            'previusPage' => $previusPage
        ];
        

        return $this->render('views/blogposts.php', $properties);
    }

    public function pathProcessing(string $path):string {

        $path = preg_replace('~\d+~','', $path);
        
        if (strrpos($path, '/') === (strlen($path)-1)) 
        {
            $path =substr($path, 0, strlen($path)-1);
        }

        return $path;
    }

    public function morePages(array $blogposts, array $allBlogposts, int $page , int $pageLength) {

        if (count($blogposts)*$page >= count($allBlogposts) || count($blogposts) < $pageLength) {

            return false;

        } else {

            return true;
        }
    }
    
    public function getAll():string
    {   
       
        return $this->getAllWithPage(1);
    }

    public function getBlogpost($id):string
    {   
        $blogpostModel = new BlogpostModel();
        
        $blogpost = $blogpostModel->getBlogpost($id);

        if (empty($blogpost)) {
            $params = ['errorMessage' => 'inlägged du letar efter finns inte'];
            return $this->render('views/error.php', $params);
        }

        $comments = $blogpostModel->getComments($id);

        
        $blogpost->setTags(preg_replace('~,~',' ',$blogpost->getTags()));
         
       
        $properties = [
            'blogpost' => $blogpost,
            'comments' => $comments
        ];

        return $this->render('views/blogpost.php', $properties);
    }

    public function getByUserWithPage($page): string {
        $page = (int)$page;
        $blogpostModel = new BlogpostModel();
      
        $blogposts = $blogpostModel->getByUserWithPage($this->user->getId(), $page, self::PAGE_LENGTH);
        
        if (empty($blogposts)) {
            $params = ['errorMessage' => 'sidan du letar efter finns inte'];
            return $this->render('views/error.php', $params);
        }
        $allBlogposts = $blogpostModel->getAllByUser($this->user->getId());
        $path = $this->request->getPath();
       
        
       
        $path = $this->pathProcessing($path);
        $nextPage = $path . '/' . ($page+1);
        $previusPage = $path . '/' . ($page-1);

        $morePages = $this->morePages($blogposts, $allBlogposts, $page, self::PAGE_LENGTH);
       
        foreach($blogposts as $blogpost) {
            $blogpost->setTags(preg_replace('~,~',' ',$blogpost->getTags()));
         }

        $properties = [
            'blogposts' => $blogposts,
            'page' => $page,
            'morePages' => $morePages,
            'nextPage' => $nextPage,
            'previusPage' => $previusPage,
            'path' => substr($path,0,strlen($path)-1),

        ];

        return $this->render('views/blogposts.php', $properties);
    }
    public function getByUser(): string {
       
        return $this->getByUserWithPage(1);   
    }

    public function insertBlogPostToDb() {
   
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
        $tags = $this->tagProcessing($params->getString('tagname'));
     
        $blogpostModel = new BlogpostModel();
     
        $blogpostModel->insertBlogPostToDb($this->user->getId(), $postName, $content, $tags);

     
        header("Location: /start/logedin/my-blogposts");
    }

    public function tagProcessing(string $tags):array {

        $tags = explode(" ", trim($tags));
        $tags = array_unique($tags);
        
        for($i = 0; $i < count($tags); $i++) {
          
            if (strlen($tags[$i]) === 0) {
                unset($tags[$i]);
            }
        }

        return $tags;
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
        $tags = $this->tagProcessing($params->getString('tagname'));
        $blogpostModel = new BlogpostModel();
        
        $blogpostModel->editBlogpost($blogpostId, $postName, $tags, $content);
        header("Location: /start/blogpost/" . $blogpostId);

    }

    public function blogpostEditPage():string 
    {   
        $params = $this->request->getParams();
        $blogpostId = $params->getInt('blogpost_id');
      
        $blogpostModel = new BlogpostModel();
      
        $blogpost = $blogpostModel->getBlogpost($blogpostId);

        
        $blogpost->setTags(preg_replace('~,~',' ',$blogpost->getTags()));
         
        $properties =[
            'blogpost' => $blogpost
        ];
        
        return $this->render('views/blogpostEditPage.php', $properties);
    }

    public function search($page) {
       
        $page = (int) $page;

        $params = $this->request->getParams();
    
        if(!$params->has('search') && isset($_COOKIE['search'])) {
            $search = $_COOKIE['search'];
            $searchType = (int) $_COOKIE['search_type'];
          
        } else if($params->getString('search') === "") {
            $params = ['errorMessage' => 'skriv in den taggen du vill söka efter'];
            setcookie("search_type", null, time() -3600);
            setcookie("search", null, time() -3600);
            return $this->render('views/blogposts.php', $params);
          
        } else {
            $search = $params->getString('search');
            $searchType = null;
        }
      
        $blogpostModel = new BlogpostModel();

        if ($params->has('tags') && $params->has('post_name') && $params->has('content') || $searchType === 6) {
            $blogposts = $blogpostModel->searchByTagsPostAndContent($search, $page, self::PAGE_LENGTH);
            $allBlogposts = $blogpostModel->searchByTagsPostAndContent($search);
            $searchType = 6;
        } else if ($params->has('tags') && $params->has('post_name') || $searchType === 5) {
            $blogposts = $blogpostModel->searchByTagsAndPost($search,$page, self::PAGE_LENGTH);
            $allBlogposts = $blogpostModel->searchByTagsAndPost($search);
            $searchType = 5;
        } else if($params->has('post_name') && $params->has('content') || $searchType === 4) {
            $blogposts = $blogpostModel->searchByPostAndContent($search,$page, self::PAGE_LENGTH);
            $allBlogposts = $blogpostModel->searchByPostAndContent($search);
            $searchType = 4;
        } else if($params->has('tags') && $params->has('content') || $searchType === 3) {
            $blogposts = $blogpostModel->searchByTagsAndContent($search,$page, self::PAGE_LENGTH);
            $allBlogposts = $blogpostModel->searchByTagsAndContent($search);
            $searchType = 3;
        } else if ($params->has('tags') || $searchType === 2) {
            $blogposts = $blogpostModel->searchByTags($search, $page, self::PAGE_LENGTH);
            $allBlogposts = $blogpostModel->searchByTags($search);
            $searchType = 2;
        } else if($params->has('post_name') || $searchType === 1) {
            $blogposts = $blogpostModel->searchByPost($search,$page, self::PAGE_LENGTH);
            $allBlogposts = $blogpostModel->searchByPost($search);
            $searchType = 1;
        } else if($params->has('content') || $searchType === 0) {
            $blogposts = $blogpostModel->searchByContent($search,$page, self::PAGE_LENGTH);
            $allBlogposts = $blogpostModel->searchByContent($search);
            $searchType = 0;
        } else  {
            $params = ['errorMessage' => 'du måste välja vad du vill söka efter'];
            return $this->render('views/blogposts.php', $params);
        }
      
       if (empty($blogposts)) {
        $params = ['errorMessage' => 'inga sökningar med angivna parametrar hittades'];
        setcookie("search_type", null, time() -3600);
        setcookie("search", null, time() -3600);
        return $this->render('views/blogposts.php', $params);
       }

        foreach($blogposts as $blogpost) {
            $blogpost->setTags(preg_replace('~,~',' ',$blogpost->getTags()));
        }

        $path = $this->request->getPath();
       
        $path = $this->pathProcessing($path);

        $nextPage = $path . '/' . ($page+1);

        $previusPage = $path . '/' . ($page-1);
     
        $morePages = $this->morePages($blogposts, $allBlogposts, $page, self::PAGE_LENGTH);
        

        if(strpos($path,"search") !== false) {
            $path = "/start/blogpost";
        }
      
        $properties =[
            'blogposts' => $blogposts,
            'morePages' => $morePages,
            'nextPage' => $nextPage,
            'previusPage' => $previusPage,
            'page' => $page,
            'path' => $path

        ];
        setcookie("search_type", $searchType, time() + 3600);
        setcookie("search", $search, time() + 3600);
        return $this->render('views/blogposts.php', $properties);
    }

    public function deletePostFromDb() 
    {
       
        $params = $this->request->getParams();
        $blogpostId = $params->getInt('blogpost_id');
       
    
        $blogpostModel = new BlogpostModel();
       
        $blogposts = $blogpostModel->deletePostFromDb($blogpostId);

        header("Location: start/logedin/blogposts");
       
    }

    public function deleteCommentFromDb() 
    {
        $params = $this->request->getParams();
        $commentId = $params->getInt('comment_id');
        $blogpostId = $params->getInt('blogpost_id');
       
    
        $blogpostModel = new BlogpostModel();
       
        $blogposts = $blogpostModel->deleteCommentFromDb($commentId);

        header("Location: start/blogpost/$blogpostId");
    }

    public function commentEditPage():string 
    {   
        
        $params = $this->request->getParams();
        $commentId = $params->getInt('comment_id');
       
        $blogpostModel = new BlogpostModel();
        
        $comment = $blogpostModel->getComment($commentId);
        $comment = $comment[0];
        $properties =[
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

        header("Location: /start/blogpost/$blogpostId");

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
    
        $blogpostModel->insertCommentToDb($this->user->getId(), $blogpostId, $content);
        header("Location: /start/blogpost/$blogpostId");
    }

}