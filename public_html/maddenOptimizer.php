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


  </head>

  <body>

<!-- Header -->
<?php include 'header.php'; ?>
    <main role="main">

      <div class="container-fluid" style="background:#f2f4f3">
        <div class="container">
            <h4 class="sectionHeading underline">Madden Roster Optimizer</h4>
        </div>
        <br>
        <div class="container">
          <p>I thought it would be interesting from both a quantitative and qualitative perspective to try to optimize the "best" football roster
            (according to Madden ratings) to meet salary cap constraints, and I was right! Immediately I realized the fact that there is really no way to
            define "best" in this sense, and there is no mathematically optimal solution when you consider that tradeoffs and choices have to be made in terms
            of things like position. It's impossible to fill the lineup with 99 OVRs, so what positions do you put them at? Would you rather have a lineup of 5%
            all-stars and 95% scrubs or a deeper but worse-on-average roster? Due to this fact, I opted to use Excel's evolutionary solver, which allows you to
            essentially choose weights to encourage or discourage certain optimization choices via bonuses and penalties.
          </p>
          <p>
            One other cool thing that I had was access to Madden's "scheme" definitions, some examples of which are partially shown in the bottom left image above.
            This added a new dimension, I could create the "best" lineups for various schemes by utilizing the fact that Madden also provides skill ratings for each
            player in the individual categories/archetypes as well as their OVR.
          </p>
          <p>
            After organizing the data and setting up the sheets for the various schemes so that they pulled from the data sheet properly, I was able to start
            thinking about the penalties and bonuses. I added severe penalties for things like the position constraints and salary caps to prevent them from being
            broken, but also gave softer ones to things like having too high of an average age. I also gave penalties for having players with horrendously low ratings
            to add some sense of realism because they would probably not be on any "best" NFL roster. I gave bonuses for things like having college teammates or
            players with exceedlingly high ratings or certain tags like Superstar and Hidden.
          </p>
          <p>
            All in all, there is work that could be done to tweak the weights
            and try to dial in an exceedingly realistic roster builder, but it was really cool just to see how the computed optimal lineups changed with different
            weights and schemes and how you can mathematically build in certain qualitative roster desires and considerations.
          </p>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/madden1.PNG">
                </div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/madden2.PNG">
                </div>
                <div class="col-1"></div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/madden3.PNG">
                </div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/madden4.PNG">
                </div>
                <div class="col-1"></div>
            </div>
            <br>
        </div>
        <p class="text-center">The Excel sheet can be downloaded <a href="resources/6760_NFL_roster_optimization.xlsx" download>here</a></p>
        <p class="text-center">The corresponding presentation can be downloaded <a href="resources/6760_NFL_roster_optimization.pptx" download>here</a></p>
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

