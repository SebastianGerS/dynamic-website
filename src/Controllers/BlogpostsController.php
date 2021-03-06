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
            $properties = ['errorMessage' => 'sidan du letar efter finns inte'];
            return $this->render('views/error.php', $properties);
        }
        
        $allBlogposts = $blogpostModel->getAllBlogposts();
        
        $morePages = $this->morePages($blogposts, $allBlogposts, $page, self::PAGE_LENGTH);
        
        $path = $this->request->getPath();

        $path = $this->pathProcessing($path);

        $nextPage = $path . '/' . ($page+1); 
        $previusPage = $path . '/' . ($page-1);
       //dynamicly sets nextPage and previousPage by concatenating curent path to with curent page added/subtracted with one

         $properties = [
            'blogposts' => $blogposts,
            'page' => $page,
            'morePages' => $morePages,
            'path' => substr($path,0,strlen($path)-1), //becouse this path will be used to get to a spisific blogpost we dont want the "s" in the end of the path
            'nextPage' => $nextPage,
            'previusPage' => $previusPage
        ];
        

        return $this->render('views/blogposts.php', $properties);
    }

    public function pathProcessing(string $path):string 
    {
        $path = preg_replace('~\d+~','', $path);
        
        if (strrpos($path, '/') === (strlen($path)-1)) {

            $path =substr($path, 0, strlen($path)-1);
        }

        return $path;
    } /* this function takes a argument that in controller will be the current path 
        and removes anny numbers in the path and then checks if therse a "/" in the last position of the $path if so, it's removed */

    public function morePages(array $blogposts, array $allBlogposts, int $page , int $pageLength) 
    {

        if (count($blogposts)*$page >= count($allBlogposts) || count($blogposts) < $pageLength) {

            return false;

        } else {

            return true;
        }
    } /* this function checks if the length of first array (blogposts) times the curent page
     is less or equal to the the length of the second array(allblogposts) or if the first array is less then the length of the page
     this is to determain wheter or not there are sufficient bloggposts to render morepages*/
    
    public function getAll():string
    {   
        return $this->getAllWithPage(1);
    }

    public function getBlogpost($id):string
    {   
        $params = $this->request->getParams();

        $blogpostModel = new BlogpostModel();
        
        $blogpost = $blogpostModel->getBlogpost($id);

        if (empty($blogpost)) {
            $properties = ['errorMessage' => 'inlägged du letar efter finns inte'];
            return $this->render('views/error.php', $properties);
        }

        $comments = $blogpostModel->getComments($id);
        

        $properties = [
            'blogpost' => $blogpost,
            'comments' => $comments
        ];

        if ($params->has('rootPage')) {
            $properties['rootPage'] = $params->getString('rootPage');
           
        } /*checks if the parameter rootPage isset if so it's pased allong to properties. 
          This parameter is used to preserve the path from which the user enterd the blogpost view 
          to make it possible to navigate back to the page before even if  subbpages has been viseted in between*/

        $properties['previousPage'] = $previusPage;

        return $this->render('views/blogpost.php', $properties);
    }

    public function getByUserWithPage($page): string {

        $page = (int)$page;
        $blogpostModel = new BlogpostModel();
      
        $blogposts = $blogpostModel->getByUserWithPage($this->user->getId(), $page, self::PAGE_LENGTH);
        
        if (empty($blogposts)) {
            $properties = ['errorMessage' => 'Du har inga blogposter än'];
            return $this->render('views/blogposts.php', $properties);
        }

        $allBlogposts = $blogpostModel->getAllByUser($this->user->getId());

        $path = $this->request->getPath();
        $path = $this->pathProcessing($path);
        $nextPage = $path . '/' . ($page+1);
        $previusPage = $path . '/' . ($page-1);

        $morePages = $this->morePages($blogposts, $allBlogposts, $page, self::PAGE_LENGTH);

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

    public function getByUser(): string 
    {   
        return $this->getByUserWithPage(1);   
    }

    public function insertBlogPostToDb() 
    {
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
        } //checkes that all nesesary infromation has been filed in by the user 

        $postName = $params->getString('post_name');
        $content = $params->getString('content');
        $tags = $this->tagProcessing($params->getString('tagname'));
     
        $blogpostModel = new BlogpostModel();
     
        $blogpostModel->insertBlogPostToDb($this->user->getId(), $postName, $content, $tags);

        header("Location: /start/logedin/my-blogposts");
    }

    public function tagProcessing(string $tags):array 
    {
        $tags = explode(" ", trim($tags));
        $tags = array_unique($tags);
        
        for($i = 0; $i < count($tags); $i++) {
          
            if (strlen($tags[$i]) === 0) {
                unset($tags[$i]);
            }
        }

        return $tags;
    } //this functions takes a string, trims enny whitespace before or after, explodes the string into an array, 
      //removes anny eventual duplicates in the array and removes enny tags that has a length of 0

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
        } //checkes that all nesecary info is filed in

        $blogpostId = $params->getInt('blogpost_id');
        $postName = $params->getString('post_name');
        $content = $params->getString('content');
        $tags = $this->tagProcessing($params->getString('tagname'));

        $blogpostModel = new BlogpostModel();
        $blogpostModel->editBlogpost($blogpostId, $postName, $tags, $content);

        header("Location: /start/blogpost/" . $blogpostId); // redirects to the blogpost view of the edited blogpost

    }

    public function blogpostEditPage():string 
    {   
        $params = $this->request->getParams();
        $blogpostId = $params->getInt('blogpost_id');
      
        $blogpostModel = new BlogpostModel();
        $blogpost = $blogpostModel->getBlogpost($blogpostId);
         
        $properties =[
            'blogpost' => $blogpost,
            'rootPage' => $params->getString('rootPage')
        ];
        
        return $this->render('views/blogpostEditPage.php', $properties);
    }

    public function search($page) 
    {   
        $page = (int) $page;

        $params = $this->request->getParams();
    
        if(!$params->has('search') && isset($_COOKIE['search'])) {

            $search = $_COOKIE['search'];
            $searchType = (int) $_COOKIE['search_type'];
            // if search is not set and the cookie search is set this means that this 
            //functions was triggerd by the user pushing either the next or the previouspages button
            // this meens that in stead of looking on the search field for what type of search it is 
            //or what to search for we need to look in the previusly set cookie 
        } else if($params->getString('search') === "") {

            $params = ['errorMessage' => 'skriv in den taggen du vill söka efter'];

            setcookie("search_type", null, time() -3600); 
            setcookie("search", null, time() -3600);
            //makes shure no informations from previus search is saved
            return $this->render('views/blogposts.php', $params);
          
        } else {

            $search = $params->getString('search');
            $searchType = null; //makes shure that the search type is set to null so that it whont interfear with this new search
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
        } // checks which params has been set or if theres a searchtype thats has been set and selects apropriet function in moddel to search with

      
       if (empty($blogposts)) {
        
        $params = ['errorMessage' => 'inga sökningar med angivna parametrar hittades'];
        setcookie("search_type", null, time() -3600);
        setcookie("search", null, time() -3600);
        return $this->render('views/blogposts.php', $params);
       } //if theres no search ressult a error messages is returned with the view

        $path = $this->request->getPath();
        $path = $this->pathProcessing($path);
        $nextPage = $path . '/' . ($page+1);
        $previusPage = $path . '/' . ($page-1);
        $morePages = $this->morePages($blogposts, $allBlogposts, $page, self::PAGE_LENGTH);
        
        $path = "/start/blogpost"; //resets the path neccesary becous it's used to set corect 
        //path to the spesific blogposts which differs alot from the search path
      
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
        //setts coockies to perserve information off the search incase there ar multiple pages in the search which then would need that information to render properly
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
            'comment' => $comment,
            'rootPage' => $params->getString('rootPage')

        ];
       
        return $this->render('views/commentEditPage.php', $properties);
    }

    public function editCommentInDatabase() 
    {    
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

    public function insertCommentToDb() 
    {
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