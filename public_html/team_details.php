<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../../../favicon.ico">

        <title>Connor Young</title>

        <!-- Bootstrap core CSS -->
        <link href="../resources/css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom styles for this template -->
        <link href="../resources/css/album.css" rel="stylesheet">

        <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />

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
                <li><a style="color: dodgerblue" class="footerContent" href="">Home</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="">NHL Database</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="">NHL Lines Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="">NBA Fantasy Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="">NFL Roster Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="">Sr. Design Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="">Robot Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="">Thermistor Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="">Water Pump Project</a></li>
                <li><a style="color: dodgerblue" class="footerContent" href="">Planter Box Project</a></li>
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
    <div class="bg-dark text-white">
      <div style='margin-left: 3%; margin-right: 3%'>
        <?php

          ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ALL);
          include('db_connection.php');

    ########## GET ALL TEAM DATA TO USE FOR THE PAGE ##########

          // Check if the 'team_id' is passed in the URL
          if (isset($_GET['team_id'])) {
              $team_id = $_GET['team_id'];

              $sql = "SELECT 
                        teams.fullName, 
                        teams.teamLogo, 
                        latest_stats.*,
                        season_stats.*
                      FROM nhl_teams AS teams
                      LEFT JOIN team_latest_stats AS latest_stats
                        ON teams.id = latest_stats.teamID
                      LEFT JOIN team_season_stats AS season_stats
                        ON teams.id = season_stats.teamID
                      WHERE teams.id = $team_id
                      GROUP BY season_stats.playerID";
              $result = mysqli_query($conn, $sql);

              if (!$result) {
                echo "SQL Error: " . mysqli_error($conn);
              } elseif (mysqli_num_rows($result) == 0) {
                echo "No players found for this team.";
              } else {
                    // Fetch the row to get the team logo
                    $team = mysqli_fetch_assoc($result);

                    ### Flexbox for player name/number/active/id and headshot/team logo - aligns them side-by-side ###
                    echo "<div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;'>";
                        // Left side: Team Name
                        echo "<div>";
                            echo "<h1 style='margin-top: 3px'>Team Details: " . $team['fullName'] . "</h1>";
                        echo "</div>";
                        // Right side: Team Logo
                        $teamLogo = $team['teamLogo'];
                        echo "<div style='display: flex; align-items: center; gap: 5px; margin-top: 10px;'>";
                            if ($teamLogo != 'false' and $teamLogo != '' and $teamLogo != 'N/A') {
                                echo "<img src='" . htmlspecialchars($teamLogo) . "' alt='team logo' style='height: 120px'>";
                            } else {
                                echo "<p></p>";
                            }
                        echo "</div>";
                    echo "</div>";
                    mysqli_data_seek($result, 0); // Reset the result pointer to the first row
             }

            
              
                    echo "<h3 style='text-align: center'>Skater Current Season Stats</h3>";
                    echo "<table class='player-stats-table'>";
                    echo "<thead>";
                        echo "<tr>";
                            echo "<th style='width: 10%'>Player ID</th>";
                            echo "<th style='width: 12%'>Name</th>";
                            echo "<th style='width: 6%'>Pos.</th>";
                            echo "<th style='width: 5%'>GP</th>";
                            echo "<th style='width: 5%'>G</th>";
                            echo "<th style='width: 5%'>A</th>";
                            echo "<th style='width: 5%'>P</th>";
                            echo "<th style='width: 7%'>+/-</th>";
                            echo "<th style='width: 8%'>Shots</th>";
                            echo "<th style='width: 10%'>Shot %</th>";
                            echo "<th style='width: 10%'>Avg TOI (min)</th>";
                            echo "<th style='width: 10%'>Avg Shifts/Gm</th>";
                            echo "<th style='width: 7%'>FO %</th>";
                        echo "</tr>";
                    echo "</thead>";
                
              
            while ($row = mysqli_fetch_assoc($result)) {

                // print_r($row);

                $teamID = $team_id;
                $playerID = $row['playerID'];
                // echo "Player ID: " . $playerID;
                $firstName = $row['firstName'];
                $lastName = $row['lastName'];
                $positionCode = $row['positionCode'];
                $seasonsGamesPlayed = $row['seasonGamesPlayed'];
                $seasonGoals = $row['seasonGoals'];
                $seasonAssists = $row['seasonAssists'];
                $seasonPoints = $row['seasonPoints'];
                $seasonPlusMinus = $row['seasonPlusMinus'] !== null && $row['seasonPlusMinus'] !== '' ? $row['seasonPlusMinus'] : "N/A";
                $seasonShots = $row['seasonShots'];
                $seasonShootingPct = $row['seasonShootingPct'];
                $seasonAvgTOI = $row['seasonAvgTOI'];
                $seasonAvgTOI = gmdate("i:s", (int) $seasonAvgTOI); // Convert seconds to minutes:seconds format
                $seasonAvgShifts = $row['seasonAvgShifts'];
                $seasonFOWinPct = number_format((float) $row['seasonFOWinPct']*100, 1);
                $seasonGS = $row['seasonGS'];
                $seasonWins = $row['seasonWins'];
                $seasonLosses = $row['seasonLosses'];
                $seasonTies = $row['seasonTies'];
                $seasonOTLosses = $row['seasonOTLosses'];
                $seasonGAA = $row['seasonGAA'];
                $seasonSavePct = $row['seasonSavePct'];
                $seasonSA = $row['seasonSA'];
                $seasonSaves = $row['seasonSaves'];
                $seasonGA = $row['seasonGA'];
                $seasonSO = $row['seasonSO'];
                $seasonTOI = $row['seasonTOI'];

                if ($row['positionCode'] != 'G') {
                    echo "<tr>";
                    echo "<td><a href='player_details.php?player_id=" . $playerID . "'" . "</a>" . $playerID . "</td>";
                    echo "<td>" . $firstName . " " . $lastName . "</td>";
                    echo "<td>" . $positionCode . "</td>";
                    echo "<td>" . $seasonsGamesPlayed . "</td>";
                    echo "<td>" . $seasonGoals . "</td>";
                    echo "<td>" . $seasonAssists . "</td>";
                    echo "<td>" . $seasonPoints . "</td>";
                    // var_dump($row); "<br><br>";
                    echo "<td>" . $seasonPlusMinus . "</td>";
                    echo "<td>" . $seasonShots . "</td>";
                    echo "<td>" . number_format((float) $seasonShootingPct*100, 1) . "</td>";
                    // $seasonAvgTOI_total_secs = (float) $seasonAvgTOI;
                    // $seasonAvgTOI_mins = (int) floor(($seasonAvgTOI_total_secs / 60));
                    // $seasonAvg_remaining_secs = (int) ($seasonAvgTOI_total_secs % 60);
                    // $formatted_seasonAvgTOI = sprintf("%02d:%02d", $seasonAvgTOI_mins, $seasonAvg_remaining_secs);
                    echo "<td>" . $seasonAvgTOI . "</td>";
                    echo "<td>" . number_format((float) $seasonAvgShifts, 1) . "</td>";
                    echo "<td>" . $seasonFOWinPct . "</td>";
                echo "</tr>";

                }
                    
            }
            echo "</table>";
            echo "</div>";
            echo "<br>";
          }

          
          ?>
          

        <footer class="bg-body-tertiary">
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
    </body>
</html>