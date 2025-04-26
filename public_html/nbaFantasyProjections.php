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
<header class="bg-gray-900 text-white shadow">
  <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3">
    <div class="flex items-center space-x-4"> <!-- Add space between CY and Home -->
      <a href="#" class="flex items-center space-x-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
          <circle cx="12" cy="13" r="4" />
        </svg>
        <strong>CY</strong>
      </a>
      <p><a href="https://connoryoung.com" class="hover:text-blue-400">Home</a></p>
      <p><a href="aboutMe.html" class="hover:text-blue-400">About Me</a></p>
    </div>

    <nav>
      <ul class="flex flex-wrap gap-4 text-sm font-medium">
        <li><a href="nhlIndex.html" class="hover:text-blue-400">NHL Lines</a></li>
        <li><a href="nbaFantasyProjections.html" class="hover:text-blue-400">NBA Fantasy</a></li>
        <li><a href="maddenOptimizer.html" class="hover:text-blue-400">NFL Roster</a></li>
        <li><a href="seniorDesign.html" class="hover:text-blue-400">Sr. Design</a></li>
        <li><a href="autonomousRobot.html" class="hover:text-blue-400">Robot</a></li>
        <li><a href="thermistorCleaner.html" class="hover:text-blue-400">Thermistor</a></li>
        <li><a href="waterPump.html" class="hover:text-blue-400">Water Pump</a></li>
        <li><a href="planterBoxes.html" class="hover:text-blue-400">Planter Boxes</a></li>
      </ul>
    </nav>
  </div>
</header>

    <main role="main">

      <div class="container-fluid" style="background:#f2f4f3">
        <div class="container">
            <h4 class="sectionHeading underline">NBA Fantasy Score Projection</h4>
        </div>
        <br>
        <div class="container">
          <p>
            For this project we built a model that DFS players can utilize to pick better lineups for fantasy competitions. Trained on 5 seasons of NBA
            game data, the model outputs a prediction for a player's fantasy score in an upcoming game based on factors like the opposing team, the game
            being home or away, and his performances in both the short-term and relatively long-term past.
        </p>
        <p>
            We compared the prediction accuracy on a test set after training a pair of random forest and a pair of XGBoost models in order to determine
            which gave the optimal performance. The winner was not actually technically a single model, but 7 independent ones that would each predict
            a single stat out of the 7 categories that mathematically factor into fantasy score. Then we simply applied the open-source DraftKings fantasy
            formula to those predictions to calculate the point value. When using these predictions in conjunction with salary cap optimization for a
            DraftKings contest, we achieved an 8.58% improvement in lineup performance over the DraftKings-provided point projections.
        </p>
        <br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5 containedImage">
                    <img class="d-flex" src="../resources/images/nba_results.PNG">
                </div>
                <div class="col-5 containedImage">
                    <img class="d-flex" src="../resources/images/nba_paper_heading.PNG">
                </div>
                <div class="col-1"></div>
            </div>
            <br><br>
            <div class="row">
                <div class="col-1"></div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/nba_code.PNG">
                </div>
                <div class="col-5 containedImage">
                    <img src="../resources/images/nba_code_2.PNG">
                </div>
                <div class="col-1"></div>
            </div>
        </div>
        <br>
        <p class="text-center">The GitHub for this project can be viewed <a href="https://github.com/andrewkoo/cs5785_final_project">here.</a></p>
        <p class="text-center">The paper written for this project can be downloaded <a href="resources/AML_FinalProject_Report.pdf" download>here.</a></p>
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
