
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

        <!-- <script src="https://cdn.tailwindcss.com"></script> -->

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

            // $skaters_sql = "SELECT 
            //                   teams.*,
            //                   latest_stats.*,
            //                   season_stats.*
            //                 FROM nhl_teams AS teams
            //                 LEFT JOIN team_latest_stats AS latest_stats
            //                   ON teams.id = latest_stats.teamID
            //                 LEFT JOIN team_season_stats AS season_stats
            //                   ON teams.id = season_stats.teamID
            //                 WHERE teams.id = $team_id AND season_stats.positionCode != 'G'
            //                 GROUP BY season_stats.playerID
            //                 ORDER BY season_stats.seasonID DESC";

            // $goalies_sql = "SELECT 
            //                   teams.*,
            //                   latest_stats.*,
            //                   season_stats.*
            //                 FROM nhl_teams AS teams
            //                 LEFT JOIN team_latest_stats AS latest_stats
            //                   ON teams.id = latest_stats.teamID
            //                 LEFT JOIN team_season_stats AS season_stats
            //                   ON teams.id = season_stats.teamID
            //                 WHERE teams.id = $team_id AND season_stats.positionCode = 'G'
            //                 GROUP BY season_stats.playerID
            //                 ORDER BY season_stats.seasonID DESC";

            // $rosters_sql = " SELECT team_id, team_triCode, position, player_id, firstName, lastName, season
            //             FROM (
            //                 -- Forward block
            //                 SELECT 
            //                   team_season_rosters.team_id,
            //                   team_season_rosters.team_triCode,
            //                   team_season_rosters.season,
            //                   'forward' AS position,
            //                   exploded_forwards.player_id,
            //                   nhl_players.firstName,
            //                   nhl_players.lastName
            //                 FROM team_season_rosters
            //                 -- JSON_TABLE explodes list into individual rows to JOIN on
            //                 JOIN JSON_TABLE(team_season_rosters.forwards, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_forwards
            //                   ON 1=1
            //                 JOIN nhl_players ON nhl_players.playerID = exploded_forwards.player_id
            //                 WHERE team_season_rosters.team_id = $team_id

            //                 UNION ALL

            //                 -- Defense block
            //                 SELECT 
            //                   team_season_rosters.team_id,
            //                   team_season_rosters.team_triCode,
            //                   team_season_rosters.season,
            //                   'defenseman' AS position,
            //                   exploded_defensemen.player_id,
            //                   nhl_players.firstName,
            //                   nhl_players.lastName
            //                 FROM team_season_rosters
            //                 JOIN JSON_TABLE(team_season_rosters.defensemen, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_defensemen
            //                   ON 1=1
            //                 JOIN nhl_players ON nhl_players.playerID = exploded_defensemen.player_id
            //                 WHERE team_season_rosters.team_id = $team_id

            //                 UNION ALL

            //                 -- Goalie block
            //                 SELECT 
            //                   team_season_rosters.team_id,
            //                   team_season_rosters.team_triCode,
            //                   team_season_rosters.season,
            //                   'goalie' AS position,
            //                   exploded_goalies.player_id,
            //                   nhl_players.firstName,
            //                   nhl_players.lastName
            //                 FROM team_season_rosters
            //                 JOIN JSON_TABLE(team_season_rosters.goalies, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_goalies
            //                   ON 1=1
            //                 JOIN nhl_players ON nhl_players.playerID = exploded_goalies.player_id
            //                 WHERE team_season_rosters.team_id = $team_id
            //             ) AS roster_view
            //             ";
            // Combined query for skaters (forwards and defensemen)
            $skaters_combined_sql = "
                                SELECT 
                                    roster.team_id,
                                    roster.team_triCode,
                                    roster.position,
                                    roster.player_id,
                                    roster.firstName,
                                    roster.lastName,
                                    roster.season,
                                    CONCAT(roster.season, '-2') as seasonWithType,
                                    teams.*,
                                    stats.seasonGamesPlayed,
                                    stats.seasonGoals,
                                    stats.seasonAssists,
                                    stats.seasonPoints, 
                                    stats.seasonPlusMinus,
                                    stats.seasonShots,
                                    stats.seasonShootingPct,
                                    stats.seasonAvgTOI,
                                    stats.seasonAvgShifts,
                                    stats.seasonFOWinPct
                                FROM 
                                (
                                    -- Forward block
                                    SELECT 
                                        team_season_rosters.team_id,
                                        team_season_rosters.team_triCode,
                                        team_season_rosters.season,
                                        nhl_players.position,
                                        exploded_forwards.player_id,
                                        nhl_players.firstName,
                                        nhl_players.lastName
                                    FROM team_season_rosters
                                    JOIN JSON_TABLE(team_season_rosters.forwards, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_forwards
                                        ON 1=1
                                    JOIN nhl_players ON nhl_players.playerID = exploded_forwards.player_id
                                    WHERE team_season_rosters.team_id = $team_id

                                    UNION ALL

                                    -- Defense block
                                    SELECT 
                                        team_season_rosters.team_id,
                                        team_season_rosters.team_triCode,
                                        team_season_rosters.season,
                                        nhl_players.position,
                                        exploded_defensemen.player_id,
                                        nhl_players.firstName,
                                        nhl_players.lastName
                                    FROM team_season_rosters
                                    JOIN JSON_TABLE(team_season_rosters.defensemen, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_defensemen
                                        ON 1=1
                                    JOIN nhl_players ON nhl_players.playerID = exploded_defensemen.player_id
                                    WHERE team_season_rosters.team_id = $team_id
                                ) AS roster

                                LEFT JOIN nhl_teams AS teams ON teams.id = roster.team_id
                                LEFT JOIN team_season_stats AS stats 
                                    ON stats.teamID = roster.team_id 
                                    AND stats.playerID = roster.player_id 
                                    AND CONCAT(roster.season, '-2') = stats.seasonID
                                ORDER BY roster.season DESC, roster.lastName
                            ";

            // Combined query for goalies
            $goalies_combined_sql = "
                                    SELECT 
                                        roster.team_id,
                                        roster.team_triCode,
                                        roster.position,
                                        roster.player_id,
                                        roster.firstName,
                                        roster.lastName,
                                        roster.season,
                                        CONCAT(roster.season, '-2') as seasonWithType,
                                        teams.*,
                                        stats.seasonGamesPlayed,
                                        stats.seasonGS,
                                        stats.seasonWins,
                                        stats.seasonLosses,
                                        stats.seasonTies,
                                        stats.seasonOTLosses,
                                        stats.seasonGAA,
                                        stats.seasonSavePct,
                                        stats.seasonSA,
                                        stats.seasonSaves,
                                        stats.seasonGA,
                                        stats.seasonSO,
                                        stats.seasonTOI
                                    FROM (
                                        -- Goalie block
                                        SELECT 
                                            team_season_rosters.team_id,
                                            team_season_rosters.team_triCode,
                                            team_season_rosters.season,
                                            'goalie' AS position,
                                            exploded_goalies.player_id,
                                            nhl_players.firstName,
                                            nhl_players.lastName
                                        FROM team_season_rosters
                                        JOIN JSON_TABLE(team_season_rosters.goalies, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_goalies
                                            ON 1=1
                                        JOIN nhl_players ON nhl_players.playerID = exploded_goalies.player_id
                                        WHERE team_season_rosters.team_id = $team_id
                                    ) AS roster
                                    LEFT JOIN nhl_teams AS teams ON teams.id = roster.team_id
                                    LEFT JOIN team_season_stats AS stats 
                                        ON stats.teamID = roster.team_id 
                                        AND stats.playerID = roster.player_id 
                                        AND CONCAT(roster.season, '-2') = stats.seasonID
                                    ORDER BY roster.season DESC, roster.lastName
                                ";
              
            $result_skaters_combined = mysqli_query($conn, $skaters_combined_sql);
            $result_goalies_combined = mysqli_query($conn, $goalies_combined_sql);

              
            // $result_skaters = mysqli_query($conn, $skaters_sql);
            // $result_goalies = mysqli_query($conn, $goalies_sql);
            // $result_rosters = mysqli_query($conn, $rosters_sql);

            if (!$result_skaters_combined || !$result_goalies_combined) {
              die("Query failed: " . mysqli_error($conn));
          } elseif (mysqli_num_rows($result_skaters_combined) == 0 && mysqli_num_rows($result_goalies_combined) == 0) {
              echo "No players found for this team.";
          } else {
              // Fetch the row to get the team logo and build header
              $team = mysqli_fetch_assoc($result_skaters_combined);

              $teamColor1 = $team['teamColor1'];
              $teamColor2 = $team['teamColor2'];
              $teamColor3 = $team['teamColor3'];
              $teamColor4 = $team['teamColor4'];
              $teamColor5 = $team['teamColor5'];
              ?>

              <div class="full-page-content-container">

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
              mysqli_data_seek($result_skaters_combined, 0); // Reset the result pointer to the first row
              }

              // Step 1: Get all unique seasons for the dropdown
              $seasons = [];
              // Get seasons from skaters
              mysqli_data_seek($result_skaters_combined, 0);
              while ($row = mysqli_fetch_assoc($result_skaters_combined)) {
                  $seasonID = $row['season'];
                  $seasonWithType = $row['seasonWithType']; // Format: 20242025-2
                  if (!in_array($seasonWithType, $seasons)) {
                      $seasons[] = $seasonWithType;
                  }
              }
              
              // Get seasons from goalies
              mysqli_data_seek($result_goalies_combined, 0);
              while ($row = mysqli_fetch_assoc($result_goalies_combined)) {
                  $seasonWithType = $row['seasonWithType']; // Format: 20242025-2
                  if (!in_array($seasonWithType, $seasons)) {
                      $seasons[] = $seasonWithType;
                  }
              }
              
              rsort($seasons); // Sort seasons in descending order to show the latest season first
              ?>
              <br><br>
              <!-- Step 2: Add Dropdown for Season Selection -->
              <div class="container filter-container" style='border: 1px solid <?php echo $teamColor2; ?>; border-radius: 5px; background-color: <?php echo $teamColor1; ?>;
                margin: auto; color: black'>
                  <label for="seasonDropdown">Filter by Season:</label>
                  <select id="seasonDropdown" style="border: 1px solid <?php echo $teamColor2; ?>">;
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

                <!-- SKATERS COMBINED TABLE -->
                <div class="w-full overflow-x-auto">
                    <br>
                    <h3 style='text-align: center; color: <?php echo $teamColor2; ?>'>Skaters - Season Roster & Stats</h3>
                    <table class='player-stats-table default-zebra-table min-w-[900px] table-auto' style='color: black; border: 2px solid <?php echo $teamColor2; ?>'>
                    <colgroup>
                    <col class='skaters-combined-season'>
                    <col class='skaters-combined-name'>
                    <col class='skaters-combined-position'>
                    <col class='skaters-combined-gp'>
                    <col class='skaters-combined-g'>
                    <col class='skaters-combined-a'>
                    <col class='skaters-combined-p'>
                    <col class='skaters-combined-plus-minus'>
                    <col class='skaters-combined-shots'>
                    <col class='skaters-combined-shot-pct'>
                    <col class='skaters-combined-avg-toi'>
                    <col class='skaters-combined-avg-shifts'>
                    <col class='skaters-combined-fo-pct'>
                      </colgroup>
                    <thead>
                            <tr style='background-color: <?php echo $teamColor1; ?>; color: <?php echo $teamColor2; ?>'>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>Season</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>Name</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>Pos.</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>GP</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>G</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>A</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>P</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>+/-</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>Shots</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>Shot %</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>Avg TOI</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>Avg Shifts</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>FO %</th>
                            </tr>
                        </thead>
                        <tbody id='skaterStatsTable'>
                            <?php
                            mysqli_data_seek($result_skaters_combined, 0);
                            while ($row = mysqli_fetch_assoc($result_skaters_combined)) {
                                $seasonID = $row['season'];
                                $seasonWithType = $row['seasonWithType']; // Format: 20242025-2
                                $playerID = $row['player_id'];
                                $firstName = $row['firstName'];
                                $lastName = $row['lastName'];
                                
                                // Format position display
                                $position = $row['position'];
                                if ($position == 'R') {
                                    $positionDisplay = 'RW';
                                } else if ($position == 'L') {
                                    $positionDisplay = 'LW';
                                } else if ($position == 'C') {
                                    $positionDisplay = 'C';
                                } else if ($position == 'D') {
                                    $positionDisplay = 'D';
                                } else {
                                    $positionDisplay = $position; // Keep original value if not a forward or defenseman
                                }
                                
                                // Extract season years for display
                                $seasonYear1 = substr($seasonID, 0, 4);
                                $seasonYear2 = substr($seasonID, 4, 4);
                                
                                echo "<tr data-season='$seasonWithType'>"; // For filtering by season with type
                                echo "<td>" . $seasonYear1 . "-" . $seasonYear2 . "</td>";  // Season display
                                echo "<td><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $playerID . "'>" . $firstName . " " . $lastName . "</a></td>";
                                echo "<td>" . $positionDisplay . "</td>";
                                
                                // Display stats if available, otherwise show dash
                                echo "<td>" . ($row['seasonGamesPlayed'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonGoals'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonAssists'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonPoints'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonPlusMinus'] !== null && $row['seasonPlusMinus'] !== '' ? $row['seasonPlusMinus'] : "-") . "</td>";
                                echo "<td>" . ($row['seasonShots'] ?? "-") . "</td>";
                                
                                // Handle percentages and formatting
                                if (isset($row['seasonShootingPct'])) {
                                    echo "<td>" . number_format((float) $row['seasonShootingPct']*100, 1) . "</td>";
                                } else {
                                    echo "<td>-</td>";
                                }
                                
                                // Format time on ice if available
                                if (isset($row['seasonAvgTOI'])) {
                                    echo "<td>" . gmdate("i:s", (int) $row['seasonAvgTOI']) . "</td>";
                                } else {
                                    echo "<td>-</td>";
                                }
                                
                                // Format shifts
                                if (isset($row['seasonAvgShifts'])) {
                                    echo "<td>" . number_format((float) $row['seasonAvgShifts'], 1) . "</td>";
                                } else {
                                    echo "<td>-</td>";
                                }
                                
                                // Format faceoff percentage
                                if (isset($row['seasonFOWinPct'])) {
                                    echo "<td>" . number_format((float) $row['seasonFOWinPct']*100, 1) . "</td>";
                                } else {
                                    echo "<td>-</td>";
                                }
                                
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- GOALIES COMBINED TABLE -->
                <br>
                <h3 style='text-align: center; color: <?php echo $teamColor2; ?>'>Goalies - Season Roster & Stats</h3>
                <div class="shadow-md rounded-lg overflow-x-auto">
                    <table class='goalie-stats-table default-zebra-table table-auto' style='color: black; border: 2px solid <?php echo $teamColor2; ?>'>
                    <colgroup>
                    <col class='goalies-combined-season'>
                    <col class='goalies-combined-name'>
                    <col class='goalies-combined-gp'>
                    <col class='goalies-combined-gs'>
                    <col class='goalies-combined-w'>
                    <col class='goalies-combined-l'>
                    <col class='goalies-combined-t'>
                    <col class='goalies-combined-otl'>
                    <col class='goalies-combined-gaa'>
                    <col class='goalies-combined-sv'>
                    <col class='goalies-combined-sa'>
                    <col class='goalies-combined-saves'>
                    <col class='goalies-combined-ga'>
                    <col class='goalies-combined-so'>
                    <col class='goalies-combined-toi'>
                    </colgroup>    



                    <thead>
                            <tr style='background-color: <?php echo $teamColor1; ?>; border: 2px solid <?php echo $teamColor2; ?>; color: <?php echo $teamColor2; ?>'>
                                <th>Season</th>
                                <th>Name</th>
                                <th>GP</th>
                                <th>GS</th>
                                <th>W</th>
                                <th>L</th>
                                <th>T</th>
                                <th>OTL</th>
                                <th>GAA</th>
                                <th>Sv. %</th>
                                <th>SA</th>
                                <th>Saves</th>
                                <th>GA</th>
                                <th>SO</th>
                                <th>TOI (min)</th>
                            </tr>
                        </thead>
                        <tbody id='goalieStatsTable'>
                            <?php
                            mysqli_data_seek($result_goalies_combined, 0);
                            while ($row = mysqli_fetch_assoc($result_goalies_combined)) {
                                $seasonID = $row['season'];
                                $seasonWithType = $row['seasonWithType']; // Format: 20242025-2
                                $playerID = $row['player_id'];
                                $firstName = $row['firstName'];
                                $lastName = $row['lastName'];
                                
                                // Extract season years for display
                                $seasonYear1 = substr($seasonID, 0, 4);
                                $seasonYear2 = substr($seasonID, 4, 4);
                                
                                echo "<tr data-season='$seasonWithType'>"; // For filtering by season with type
                                echo "<td>" . $seasonYear1 . "-" . $seasonYear2 . "</td>";  // Season
                                echo "<td><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $playerID . "'>" . $firstName . " " . $lastName . "</a></td>";
                                
                                // Display stats if available, otherwise show dash
                                echo "<td>" . ($row['seasonGamesPlayed'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonGS'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonWins'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonLosses'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonTies'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonOTLosses'] ?? "-") . "</td>";
                                
                                // Format GAA
                                if (isset($row['seasonGAA'])) {
                                    echo "<td>" . number_format((float) $row['seasonGAA'], 2) . "</td>";
                                } else {
                                    echo "<td>-</td>";
                                }
                                
                                // Format save percentage
                                if (isset($row['seasonSavePct'])) {
                                    echo "<td>" . number_format((float) $row['seasonSavePct'], 3) . "</td>";
                                } else {
                                    echo "<td>-</td>";
                                }
                                
                                echo "<td>" . ($row['seasonSA'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonSaves'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonGA'] ?? "-") . "</td>";
                                echo "<td>" . ($row['seasonSO'] ?? "-") . "</td>";
                                
                                // Format TOI
                                if (isset($row['seasonTOI'])) {
                                    echo "<td>" . gmdate("i:s", (int) $row['seasonTOI']) . "</td>";
                                } else {
                                    echo "<td>-</td>";
                                }
                                
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <br>
            <hr style='border-color: <?php echo $teamColor1 ?>; width: 70%; margin: auto; border-width: 2px;'>

            <?php
            
        } else {
            echo "<div class='container'><div class='alert alert-warning'>No team ID provided. Please select a team.</div></div>";
        }
        // Close database connection
        mysqli_close($conn);
          ?>
          <br>
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

          <br>            
          <hr style='border-color: <?php echo $teamColor1 ?>; width: 70%; margin: auto; border-width: 2px;'>
          <br>
          
          
      
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
                  const rosterRows = document.querySelectorAll('#seasonRosterTable tr');

                  // // Debug information
                  // console.log('Season dropdown:', dropdown ? dropdown.value : 'Not found');
                  // console.log('Skater rows found:', skaterRows.length);
                  // console.log('Goalie rows found:', goalieRows.length);
                  // console.log('Roster rows found:', rosterRows.length);

                  // // Debug data attributes
                  // console.log('Skater seasons:', Array.from(skaterRows).map(row => row.dataset.season).filter(Boolean));
                  // console.log('Goalie seasons:', Array.from(goalieRows).map(row => row.dataset.season).filter(Boolean));
                  // console.log('Roster seasons:', Array.from(rosterRows).map(row => row.dataset.season).filter(Boolean));

                  // Function to filter rows by season
                  function filterTableBySeason(seasonID) {
                      console.log("Filtering by season:", seasonID);

                      const baseSeasonID = seasonID.split('-')[0]; // "20242025-2" becomes "20242025" - needed for roster table filtering

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
                          if (row.dataset.season === seasonID || row.classList.contains('no-data-row')) {
                              row.style.display = ''; // Show row
                          } else {
                              row.style.display = 'none'; // Hide row
                          }
                      });

                      // Filter roster rows
                      rosterRows.forEach(row => {
                          if (row.dataset.season === seasonID || row.dataset.season === baseSeasonID) {
                              row.style.display = ''; // Show row
                          } else {
                              row.style.display = 'none'; // Hide row
                          }
                      });
                  }

                  // Set default season to the first option in the dropdown
                  if (dropdown) {
                      const defaultSeason = dropdown.value;
                      console.log("Setting default season:", defaultSeason);
                      filterTableBySeason(defaultSeason);

                      // Add event listener to dropdown
                      dropdown.addEventListener('change', function () {
                          console.log("Dropdown changed to:", this.value);
                          filterTableBySeason(this.value);
                      });
                  } else {
                      console.error("Season dropdown not found!");
                  }
              });
                          </script>
    </body>
</html>