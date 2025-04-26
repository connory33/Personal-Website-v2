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
            <h4 class="sectionHeading underline">Thermistor Cleaner</h4>
        </div>
        <div class="container">
          <br>
            <p>The first part of the project was customer research, where my partner and I spoke with a campus dining hall worker about their painpoints.
                They told us that food trays needed to be periodically measured for temperature, and that the same thermometer probe (thermistor) is used
                 for each tray. This creates an issue with contamination if the thermistor is not sufficiently cleaned between measurements, something that
                  very commonly happens with innatentive or lazy workers.</p>
            <p>We wanted to create a device that attached to the thermometer and quickly cleaned the probe with the push of a button. We thought the best 
                way to do this would be a scraping device that traveled along the thermistor, also equipped with a internal brush to remove residue.</p>
            <p>We originally looked into linear actuators, but they were too expensive for our budget. We found solenoids as a possible linear motion alternative,
                but their tiny range of motion presented an issue. In an effort to increase this, we devised a lever system in combination with the solenoids that 
                would increase the range of motion from 2 mm to ~2 inches.</p>
            <p>The housing was laser-cut from acrylic, and the bars for the lever system were 3D printed for minimum weight.</p>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/therm_parts.JPG">
                </div>
                <div class="col-5 containedImage">  
                    <img src="../resources/images/therm_top_inside.JPG">
                </div>
                <div class="col-1"></div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/therm_side.JPG">
                </div>
                <div class="col-5 containedImage">
                    <img src="">
                </div>
                <div class="col-1"></div>
            </div>
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