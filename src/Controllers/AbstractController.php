<?php
namespace Blog\Controllers;

use Blog\Core\Request;
use Blog\Domain\User;
use Blog\Models\BlogpostModel;
abstract class AbstractController
{
    protected $request;
    protected $view;
    protected $user;

    public function __construct(Request $request)
    {   
       
        $this->request = $request;   
       
    }

    public function setUser($user) 
    {
        $this->user = new User(
            $user->type,
            $user->id,
            $user->firstname,
            $user->surname,
            $user->username,
            $user->email
        );
    }

    public function unsetUser() 
    {
        $this->user = null;
    }

    protected function render(string $view, array $properties): string 
    {
        
        $user = json_decode($this->request->getCookies()->get('user'));
        $blogpostModel = new BlogpostModel();
        $toptags = $blogpostModel->topTags();
        $id = 1;
        foreach($toptags as $tag) {
            $properties["toptag$id"] = $tag["tagname"];
            $id++;
        }
        $properties['back'] = $_SERVER['HTTP_REFERER'];
        $properties['user'] =  $user;
      
        extract($properties);

        ob_start();
        include_once ($_SERVER['DOCUMENT_ROOT'] . "/templates/head.html");
        include_once ( $_SERVER['DOCUMENT_ROOT'] . "/views/header.php");
            include_once $view;
        include_once ($_SERVER['DOCUMENT_ROOT'] . "/templates/footer.html");
        $renderedView = ob_get_clean();


        return $renderedView;
    }
}