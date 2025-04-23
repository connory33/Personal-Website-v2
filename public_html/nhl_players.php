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
  <header>
      <div class="collapse bg-dark" id="navbarHeader">
        <div class="container">
          <div class="row">
            <div class="col-sm-8 col-md-7 py-4">
              <h4 class="text-white">About Me</h4>
              <p class="text-white">
                  My name is Connor Young and I'm from Palo Alto, CA. I have a BS in Mechanical Engineering and a minor in Business from Cornell University
                  and a Master's in Operations Research and Information Engineering from Cornell Tech. 
                  <br /><br />
                  My career goal is to bring innovative technologies to the world.
                  I thrive at the intersection of technical and non-technical thinking and enjoy using my analytical mindset to break down and solve problems
                  that are both quantitatively and qualitatively complex.
                  <br /><br />
                  I seek opportunities to understand and balance customer needs with engineering constraints to deliver
                  innovative and high-value products. I greatly enjoy fast-paced work environments with high levels of
                  collaboration, accountability, and room for impact and growth.
              </p>
            </div>
            <div class="col-sm-4 offset-md-1 py-4">
              <h4 class="text-white">Contact</h4>
              <ul class="list-unstyled">
                <li><a style="color: dodgerblue" class="footerContent" href="https://www.linkedin.com/in/connoryoung33/">LinkedIn</a></li>
                <li><a style="color: #b55850" class="footerContent" href="https://www.github.com/connory33">GitHub</a></li>
                <li><a href="#" class="text-white">connor@connoryoung.com</a></li>
              </ul>
              <h4 class="text-white">Site Navigation</h4>
              <ul class="list-unstyled">
                <li><a style="color: dodgerblue" class="footerContent" href="index_v3.html">Home</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="nhlIndex.html">NHL Database</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="nhlLinesProject.html">NHL Lines Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="nbaFantasyProjections.html">NBA Fantasy Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="maddenOptimizer.html">NFL Roster Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="seniorDesign.html">Sr. Design Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="autonomousRobot.html">Robot Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="thermistorCleaner.html">Thermistor Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="waterPump.html">Water Pump Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="planterBoxes.html">Planter Box Project</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <div class="navbar navbar-dark bg-dark box-shadow">
        <div class="container d-flex justify-content-between">
          <a href="#" class="navbar-brand d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
            <!-- <a href="../resources/images/Wheel1.jpg" width="20" height="20" class="mr-2"></a> -->
            <strong>CY</strong>
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
        </div>
      </div>
    </header>
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


        if (!empty($_GET['search_column']) && !empty($_GET['search_term'])) {
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

            // Pagination setup
            $limit = 25; // Results per page
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = ($page - 1) * $limit;

            // Get total count (for Load More logic)
            $count_sql = "SELECT COUNT(*) as total FROM (" . preg_replace("/SELECT.+?FROM/", "SELECT 1 FROM", $sql, 1) . ") as count_table";
            $count_result = mysqli_query($conn, $count_sql);
            $total_rows = mysqli_fetch_assoc($count_result)['total'];

            $start = $offset + 1;
            $end = min($offset + $limit, $total_rows);
            $total_pages = ceil($total_rows / $limit);

            $sql .= " LIMIT $limit OFFSET $offset";

            echo "<br>";

            // Execute the query, check if successful and if results were found
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                die("Query failed: " . mysqli_error($conn));
            }
            
            echo "<h5>Results found for  " . ucfirst($searchColumn) . ' = ' . $originalSearchTerm . "</h5>";
            
            if (mysqli_num_rows($result) == 0) {
                print("No results found.<br><br>");
            }

            ?>

            <!-- Display results in a table format -->
            <div class="table-container">
                <table id='games-players-summary-table'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Number</th>
                            <th>Team</th>
                        </tr>
                    </thead>
                    <tbody>

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
                    echo "</tbody>";
                echo "</table>";
            echo "</div>";

            $total_pages = ceil($total_rows / $limit);

                ?>

                <?php if ($total_rows > 0): ?>
                    <br><div style="margin-bottom: 10px;">
                        Showing results <?= $start ?>–<?= $end ?> of <?= $total_rows ?> (Page <?= $page ?> of <?= $total_pages ?>)
                    </div>
                <?php endif; ?>

                <?php

                if ($page==1) {
                    $next_page = $page + 1;
                    $advance_page = http_build_query(array_merge($_GET, ['page' => $next_page]));
                    echo "<div><a class='btn btn-secondary' href='?" . $advance_page . "'>Next</a>
                        </div>";
                } else if ($page>1 and $page<$total_pages) {
                    $prev_page = $page - 1;
                    $next_page = $page + 1;
                    $prev_page = http_build_query(array_merge($_GET, ['page' => $prev_page]));
                    $advance_page = http_build_query(array_merge($_GET, ['page' => $next_page]));
                    echo "<div style='text-align:center; margin-top: 20px;'>
                        <a class='btn btn-secondary' href='?" . $prev_page . "' style='margin-right: 10px'>Previous</a>";
                    echo "<a class='btn btn-secondary' href='?" . $advance_page . "'>Next</a>
                        </div>";
                } else {
                    $prev_page = $page - 1;
                    echo "<div style='text-align:center; margin-top: 20px;'>
                        <a class='btn btn-secondary' href='?" . $prev_page . "'>Previous</a></div>";
                }      

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