<?php
// src/Core/Controller.php

namespace Core;

class Controller
{
    protected function view($view, $data = [])
    {
        extract($data);
        $viewFile = __DIR__ . "/../../views/{$view}.php";
        
        if (file_exists($viewFile)) {
            ob_start();
            include $viewFile;
            $content = ob_get_clean();
            
            // Check if layout exists
            $layoutFile = __DIR__ . "/../../views/layouts/main.php";
            if (file_exists($layoutFile)) {
                include $layoutFile;
            } else {
                echo $content;
            }
        } else {
            throw new \Exception("View not found: {$view}");
        }
    }
    
    protected function redirect($url, $flash = [])
    {
        if (!empty($flash)) {
            $_SESSION['flash'] = $flash;
        }
        header("Location: {$url}");
        exit;
    }
    
    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function validate($data, $rules)
    {
        $errors = [];
        $valid = true;
        
        foreach ($rules as $field => $rule) {
            $rulesList = explode('|', $rule);
            
            foreach ($rulesList as $singleRule) {
                if ($singleRule === 'required' && empty($data[$field])) {
                    $errors[$field][] = "The {$field} field is required";
                    $valid = false;
                }
                
                if (strpos($singleRule, 'min:') === 0) {
                    $min = explode(':', $singleRule)[1];
                    if (isset($data[$field]) && strlen($data[$field]) < $min) {
                        $errors[$field][] = "The {$field} must be at least {$min} characters";
                        $valid = false;
                    }
                }
                
                if (strpos($singleRule, 'max:') === 0) {
                    $max = explode(':', $singleRule)[1];
                    if (isset($data[$field]) && strlen($data[$field]) > $max) {
                        $errors[$field][] = "The {$field} must not exceed {$max} characters";
                        $valid = false;
                    }
                }
                
                if ($singleRule === 'email' && isset($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field][] = "The {$field} must be a valid email address";
                    $valid = false;
                }
                
                if ($singleRule === 'numeric' && isset($data[$field]) && !is_numeric($data[$field])) {
                    $errors[$field][] = "The {$field} must be a number";
                    $valid = false;
                }
            }
        }
        
        return ['valid' => $valid, 'errors' => $errors];
    }
    
    protected function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}