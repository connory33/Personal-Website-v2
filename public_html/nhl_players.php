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
    <div class="bg-dark text-white text-center">
        <br>
        <p>Search again:</p>

        <div class='search-container'>
            <form id="nhl-search" method="GET" action="nhl_games.php">
                    <select name="search_column" id="nhl-search-column">
                        <option value="season">Season</option>
                        <option value="gameDate">Game Date</option>
                        <option value="easternStartTime">Start Time</option>
                        <option value="gameType">Game Type</option>
                        <option value="team">Team</option>
                        <option value="homeTeamId">Home Team</option>
                        <option value="awayTeamId">Away Team</option>
                        <option value="player">Player Name</option>
                    </select>
                    <input  type="text" name="search_term" id="search-term" placeholder="Enter search term" required>
                    <input  type="submit" value="Search" class="btn btn-primary">
            </form>
        </div>
        <br>

        <?php
        include('db_connection.php');

        ini_set('display_errors', 1); error_reporting(E_ALL);

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            // $searchTerm = $_POST['search_term'];
            $searchColumn = mysqli_real_escape_string($conn, $_GET['search_column']);
            $searchTerm = mysqli_real_escape_string($conn, $_GET['search_term']);
            $originalSearchTerm = $searchTerm;

        
            $sql = "SELECT *
                FROM
                    nhl_players
                WHERE 
                    firstName LIKE '%$searchTerm%' 
                    OR lastName LIKE '%$searchTerm%'";

            echo "<br>";

            // Execute the query, check if successful and if results were found
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                die("Query failed: " . mysqli_error($conn));
            }
            
            echo "<h5>Results found for  " . ucfirst($searchColumn) . ' = ' . $originalSearchTerm . "</h5>";
            
            if (mysqli_num_rows($result) > 0) {
                echo "<h6>" . mysqli_num_rows($result) . " results<br><br></h6>";
            } else {
                print("No results found.<br><br>");
            }

            ?>

            <!-- Display results in a table format -->
            <table id='games-players-summary-table'>
                <thead>
                    <tr style="color: white; font-weight: bold; background-color: #2e5b78; border: 1px solid #bcd6e7">
                        <th>ID</th>
                        <th>Name</th>
                        <th>Number</th>
                        <th>Team</th>
                    </tr>
                </thead>

            <?php
            while ($row = $result->fetch_assoc()){
                echo "<tr>";
                    echo "<td><a href='player_details.php?player_id=" . $row['playerId'] . "'" . "</a>" . $row['playerId'] . "</td>";
                    echo "<td>" . $row['firstName'] . ' ' . $row['lastName'] . "</td>";
                    if ($row['sweaterNumber'] == '') {
                        echo "<td>-</td>";
                    } else {
                        echo "<td>" . $row['sweaterNumber'] . "</td>";
                    }
                    if ($row['currentTeamAbbrev'] == '') {
                        echo "<td>-</td>";
                    } else {
                        echo "<td>" . $row['currentTeamAbbrev'] . "</td>";
                    }

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
        <p>Copyright &copy; 2025 Connor Young</p>
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
    <!-- Placed at the end of the document so the pages load faster -->

    <!-- JS for search form, allowing player to access nhl_players.php and others to nhl_games.php -->
    <script>
        document.getElementById('nhl-search').addEventListener('submit', function (e) {
            const column = document.getElementById('nhl-search-column').value;
            if (column === 'player') {
            this.action = 'nhl_players.php';
            } else {
            this.action = 'nhl_games.php';
            }
        });
    </script>

  </body>
</html>