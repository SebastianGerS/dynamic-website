<?php

namespace Blog\Core;
use Blog\Controllers\ErrorController;

class Router 
{
    private $routeMap;
    private static $regexPatterns = [
        'number' => '\d+',
        'string' => '\w'
    ];// sets the varible to two key => value pairs one containg a regular exprsion to find all numbers and one to find all "word characters" 

    public function __construct() 
    {
        $json = file_get_contents(__DIR__ . '/../../config/routes.json');
        $this->routeMap = json_decode($json, true);
    } // when a new instance of this class is caled all the data in routes.json will be stored as a string in the routemap variable

    public function route(Request $request): string 
    {
        $path = $request->getPath();//gets curent path
        
        foreach($this->routeMap as $route => $info) {

            $regexRoute = $this->getRegexRoute($route, $info);
            
            if (preg_match("@^/$regexRoute$@", $path)) {
                
                return $this->executeController($route, $path, $info, $request);
            }
           
        } // loops thrue all the keyes in the routmap array (all the routes) 
        //and checks if the curent path matches eny of the keys and cals the executecontroller function if it finds a match
        // here regexRoute will att times contain a regular exprsion with in it self to match on eventual numbers (in our case)
        // to be able to match on it being a number of any kind in the matching place of the curent path
        $errorController = new ErrorController($request); 
      
        return $errorController->notFound(); 
        // if the curent path does not match eny of the given paths a errorcontroller function is caled to render a errorpage view 
    }

    private function getRegexRoute(string $route, array $info): string 
    {
        if (isset($info['params'])) {

            foreach ($info['params'] as $name => $type) {

                $route = str_replace(':' . $name, self::$regexPatterns[$type], $route);
            }
        } 

        return $route;
    }// if the $info varible (which contains the value of the key ($route)) contains the key params the params key is looped thrue
        // and the value of each key in params is used to edentify which regex pattern is to replace the curent key from params in $route
        // after this $route is returnd and stored as $regexRoute
    private function executeController(
        string $route,
        string $path,
        array $info,
        Request $request
    ): string 
    {
        $controllerName = '\Blog\Controllers\\' . $info['controller'] . 'Controller'; // the value of $info controller is used to find the corect controller to execute
       
        $controller = new $controllerName($request); 
      
        if (isset($info['login']) && $info['login']) {
        
            if ($request->getCookies()->has('user')) {

                $user = $request->getCookies()->get('user');
                $controller->setUser(json_decode($user));
            
            } else {
                
                $errorController = new UserController($request);
                return $errorController->notFound();
            }
        } // if the login key exists in $info we check if a user cookie is set
        // if so the cookie is used to set the $user varible in the controller (found in the abrtract controller)
        // if the key exists but user cookie is not set the error controller is used to show an error messege
        $params = $this->extractParams($route, $path); 
        return call_user_func_array([$controller, $info['method']], $params); 
        // the corect controller (stored in $controller) method (from $info['method])is caled with params extracted eith extractParams
        
    } 

    private function extractParams(string $route, string $path): array 
    {
        $params = [];
       
        $pathParts = explode('/', $path); // explodes the curent path into an array by every slash
        $routeParts = explode('/', $route); // explodes the curent route thats has been matched into an array by every slash
      
        foreach ($routeParts as $key => $routePart) {

            if (strpos($routePart, ':') === 0) {
                $name = substr($routePart, 1);
                $params[$name] = $pathParts[$key+1];
            } // if the first character in a routpart is : name gets stores the value from routepart except the : (in our case page or id)
        }     // and creates a new keyvalue pair with name as key and sets the value to te content of the coresponind key in the pathparts array 
        //(+1 becous that array starts with a key with an empty string and the coresponding key is there fore a step further away)
        
        return $params;
    }
}