<?php
  spl_autoload_register(function ($className) {
    include_once $className.".class.php";
  });

  $router = new Router();

  $router->route("item/:id", 'GET', function ($params) {
    echo "get item<br>";
    echo "params<br>";
    print_r($params);
  });

  $router->route("item", 'POST', function ($params) {
    echo "post item<br>";
    echo "params<br>";
    print_r($params);
  });

  $router->route("item/:id", 'PUT', function ($params) {
    echo "put item<br>";
    echo "params<br>";
    print_r($params);
  });

  $router->route("item/:id", 'DELETE', function ($params) {
    echo "delete item<br>";
    echo "params<br>";
    print_r($params);
  });

  $router->request($_GET['route'], $_SERVER['REQUEST_METHOD']);
?>