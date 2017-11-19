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
    } // sets request to provided request

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
    } //function that sets a user to a new user object from provided user info

    public function unsetUser() 
    {
        $this->user = null;
    } // sets user to null

    protected function render(string $view, array $properties): string 
    {
        
        $user = json_decode($this->request->getCookies()->get('user')); //sets user varible within the function scoope to user saved in coockie
        
        $blogpostModel = new BlogpostModel(); 
        $toptags = $blogpostModel->topTags(); // saves an array of the curently most popular tags to a variable

        $id = 1;

        foreach($toptags as $tag) {

            $properties["toptag$id"] = $tag["tagname"];
            $id++;
        } // iterates over the toptags and stores each of the array elements tagnames in properties 

        $properties['back'] = $_SERVER['HTTP_REFERER']; // sets the variable to the previous page url of the previous path 
        $properties['user'] =  $user; // stores curent user in properties
      
        extract($properties); // extract the properties to make the avalible in curent view (and in included templates)

        ob_start();
        include_once ($_SERVER['DOCUMENT_ROOT'] . "/templates/head.html");
        include_once ( $_SERVER['DOCUMENT_ROOT'] . "/templates/header.php");
            include_once $view;
        include_once ($_SERVER['DOCUMENT_ROOT'] . "/templates/footer.html");
        $renderedView = ob_get_clean();

        return $renderedView;
    }
}