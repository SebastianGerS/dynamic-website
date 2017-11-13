<?php
namespace Blog\Controllers;

use Blog\Core\Request;
use Blog\Domain\User;

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

    protected function render(string $template, array $properties): string 
    {
        extract($properties);

        ob_start();
        include ($_SERVER['DOCUMENT_ROOT'] . "/templates/head.html");
        include ( $_SERVER['DOCUMENT_ROOT'] . "/views/header.php");
        include $template;
        include ($_SERVER['DOCUMENT_ROOT'] . "/templates/footer.html");

        $renderedView = ob_get_clean();

        return $renderedView;
    }
}