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
