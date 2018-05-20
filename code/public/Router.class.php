<?php

  class Router {
    private $routes;

    public function __construct () {
      $this->routes = [];
    }

    public function route ($path, $requestMethod, $f)
    {
      $this->routes[] = new Route($path, $requestMethod, $f);
    }

    public function request ($path, $requestMethod) {
      for ($i = 0; $i <= count($this->routes)-1; $i++) {
        $route = $this->routes[$i];
        if ($route->isMatch($path, $requestMethod))
        {
          $route->applyPath($path);
          return;
        }
      }
    }
  }
  
?>