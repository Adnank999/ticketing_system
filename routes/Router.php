<?php

class Router {
    private static $routes = [];

    public static function add($method, $path, $handler) {
        self::$routes[] = compact('method', 'path', 'handler');
    }

    public static function dispatch() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        foreach (self::$routes as $route) {
            $pattern = preg_replace('#\{[a-z_]+\}#', '([a-zA-Z0-9_-]+)', $route['path']);
            if ($method === $route['method'] && preg_match("#^{$pattern}$#", $uri, $matches)) {
                array_shift($matches);
                call_user_func_array($route['handler'], $matches);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
}
