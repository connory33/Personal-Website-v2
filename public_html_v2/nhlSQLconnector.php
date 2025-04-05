<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Connor Young</title>

    <link rel="stylesheet" type="text/css" href="../resources/css/default_v2.css">

    <!-- Bootstrap core CSS -->
    <link href="../resources/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../resources/css/album.css" rel="stylesheet">
  </head>
  <body>
    <div class="bg-dark text-white text-center">
        <?php
        $servername = "connoryoung.com";
        $username = "connor";
        $password = "PatrickRoy33";
        $dbname = "NHL API";

        ini_set('display_errors', 'on');
        error_reporting(E_ALL);

        // Create connection
        $conn = mysqli_connect($servername, $username, $password, $dbname);

        // Check connection
        if (mysqli_connect_error()) {
            die("Connection failed: " . $conn->connect_error);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $searchTerm = $_POST['search'];
            print("Search term: " . $searchTerm . "<br><br>");

            // Write SQL queries to search for term in all different columns (season, date, start time, game type,
            // home team, visiting team, periods)
            $sql = "SELECT export.*, nhl_teams.triCode as triCode
                FROM export INNER JOIN nhl_teams ON export.visitingTeamId = nhl_teams.id
                WHERE `season` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'
                OR `gameDate` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'
                OR `easternStartTime` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'
                OR `gameType` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'
                OR `homeTeamId` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'
                OR `visitingTeamId` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'
                OR `period` LIKE '%" . mysqli_real_escape_string($conn, $searchTerm) . "%'";

            
            echo($sql);
            echo "<br>";

            $result = mysqli_query($conn, $sql);
            if (!$result) {
                die("Query failed: " . mysqli_error($conn));
            }
            
            // Check if there are results
            if (mysqli_num_rows($result) > 0) {
                print("Results found: " . mysqli_num_rows($result) . "<br><br>");
            } else {
                print("No results found.<br>");
            }

            $resultData = $result->fetch_assoc();


            echo "<br>";
            echo "<table class='nhlTable'>";
            echo "<tr style='border: 1px solid white;'>";
            // echo "<td style='font-weight: bold'>Game ID</td>";
            echo "<td style='font-weight: bold'>Season</td>";
            echo "<td style='font-weight: bold'>Game #</td>";
            echo "<td style='font-weight: bold'>Date</td>";
            echo "<td style='font-weight: bold'>Start Time (EST)</td>";
            // echo "<td style='font-weight: bold'>gameScheduleStateId</td>";
            // echo "<td style='font-weight: bold'>gameStateId</td>";
            echo "<td style='font-weight: bold'>Game Type</td>";
            echo "<td style='font-weight: bold'>Home Score</td>";
            echo "<td style='font-weight: bold'>Home Team ID</td>";
            echo "<td style='font-weight: bold'>Period</td>";
            echo "<td style='font-weight: bold'>Visiting Score</td>";
            echo "<td style='font-weight: bold'>Visiting Team</td>";
            echo "</tr>";
            while ($row = $result->fetch_assoc()){
                echo "<tr style='border: 1px solid gray; class='nhlRow'>";
                // echo "<td>".$row['id']."</td>";
                
                # Season
                $formatted_season_1 = substr($row['season'], 0, 4);
                $formatted_season_2 = substr($row['season'], 4);
                echo "<td>".htmlspecialchars($formatted_season_1)."-".htmlspecialchars($formatted_season_2)."</td>";
                
                echo "<td>".$row['gameNumber']."</td>";

                # Date
                $gameDate = $row['gameDate'];
                $gameDatetime = new DateTime($gameDate);
                $formatted_gameDate = $gameDatetime->format('m/d/Y');
                echo "<td>".htmlspecialchars($formatted_gameDate)."</td>";

                # Time
                $formatted_startTime = substr($row['easternStartTime'], 11, -3);
                echo "<td>".htmlspecialchars($formatted_startTime)."</td>";

                // echo "<td>".$row['gameScheduleStateId']."</td>";

                # Game State (i.e. Final, In Progress, etc.)
                // echo "<td>".$row['gameStateId']."</td>";

                # Game Type (i.e. Preseason, Regular Season, etc.)
                $gameType_num = $row['gameType'];
                if ($gameType_num == 1) {
                    $gameType_text = "Preseason";
                } elseif ($gameType_num == 2) {
                    $gameType_text = "Reg. Season";
                } elseif ($gameType_num == 3) {
                    $gameType_text = "Playoffs";
                } else {
                    $gameType_text = "Unknown";
                }
                echo "<td>".$gameType_text."</td>";

                # Home Team
                echo "<td>".$row['homeScore']."</td>";
                echo "<td>".$row['homeTeamId']."</td>";

                # Period
                $period_num = $row['period'];
                if ($period_num == 3) {
                    $period_text = "Reg";
                } elseif ($period_num == 4) {
                    $period_text = "OT";
                } elseif ($period_num == 5) {
                    $period_text = "SO";
                } else {
                    $period_text = $period_num;
                }
                echo "<td>".$period_text."</td>";
                
                # Visitor
                echo "<td>".$row['visitingScore']."</td>";
                echo "<td>".$row['triCode']."</td>";
                echo "</tr>";
            }
            echo "</table>";


            $conn->close();
        }
        ?>

    <br>
    <br>
    </div>

    <footer class="text-muted">
      <div class="container">
        <p class="float-right">
          <a href="#">Back to top</a>
        </p>
        <p>Copyright &copy; 2021 Connor Young</p>
      </div>
    </footer>

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