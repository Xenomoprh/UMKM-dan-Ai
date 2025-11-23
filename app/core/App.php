<?php

// File: app/core/App.php

class App {
    protected $controller = 'Home'; // Controller default
    protected $method = 'index';    // Method default
    protected $params = [];         // Parameter default

    public function __construct() {
        $url = $this->parseURL();

        // Handle Controller
        if (isset($url[0]) && file_exists('../app/controllers/' . $url[0] . '.php')) {
            $this->controller = $url[0];
            unset($url[0]);
        }
        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        // Handle Method
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // Handle Params
        if (!empty($url)) {
            $this->params = array_values($url);
        }

        // Jalankan controller & method, serta kirimkan params jika ada
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL() {
        // First try to get URL from query string
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        
        // If not in query string, try to get from PATH_INFO (for URLs like /index.php/controller/method)
        if (isset($_SERVER['PATH_INFO'])) {
            $url = rtrim($_SERVER['PATH_INFO'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            $url = array_filter($url); // Remove empty values
            return array_values($url);
        }
        
        return [];
    }
}