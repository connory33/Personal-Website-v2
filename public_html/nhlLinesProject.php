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
                <h4 class="sectionHeading underline">NHL Line Analysis</h4>
            </div>
            <br>
            <div class="container">
                <p>
                    In this project, I investigated the dependency of each NHL team on their "top line" throughout the course of the 2020-2021 and 2021-2022 seasons.
                </p>
                <p>
                    A typical NHL team uses 4 "lines" of forwards, rotating them frequently throughout the game. Each line is a set of 3 players, and the "top" or "1st" line
                    generally receives the highest percentage of playing time in a game, decreasing with each line.
                </p>
                <p>
                    Looking at the percentage of total playing time that each team gives to its top line and comparing it with overall team performance can reveal some interesting
                    insights about the tradeoffs between having star players and having a "deep" roster where the lesser lines share more playing time. Of course, there are many other
                    factors to consider in overall performance, as well as choices to make in how a top line is defined, given that many teams "shuffle" their lines throughout the course
                    of a game. All of these topics and more are discussed in the associated paper.
                </p>
                <p>
                    Data used for this project was scraped from the public NHL API and all analysis was done using Python and associated libraries.
                </p>
                <br>
                <div class="row">
                    <div class="col-1"></div>
                    <div class="col-5 containedImage">
                        <img class="d-flex" src="../resources/images/shift_pic1.PNG">
                    </div>
                    <div class="col-5 containedImage">
                        <img class="d-flex" src="../resources/images/shift_pic2.PNG">
                    </div>
                    <div class="col-1"></div>
                </div>
                <br><br>
                <div class="row">
                    <div class="col-1"></div>
                    <div class="col-5 containedImage">
                        <img src="../resources/images/shift_pic3.PNG">
                    </div>
                    <div class="col-5 containedImage">
                        <img src="../resources/images/shift_pic4.PNG">
                    </div>
                    <div class="col-1"></div>
                </div>
            </div>
            <br>
            <p class="text-center">The GitHub for this project can be viewed <a href="https://github.com/connory33/NHL_shift_analysis">here.</a></p>
            <p class="text-center">The paper written for this project can be downloaded <a href="resources/NHL_shift_analysis.pdf" download>here.</a></p>
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
