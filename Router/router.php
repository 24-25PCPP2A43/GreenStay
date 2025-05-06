<?php
// /App/Router.php

class Router {
    private $routes = [];

    public function add($method, $path, $controllerAction) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'controllerAction' => $controllerAction
        ];
    }

    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        foreach ($this->routes as $route) {
            // Simple matching - pour une solution plus robuste, utilisez des expressions régulières
            if ($route['method'] === $requestMethod && $route['path'] === $requestUri) {
                list($controllerName, $actionName) = explode('@', $route['controllerAction']);
                
                // Inclure le fichier du contrôleur
                $controllerFile = __DIR__ . '/../Controller/' . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    
                    // Instancier le contrôleur et appeler l'action
                    $controller = new $controllerName();
                    $controller->$actionName();
                    return;
                }
            }
        }

        // Route non trouvée
        http_response_code(404);
        echo '404 Not Found';
    }
}