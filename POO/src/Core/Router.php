<?php

namespace App;

class Router {
    private $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function resolve() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $callback = $this->routes[$method][$path] ?? false;

        if (!$callback) {
            http_response_code(404);
            echo "404 - Vous etes perdu ? La page n'existe pas !";
            return;
        }

        if (is_callable($callback)) {
            return call_user_func($callback);
        }

        if (is_array($callback)) {
            $controller = new $callback[0];
            return $controller->{$callback[1]}();
        }
    }
}