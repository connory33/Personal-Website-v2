<?php include('db_connection.php'); ?>
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

        <?php

          ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ALL);

    ########## GET ALL TEAM DATA TO USE FOR THE PAGE ##########

        // $teamColors = [
        //   "ANA" => ['#F47A38', '#B9975B', '#C1C6C8', '#000']
        // ]

          // Check if the 'team_id' is passed in the URL
          if (isset($_GET['team_id'])) {
            $team_id = $_GET['team_id'];

            $skaters_sql = "SELECT 
                              teams.*,
                              latest_stats.*,
                              season_stats.*
                            FROM nhl_teams AS teams
                            LEFT JOIN team_latest_stats AS latest_stats
                              ON teams.id = latest_stats.teamID
                            LEFT JOIN team_season_stats AS season_stats
                              ON teams.id = season_stats.teamID
                            WHERE teams.id = $team_id AND season_stats.positionCode != 'G'
                            GROUP BY season_stats.playerID
                            ORDER BY season_stats.seasonID DESC";

            $goalies_sql = "SELECT 
                              teams.*,
                              latest_stats.*,
                              season_stats.*
                            FROM nhl_teams AS teams
                            LEFT JOIN team_latest_stats AS latest_stats
                              ON teams.id = latest_stats.teamID
                            LEFT JOIN team_season_stats AS season_stats
                              ON teams.id = season_stats.teamID
                            WHERE teams.id = $team_id AND season_stats.positionCode = 'G'
                            GROUP BY season_stats.playerID
                            ORDER BY season_stats.seasonID DESC";
              
            $result_skaters = mysqli_query($conn, $skaters_sql);
            $result_goalies = mysqli_query($conn, $goalies_sql);

            if (!$result_skaters || !$result_goalies) {
              die("Query failed: " . mysqli_error($conn));
            } elseif (mysqli_num_rows($result_skaters) == 0 and mysqli_num_rows($result_goalies) == 0) {
              echo "No players found for this team.";
            } else {
              // Fetch the row to get the team logo amd build header
              $team = mysqli_fetch_assoc($result_skaters);

              $teamColor1 = $team['teamColor1'];
              $teamColor2 = $team['teamColor2'];
              $teamColor3 = $team['teamColor3'];
              $teamColor4 = $team['teamColor4'];
              $teamColor5 = $team['teamColor5'];
              ?>
              <div class="text-white">
              <?php
              // echo "<div class='container; background-color: $teamColor1'>";
              echo "<div style='padding-left: 10px; padding-right: 10px; padding-top: 25px'>";
              ### Flexbox for player name/number/active/id and headshot/team logo - aligns them side-by-side ###
              echo "<div class='team-header' style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border: 2px solid $teamColor1; 
              padding-left: 15px; padding-right: 15px; padding-top: 10px; padding-bottom: 10px; background-color: $teamColor2; border-radius: 10px;
              width: 70%; margin: auto;'>";
                // Left side: Team Name
                echo "<div>";
                    echo "<h1 style='margin-top: 3px; color:$teamColor1'>Team Details: </h1>" . "<h1 style='color: $teamColor1'>" . $team['fullName'] . "</h1>";
                echo "</div>";
                // Right side: Team Logo
                $teamLogo = $team['teamLogo'];
                echo "<div style='display: flex; align-items: center; gap: 5px'>";
                  if ($teamLogo != 'false' and $teamLogo != '' and $teamLogo != 'N/A') {
                      echo "<img src='" . htmlspecialchars($teamLogo) . "' alt='team logo' style='height: 120px'>";
                  } else {
                      echo "<p></p>";
                  }
                echo "</div>";
              echo "</div>";
              mysqli_data_seek($result_skaters, 0); // Reset the result pointer to the first row
              }

              // Step 1: Get all unique seasons for the dropdown
              $seasons = [];
              mysqli_data_seek($result_skaters, 0); // Reset the pointer to the start of the result
              while ($row = mysqli_fetch_assoc($result_skaters)) {
                  $seasonID = $row['seasonID'];
                  if (!in_array($seasonID, $seasons)) {
                      $seasons[] = $seasonID; // Add unique seasonID to the array
                  }
              }
              rsort($seasons); // Sort seasons in descending order to show the latest season first
              ?>
              <br><br>
              <!-- Step 2: Add Dropdown for Season Selection -->
              <div class="container filter-container" style='border: 1px solid <?php echo $teamColor2; ?>; border-radius: 5px; background-color: <?php echo $teamColor1; ?>; margin: auto;'>
                  <label for="seasonDropdown" style="margin-top:3px">Filter by Season:</label>
                  <select id="seasonDropdown" style="border: 1px solid $teamColor2">;
                      <?php foreach ($seasons as $seasonID): ?>
                          <?php 
                              $seasonYear1 = substr($seasonID, 0, 4);
                              $seasonYear2 = substr($seasonID, 4, 4);
                          ?>
                          <option value="<?php echo $seasonID; ?>">
                              <?php echo $seasonYear1 . "-" . $seasonYear2; ?>
                          </option>
                      <?php endforeach; ?>
                  </select>
              </div>
                        <br>
              <?php
              echo "<h3 style='text-align: center; color: $teamColor2'>Skater Season Stats</h3>";
              echo "<table class='player-stats-table' style='color: black; border: 2px solid $teamColor2'>";
              echo "<colgroup>";
              echo "<col class='season-col-skater-stats'>";
              echo "<col class='gameType-col-skater-stats'>";
              echo "<col class='name-col-skater-stats'>";
              echo "<col class='position-col-skater-stats'>";
              echo "<col class='gp-col-skater-stats'>";
              echo "<col class='goals-season-col-skater-stats'>";
              echo "<col class='assists-season-col-skater-stats'>";
              echo "<col class='points-season-col-skater-stats'>";
              echo "<col class='plusMinus-season-col-skater-stats'>";
              echo "<col class='shots-col-skater-stats'>";
              echo "<col class='shotPct-col-skater-stats'>";
              echo "<col class='avgTOI-col-skater-stats'>";
              echo "<col class='avgShifts-col-skater-stats'>";
              echo "<col class='FOPct-col-skater-stats'>";
              echo "</colgroup>";
              echo "<thead>";
                  echo "<tr style='background-color: $teamColor1; border: 2px solid $teamColor2; color: $teamColor2'>";
                      echo "<th style='width: 9%'>Season</th>";
                      echo "<th style='width: 9%'>Game Type</th>";
                      // echo "<th style='width: 9%'>Player ID</th>";
                      echo "<th>Name</th>";
                      echo "<th>Pos.</th>";
                      echo "<th>GP</th>";
                      echo "<th>G</th>";
                      echo "<th>A</th>";
                      echo "<th>P</th>";
                      echo "<th>+/-</th>";
                      echo "<th>Shots</th>";
                      echo "<th>Shot %</th>";
                      echo "<th>Avg TOI (min)</th>";
                      echo "<th>Avg Shifts/Gm</th>";
                      echo "<th>FO %</th>";
                  echo "</tr>";
              echo "</thead>";
              $skater_count = mysqli_num_rows($result_skaters);
              echo "<tbody id='skaterStatsTable'>";
              if ($skater_count > 0) {
                mysqli_data_seek($result_skaters, 0); // Reset pointer again for the main table loop
                
                while ($row = mysqli_fetch_assoc($result_skaters)) {

                  // print_r($row);

                  $seasonID = $row['seasonID'];
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
                  $seasonPlusMinus = $row['seasonPlusMinus'] !== null && $row['seasonPlusMinus'] !== '' ? $row['seasonPlusMinus'] : "-";
                  $seasonShots = $row['seasonShots'];
                  $seasonShootingPct = $row['seasonShootingPct'];
                  $seasonAvgTOI = $row['seasonAvgTOI'];
                  $seasonAvgTOI = gmdate("i:s", (int) $seasonAvgTOI); // Convert seconds to minutes:seconds format
                  $seasonAvgShifts = $row['seasonAvgShifts'];
                  $seasonFOWinPct = number_format((float) $row['seasonFOWinPct']*100, 1);
                  if ($positionCode == 'G') {
                    continue; // Skip goalies in the skater stats table
                  } else {
                    echo "<tr data-season='$seasonID'>"; // Add data attribute for filtering
                    $seasonYear1 = substr($seasonID, 0, 4); // Extract the year from the seasonID
                    $seasonYear2 = substr($seasonID, 4, 4); // Extract the year from the seasonID
                          echo "<td>" . $seasonYear1 . "-" . $seasonYear2 . "</td>";
                          $gameType = substr($seasonID, 9, 1); // Extract the game type from the seasonID
                          if ($gameType == 1) {
                            $gameType = 'Pre.';
                          } elseif ($gameType == 2) {
                            $gameType = 'Reg.';
                          } elseif ($gameType == 3) {
                            $gameType = 'Post.';
                          }
                          echo "<td>" . $gameType . "</td>";
                          // echo "<td><a href='player_details.php?player_id=" . $playerID . "'" . "</a>" . $playerID . "</td>";
                          echo "<td><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $playerID . "'" . "</a>" . $firstName . " " . $lastName . "</td>";
                          // echo "<td>" . $firstName . " " . $lastName . "</td>";
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
              }
              echo "</tbody>";
              echo "</table>";
              echo "<br>";


              ### GOALIE CURRENT SEASON STATS TABLE ###
              echo "<h3 style='text-align: center; color: $teamColor2'>Goalie Season Stats</h3>";
              echo "<table class='goalie-stats-table' style='color: black; border: 2px solid $teamColor2'>";
              echo "<colgroup>";
              echo "<col class='season-col-goalie-stats'>";
              echo "<col class='gameType-col-goalie-stats'>";
              echo "<col class='name-col-goalie-stats'>";
              echo "<col class='gp-col-goalie-stats'>";
              echo "<col class='gs-season-col-goalie-stats'>";
              echo "<col class='wins-season-col-goalie-stats'>";
              echo "<col class='losses-season-col-goalie-stats'>";
              echo "<col class='ties-season-col-goalie-stats'>";
              echo "<col class='otlosses-col-goalie-stats'>";
              echo "<col class='GAA-col-goalie-stats'>";
              echo "<col class='svPct-col-goalie-stats'>";
              echo "<col class='SA-col-goalie-stats'>";
              echo "<col class='saves-col-goalie-stats'>";
              echo "<col class='GA-col-goalie-stats'>";
              echo "<col class='SO-col-goalie-stats'>";
              echo "<col class='TOI-col-goalie-stats'>";
              echo "</colgroup>";
                echo "<thead>";
                  echo "<tr style='background-color: $teamColor1; border: 2px solid $teamColor2; color: $teamColor2'>";
                    echo "<th>Season</th>";
                    echo "<th>Game Type</th>";
                    echo "<th>Name</th>";
                    echo "<th>GP</th>";
                    echo "<th>GS</th>";
                    echo "<th>W</th>";
                    echo "<th>L</th>";
                    echo "<th>T</th>";
                    echo "<th>OTL</th>";
                    echo "<th>GAA</th>";
                    echo "<th>Sv. %</th>";
                    echo "<th>SA</th>";
                    echo "<th>Saves</th>";
                    echo "<th>GA</th>";
                    echo "<th>SO</th>";
                    echo "<th>TOI (min)</th>";
                  echo "</tr>";
              echo "</thead>";
              // $countSQL = "SELECT * FROM team_season_stats WHERE teamID = $team_id AND positionCode = 'G' AND seasonID = '$seasonID'";
              // $result_count = mysqli_query($conn, $countSQL);
              // $goalie_count = mysqli_num_rows($result_count);
              $goalie_count = 1; // Placeholder for goalie count
              // echo $result_count;
              echo "<tbody id='goalieStatsTable'>";

              if ($goalie_count > 0) {
                // Reset pointer for the main table loop
                mysqli_data_seek($result_goalies, 0);

                while ($row = mysqli_fetch_assoc($result_goalies)) {
                  $seasonID = $row['seasonID'];
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

                  if ($positionCode == 'G') {
                      echo "<tr data-season=$seasonID>"; // Add data attribute for filtering
                      $seasonYear1 = substr($seasonID, 0, 4); // Extract the year from the seasonID
                      $seasonYear2 = substr($seasonID, 4, 4); // Extract the year from the seasonID
                      echo "<td>" . $seasonYear1 . "-" . $seasonYear2 . "</td>";
                      $gameType = substr($seasonID, 9, 1); // Extract the game type from the seasonID
                      if ($gameType == 1) {
                        $gameType = 'Pre.';
                      } elseif ($gameType == 2) {
                        $gameType = 'Reg.';
                      } elseif ($gameType == 3) {
                        $gameType = 'Post.';
                      }
                      echo "<td>" . $gameType . "</td>";
                      // echo "<td><a href='player_details.php?player_id=" . $playerID . "'" . "</a>" . $playerID . "</td>";
                      echo "<td><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $playerID . "'" . "</a>" . $firstName . " " . $lastName . "</td>";
                      // echo "<td>" . $firstName . " " . $lastName . "</td>";
                      // echo "<td>" . $positionCode . "</td>";
                      echo "<td>" . $seasonsGamesPlayed . "</td>";
                      echo "<td>" . $seasonGS . "</td>";
                      echo "<td>" . $seasonWins . "</td>";
                      echo "<td>" . $seasonLosses . "</td>";
                      echo "<td>" . $seasonTies . "</td>";
                      echo "<td>" . $seasonOTLosses . "</td>";
                      echo "<td>" . number_format((float) $seasonGAA, 2) . "</td>";
                      echo "<td>" . number_format((float) $seasonSavePct, 3) . "</td>";
                      echo "<td>" . $seasonSA . "</td>";
                      echo "<td>" . $seasonSaves . "</td>";
                      echo "<td>" . $seasonGA . "</td>";
                      echo "<td>" . $seasonSO. "</td>";
                      echo "<td>" . gmdate("i:s", (int) $seasonTOI) . "</td>";
                      echo "</tr>";
                    }
                  }
              } else {
                    echo "<tr>";
                    echo "<td colspan='15' style='text-align: center;'>No goalies found for this team.</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                // echo "</div>";
              }
            
          ?>
          <br><br>
          <p style='color: black; text-align: center'>Select any team below to view details:</p>
              <div class="container footer-teams" style="text-align: center; color: black; background-color: $teamColor3">
              <a href='https://connoryoung.com/team_details.php?team_id=24'>ANA</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=53'>ARI</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=6'>BOS</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=7'>BUF</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=20'>CGY</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=12'>CAR</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=16'>CHI</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=21'>COL</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=29'>CBJ</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=25'>DAL</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=17'>DET</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=22'>EDM</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=13'>FLA</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=26'>LAK</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=30'>MIN</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=8'>MTL</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=1'>NJD</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=18'>NSH</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=2'>NYI</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=3'>NYR</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=9'>OTT</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=4'>PHI</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=5'>PIT</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=28'>SJS</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=55'>SEA</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=19'>STL</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=14'>TBL</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=10'>TOR</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=59'>UTA</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=23'>VAN</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=54'>VGK</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=52'>WPG</a><span style='color: <?php echo $teamColor1; ?>'> |</span>
              <a href='https://connoryoung.com/team_details.php?team_id=15'>WSH</a>

              </div>

          <br><br>
          
          
      
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
            <script>
                  document.addEventListener('DOMContentLoaded', function () {
                  const dropdown = document.getElementById('seasonDropdown');
                  const skaterRows = document.querySelectorAll('#skaterStatsTable tr');
                  const goalieRows = document.querySelectorAll('#goalieStatsTable tr');

                  // Function to filter rows by season
                  function filterTableBySeason(seasonID) {
                    // console.log("Filtering by season:", seasonID);

                      // Filter skater rows
                      skaterRows.forEach(row => {
                          if (row.dataset.season === seasonID) {
                              row.style.display = ''; // Show row
                          } else {
                              row.style.display = 'none'; // Hide row
                          }
                      });
                      
                      // Filter goalie rows
                      goalieRows.forEach(row => {
                          if (row.dataset.season === seasonID) {
                              row.style.display = ''; // Show row
                          } else {
                              row.style.display = 'none'; // Hide row
                          }
                      });
                  }

                  // Set default season to the first option in the dropdown
                  const defaultSeason = dropdown.value;
                  filterTableBySeason(defaultSeason);

                  // Add event listener to dropdown
                  dropdown.addEventListener('change', function () {
                      filterTableBySeason(this.value);
                  });
              });
            </script>
    </body>
</html>