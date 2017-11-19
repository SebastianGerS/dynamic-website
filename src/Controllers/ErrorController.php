<?php

namespace Blog\Controllers;

class ErrorController extends AbstractController
{
    public function notFound()
    {
        $properties = ['errorMessage' => 'Sidan du letar efter finns inte'];

        return $this->render('views/error.php', $properties); 
        
    }
}