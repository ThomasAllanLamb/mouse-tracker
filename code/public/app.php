<?php
  spl_autoload_register(function ($className) {
    include_once $className.".class.php";
  });

  $router = new Router();

  $router->route("history/:uid", 'POST', function ($params) {
    $db = mysqli_connect("db", "root", "root", "mouse-tracker");
    $sql = "INSERT INTO MouseHistory (uid,x,y,t) VALUES ";
    for ($i = 0; $i <= count($_POST["mouseHistory"])-1; $i++) {
      $point = $_POST["mouseHistory"][$i];
      if ($i >= 1) {
        $sql = $sql.", ";
      }
      $sql = $sql . "(".$params["uid"].", ".$point["x"].", ".$point["y"].", ".$point["t"].")";
    }
    $res = $db->query($sql);

    $db->query(""
      ."\nUPDATE KeyValue"
      ."\nSET v='".$params["uid"]."'"
      ."\nWHERE k='RecentUser'"
    );
  });

  $router->route("honk", 'GET', function ($params) {
    echo "get honk<br>";
    print_r($params);
  });

  $router->request($_GET['route'], $_SERVER['REQUEST_METHOD']);
?>