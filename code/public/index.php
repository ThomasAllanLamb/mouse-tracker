<?php
  $mysqli = mysqli_connect("db", "root", "root", "mouse-tracker");
  $res = $mysqli->query(""
    ."\nSELECT x,y,t "
    ."\nFROM MouseHistory "
    ."\nWHERE uid = ("
    ."\n  SELECT v"
    ."\n  FROM KeyValue "
    ."\n  WHERE k='RecentUser'"
    ."\n)"
    ."\nORDER BY t ASC"
  );
  $mouseHistory = $res->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
  <head>
    <script type="text/javascript" src="jquery-3.3.1.min.js"></script>
    <script type="text/javascript">
      $(function () {
        (function () {
          var t0 = (new Date).getTime();
          var incidence = 0;
          //inverse of sample frequency
          var sampleWavelength = 5;
          //!!!: the mouse history since last update
          var mouseHistory = [];
          
          //we anticipate that a future version will send updates periodically rather than onunload, so an id will be necessary for that.
          var myId = Math.floor(Math.random() * (100000));

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

          var lastKnown = {x:0,y:0,t:0};
          setInterval(function () {
            //if the user hasn't moved their mouse and this is their final entry, it might look like their behavior stopped being tracked at the most recent timestamp. To clarify that they just haven't moved, send an update saying so explicitly
            if (mouseHistory.length === 0) {
              mouseHistory.push({
                x:lastKnown.x, 
                y:lastKnown.y, 
                t:(new Date()).getTime()
              });
            }
            
            //assume for simplicity that every request succeeds
            $.ajax({
              method: "POST",
              url: "/history/"+myId,
              data: {mouseHistory:mouseHistory}
            });

            lastKnown = mouseHistory[mouseHistory.length-1];

            mouseHistory = [];
          }, 2000);
        })();

        (function () {
          //!!!: we assume that mouseHistory is sorted by time
          var mouseHistory = <?php echo json_encode($mouseHistory); ?>;

          var t0 = (new Date()).getTime();
          
          var ghost = $("<div>");
          ghost
            .css({
              width:"10px",
              height:"10px",
              background: "darkgreen",
              position:"absolute",
              top:"0px",
              left:"0px",
              display:"none"
            })
            .appendTo("body")
          ;
          
          function animateToNext () {
            currentTime = (new Date()).getTime();
            
            //just in case we got ahead of the history, discard points until the next timestamp is in the future
            while (mouseHistory.length >= 1 && parseInt(mouseHistory[0].t, 10) < currentTime-t0) {
              mouseHistory.shift();
            }

            if (mouseHistory.length === 0) {
              ghost.css({
                background: "maroon"
              });

              return;
            }
            else {
              let target = mouseHistory[0];
              mouseHistory.shift();
              ghost.animate(
                {
                  left: target.x+"px",
                  top: target.y+"px"
                },
                target.t-(currentTime-t0),
                animateToNext
              )
            }
          }
          
          var currentTime = (new Date()).getTime();
          while (mouseHistory.length >= 1 && parseInt(mouseHistory[0].t, 10) < currentTime-t0) {
            mouseHistory.shift();
          }
          if (mouseHistory.length >= 1) {
            setTimeout(
              function () {
                mouseHistory.shift();
                ghost.css({
                  display: "block",
                  top: mouseHistory[0].y+"px",
                  left: mouseHistory[0].x+"px"
                });
                animateToNext();
              },
              mouseHistory[0].t-(currentTime-t0)
            );
          }
        })();
      });

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
</html>