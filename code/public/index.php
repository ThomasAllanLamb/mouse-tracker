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
    <a href="https://github.com/ThomasAllanLamb/mouse-tracker" class="github-corner" aria-label="View source on Github"><svg width="80" height="80" viewBox="0 0 250 250" style="fill:#151513; color:#fff; position: absolute; top: 0; border: 0; right: 0;" aria-hidden="true"><path d="M0,0 L115,115 L130,115 L142,142 L250,250 L250,0 Z"></path><path d="M128.3,109.0 C113.8,99.7 119.0,89.6 119.0,89.6 C122.0,82.7 120.5,78.6 120.5,78.6 C119.2,72.0 123.4,76.3 123.4,76.3 C127.3,80.9 125.5,87.3 125.5,87.3 C122.9,97.6 130.6,101.9 134.4,103.2" fill="currentColor" style="transform-origin: 130px 106px;" class="octo-arm"></path><path d="M115.0,115.0 C114.9,115.1 118.7,116.5 119.8,115.4 L133.7,101.6 C136.9,99.2 139.9,98.4 142.2,98.6 C133.8,88.0 127.5,74.4 143.8,58.0 C148.5,53.4 154.0,51.2 159.7,51.0 C160.3,49.4 163.2,43.6 171.4,40.1 C171.4,40.1 176.1,42.5 178.8,56.2 C183.1,58.6 187.2,61.8 190.9,65.4 C194.5,69.0 197.7,73.2 200.1,77.6 C213.8,80.2 216.3,84.9 216.3,84.9 C212.7,93.1 206.9,96.0 205.4,96.6 C205.1,102.4 203.0,107.8 198.3,112.5 C181.9,128.9 168.3,122.5 157.7,114.1 C157.9,116.9 156.7,120.9 152.7,124.9 L141.0,136.5 C139.8,137.7 141.6,141.9 141.8,141.8 Z" fill="currentColor" class="octo-body"></path></svg></a><style>.github-corner:hover .octo-arm{animation:octocat-wave 560ms ease-in-out}@keyframes octocat-wave{0%,100%{transform:rotate(0)}20%,60%{transform:rotate(-25deg)}40%,80%{transform:rotate(10deg)}}@media (max-width:500px){.github-corner:hover .octo-arm{animation:none}.github-corner .octo-arm{animation:octocat-wave 560ms ease-in-out}}</style>
  </body>
</html>