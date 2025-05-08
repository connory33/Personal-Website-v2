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

    <script src="https://cdn.tailwindcss.com"></script>

  </head>

  <body>

<!-- Header -->
<?php include 'header.php'; ?>

    <main role="main">

      <div class="container-fluid" style="background:#f2f4f3">
        <div class="container">
            <h4 class="sectionHeading underline">Water Pump</h4>
        </div>
        <div class="container">
          <br>
            <p>
                Our task was to make a pump that could move 1 liter of water per minute against gravity. We could use any pump type (piston, 
                peristaltic, etc.) but had to fabricate it ourselves from aluminum stock and standard parts (o-rings, bearings, etc.) using a mill
                and lathe. Our group initially split up to research the pros and cons of the various feasible types and prepare an initial CAD design
                for the type we researched. I researched piston pumps, which we decided to use for their reliability and relatively simple fabrication.
                Specifically, we chose a two-cylinder reciprocating pump powered by a scotch yoke mechanism.
            </p>
            <p>
                After adding more detail to and improving the CAD design, we assembled a BOM and bought the necessary materials. Then it was a relatively
                easy fabrication process except for the scotch yoke, which had to have its thickness substatially reduced with the lathe. After assembly
                and testing, we ended up with a pump that moved 5 L/min.
            </p>
            <p>
                I really enjoyed this project because it was fun to do something hands-on, especially given that we were able to take it all the way from
                research and design to a finished and working pump that we could see in action. It was a very valuable experience from that perspective
                and also in terms of getting practice working with an engineering team in a practical environment that included things like formal BOMs,
                order forms, and cost estimates.
                <!-- Technically, this project improved my design, CAD, and machining skills. However, it also was a great exercise for some more intangible
                 abilities. This is one of few group engineering projects I've been a part of, and learning to delegate and cooperate in an engineering
                  team is something I want to improve and excel at. I also was able to take a product all the way from ideation to completion and testing,
                   something that students don't get a lot of in normal curriculum. --> 
                </p>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/wp_cad.JPG">
                </div>
                <div class="col-5 containedImage">  
                    <img src="../resources/images/WP1.jpeg">
                </div>
                <div class="col-1"></div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/Assembly_Render2.jpg">
                </div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/Wheel2.jpg">
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