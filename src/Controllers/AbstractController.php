<?php
namespace Blog\Controllers;

use Blog\Core\Request;

abstract class AbstractController
{
    protected $request;
    protected $view;
    protected $userId;

    public function __construct(Request $request)
    {   
        $this->request = $request;
    }

    public function setUserId(int $userId) 
    {
        $this->userId = $userId;
    }

    protected function render(string $template, array $params): string 
    {
        extract($params);

        ob_start();
        include $template;
        $renderedView = ob_get_clean();

        return $renderedView;
    }
}