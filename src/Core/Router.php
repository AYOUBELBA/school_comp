<?php
// src/Core/Router.php

namespace Core;

class Router {
    private $routes = [];
    private $params = [];
    
    public function add($method, $path, $handler) {
        $path = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_]+)', $path);
        $this->routes[] = [
            'method' => $method,
            'path' => '#^' . $path . '$#',
            'handler' => $handler
        ];
        return $this;
    }
    
    public function get($path, $handler) {
        return $this->add('GET', $path, $handler);
    }
    
    public function post($path, $handler) {
        return $this->add('POST', $path, $handler);
    }
    
    public function put($path, $handler) {
        return $this->add('PUT', $path, $handler);
    }
    
    public function delete($path, $handler) {
        return $this->add('DELETE', $path, $handler);
    }
    
    public function dispatch($method, $uri) {
        $uri = parse_url($uri, PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;
            
            if (preg_match($route['path'], $uri, $matches)) {
                array_shift($matches);
                $this->params = $matches;
                
                $handler = $route['handler'];
                if (is_array($handler)) {
                    $controller = new $handler[0]();
                    $action = $handler[1];
                    return call_user_func_array([$controller, $action], $this->params);
                }
                return call_user_func($handler, ...$this->params);
            }
        }
        
        throw new \Exception('Route not found', 404);
    }
}