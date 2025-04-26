<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Connor Young</title>

    <link rel="stylesheet" type="text/css" href="../resources/css/default_v3.css">

    <!-- Bootstrap core CSS -->
    <link href="../resources/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../resources/css/album.css" rel="stylesheet">
  </head>

  <body>

<!-- Header -->
<?php include 'header.php'; ?>

    <main role="main">

      <div class="container-fluid" style="background:#f2f4f3">
        <div class="container">
            <h4 class="sectionHeading underline">Autonomous Robot</h4>
        </div>
        <div class="container">
          <br>
          <p>
            This robot was built for a 1v1 competition where it would be placed on a rectangular surface consisting of two halves painted blue and yellow.
            Small plastic cubes were distributed throughout the board at the start of the game, and each robot started on the back edge of its half. The
            goal is for the robot to move around and push the blocks to the other half, as the one with the least blocks on its side at the end of a 30
            second period wins.
        </p>
        <p>
            In order to accomplish this, we had to come up with the optimal battle strategy and then build and program the robot to reliably execute it.
            To allow the robot to gather the necessary information and move as required, we integrated a color sensor so that it could detect its location
            and region edges and constructed two H-bridge circuits to allow for independent wheel control and pulse width modulation. We programmed the
            strategy using Arduino and C with bits, registers, etc.
        </p>
        <p>
            In addition to the electrical and coding work, we also made some mechanical improvements to aid the robot. We used a piece of scrap plastic to
            fashion a snowplow attachment to increase the cube-clearing area and laser-cut acrylic to form a housing that would protect the circuits and
            add mass in case of any collisions with other robots.
        </p> 
          <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/robot.jpg">
                </div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/robot1.jpg">
                </div>
                <div class="col-1"></div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/robot3.jpg">
                </div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/robot4.jpg">
                </div>
                <div class="col-1"></div>
            </div>
            <br>
        </div>
        <br><br>
    </div>


    </main>

    <?php include 'footer.php'; ?>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-slim.min.js"><\/script>')</script>
    <script src="../js/vendor/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/vendor/holder.min.js"></script>
  </body>
</html>

