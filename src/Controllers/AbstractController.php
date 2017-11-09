<?php
namespace Blog\Controllers;

use Blog\Core\Request;

abstract class AbstractController
{
    protected $request;
    protected $view;
    protected $userId;
    protected $coockie;

    public function __construct(Request $request)
    {   
        $this->request = $request;
        
        $this->coockie = $this->request->getCookies();
    }

    public function setUserId(int $userId) 
    {
        $this->userId = $userId;
    }

    public function unsetUserId() 
    {
        $this->userId = null;
    }

    protected function render(string $template, array $properties): string 
    {
        extract($properties);

        ob_start();
        include $template;
        $renderedView = ob_get_clean();

        return $renderedView;
    }
}