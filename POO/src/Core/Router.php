<?php

namespace App\Core;

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
        // rtrim remove a barra final para evitar erro de /user vs /user/
        $path = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $path = $path ?: '/'; // Se ficar vazio, vira '/'

        foreach ($this->routes[$method] as $route => $callback) {
            // Normaliza a rota para comparação também
            $route = rtrim($route, '/') ?: '/';

            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $path, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // 1. Se for uma função anônima (Closure)
                if (is_callable($callback)) {
                    return call_user_func_array($callback, $params);
                }

                // 2. Se for um array ['Classe', 'Metodo']
                if (is_array($callback)) {
                    [$className, $methodName] = $callback;
                    
                    // IMPORTANTE: Remova espaços e verifique se a classe existe
                    $className = trim($className); 

                    if (class_exists($className)) {
                        $controller = new $className();
                        if (method_exists($controller, $methodName)) {
                            return call_user_func_array([$controller, $methodName], $params);
                        }
                        die("Erro: Método $methodName não encontrado na classe $className");
                    }
                    die("Erro: Classe $className não encontrada. Verifique o require ou autoload.");
                }
            }
        }

        http_response_code(404);
        echo "404 - Rota não encontrada";
    }
}