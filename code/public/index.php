<!DOCTYPE html>
<head>
  <script type="text/javascript" src="jquery-3.3.1.min.js"></script>
<?php
  $mysqli = mysqli_connect("db", "root", "root", "mouse-tracker");
  $res = $mysqli->query("SHOW TABLES");
?>
  <script type="text/javascript">
    var t0 = (new Date).getTime();
    var incidence = 0;
    //inverse of sample frequency
    var sampleWavelength = 10;
    var mouseHistory = [];
    //we anticipate that a future version will send updates periodically rather than onunload, so an id will be necessary for that.
    var myId = Math.floor(Math.random() * (10000));

    $("html").on("mousemove", function (e) {
      incidence++;
      //incidence-1 because we want to offset the sampling in order record the first event
      if ((incidence-1) % sampleWavelength !== 0) {
        return;
      }
      else if ((incidence-1) % sampleWavelength === 0)
      {
        mouseHistory.push({
          t: (new Date).getTime()-t0,
          x: e.pageX,
          y: e.pageY
        });
      }
    });

    setTimeout(function () {
      $.ajax({
        method: "POST",
        url: "/history/"+myId,
        data: {mouseHistory:mouseHistory},
        async: false
      })
        .always(function () {
          debugger;
        })
      ;
    }, 4000);
  </script>
  <script type="text/javascript">
    var array;
  </script>
  <style>
    html {
      width:100%;
      height:100%;
      box-sizing:border-box;
    }
    body {
      width:100%;
      height:100%;
      margin:0;
    }
  </style>
</head>
<body>
</body>