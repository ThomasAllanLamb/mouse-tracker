<!DOCTYPE html>
<head>
  <script type="text/javascript"" src="jquery-3.3.1.min.js"></script>
<?php
  $mysqli = mysqli_connect("db", "root", "root", "mouse-tracker");
  $res = $mysqli->query("SHOW TABLES");
  print_r($res);
?>
  <script type="text/javascript">
    var array;
  </script>
  <style>
    body {
      width:100%;
      height:100%;
    }
  </style>
</head>
<body>
</body>