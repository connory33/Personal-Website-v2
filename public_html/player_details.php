<!-- TEMPLATE FOR GAME DETAIL PAGES - GETS ID SELECTED ON GAMES PAGE AND USES IT TO QUERY DATABASE FOR ADDITIONAL GAME DETAILS -->

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Connor Young</title>

    <!-- Bootstrap core CSS -->
    <link href="../resources/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../resources/css/album.css" rel="stylesheet">

    <link href="/resources/css/default_v3.css" rel="stylesheet" type="text/css" />

  </head>
  <body>
    <div class="bg-dark text-white">

        <?php
        include('db_connection.php');

        // Check if the 'game_id' is passed in the URL
        if (isset($_GET['player_id'])) {
            $player_id = $_GET['player_id'];

            $sql = "SELECT * FROM nhl_rosters
            JOIN nhl_teams ON nhl_rosters.teamID=nhl_teams.id WHERE playerID=$player_id";
            $playerInfo = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_assoc($playerInfo)) {
                $name = $row['firstName'] . ' ' . $row['lastName'];
                $teamName = $row['fullName'];
                $sweaterNumber = $row['sweaterNumber'];
                $position = $row['positionCode'];
                $headshotURL = $row['headshotURL'];
            }
            echo "<br>";
            echo "<h2>Player Details: " . $name . " (" . $player_id . ")</h2>";
            echo "<p>Current Team: " . $teamName . "</p>";
            echo "<p>Sweater Number: " . $sweaterNumber . "</p>";
            echo "<p>Position: " . $position . "</p>";
            echo "<p><img src='" . htmlspecialchars($headshotURL) . "' alt='headshot' style='height: 65px;'></p>";


            


           

        }
        ?>
        <br><br><br><br><br>

    </div>

    
    <footer class="text-muted">
      <div class="container">
        <p class="float-right">
          <a href="#">Back to top</a>
        </p>
        <p>Copyright &copy; 2025 Connor Young</p>
      </div>
    </footer>
                </div>

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