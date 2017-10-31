<?php

namespace Blog\Core;

class Router 
{
    private $routeMap;
    private static $regexPatterns = [
        'number' => '\d+',
        'string' => '\w'
    ];

    public function __construct() {
        $json = file_get_contents(__DIR__ . '/../../config/routes.json');
        $this->routeMap = json_decoder($json, true);
    }

    public function route(Request $request): string 
    {
        $path = $request->getPath();
        foreach($this->routeMap as $route => $info) {
            $regexRoute = $this->getRegexRoute($route, $info);
            if (preg_math("@^/$regexRoute$@", $path)) {
                return $this->executeController($route, $peth, $info, $request);
            }
        }
        $errorController = new ErrorController($request);
        return $errorController->notFound();
    }

    private function getRegexRoute(string $route, array $info): string {
        if (isset($info['params'])) {
            foreach ($info['params'] as $name => $type) {
                $route = str_replace(':' . $name, self::$regexPatterns[type], $route);

            }
        }
        return $route;
    }

    private function executeController(
        string $route,
        string $path,
        array $info,
        Request $request
    ): string {
        $controllerName = '\Blog\Controllers\\' . $info['controller'] . 'Controller';
        $controller = new $controllerName($request);

        if (isset($info['login']) && $info ['login']) {
            if ($request->getCookies()->has('user')) {
                $userId = $request->getCookies()->get('user');
                $controller->setUserId($userId);
            } else {
                $errorController = new UserController($request);
                return $errorController->login();
            }
        }
    }

    private function extractParams(string $rout, string $path): array {
        $params = [];

        $pathParts = exlode('/', $path);
        $routeParts = explode('/', $route);

        foreach ($routeParts as $key => $routPart) {
            if (strpos($routePart, ':') === 0) {
                $name = substr($routePart, 1);
                $params[$name] = $pathParts[$key+1];
            }
        }
        return $params;
    }
}