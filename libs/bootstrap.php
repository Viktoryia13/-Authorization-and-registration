<?php
class Bootstrap
{
    public function __construct()
    {
        $url = isset($_GET['url']) ? $_GET['url'] : null;

        if (empty($url[0])) {
            require 'controllers/users.php';
            $controller = new Users();
            $controller->index();
            
            return false;
        }

        $url = rtrim($url, '/');
        $url = explode('/', $url);
        $file = 'controllers/' . $url[0] . '.php';
        if (file_exists($file)) {
            require $file;
        } else {
            require_once('controllers/error.php');
            $controller = new Errors();
            return false;
        }

        $controller = new $url[0];
      //  $controller->loadModel($url[0]);

        if (isset($url[2])) {                              
            if (method_exists($controller, $url[1])) {
                $controller->{$url[1]}($url[2]);
            } else {
                echo 'Error!';
            }
        } else {
            if (isset($url[1]) && method_exists($controller, $url[1])) {      
                $controller->{$url[1]}();
            } else {
                $controller->index();
            }
        }
    }
}
