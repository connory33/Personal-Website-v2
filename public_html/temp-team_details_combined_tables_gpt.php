<?php include('db_connection.php'); ?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
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

          // Last updated: 2025-04-24 00:37:02
          // Author: connory33

          // Check if the 'team_id' is passed in the URL
          if (isset($_GET['team_id'])) {
            $team_id = $_GET['team_id'];

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
                                        'forward' AS position,
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
                                        'defenseman' AS position,
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
                                ORDER BY roster.season DESC, roster.position, roster.lastName
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

            // echo "<script>
            //     console.log('Skater query result rows: " . mysqli_num_rows($result_skaters_combined) . "');
            //     console.log('Goalie query result rows: " . mysqli_num_rows($result_goalies_combined) . "');
            // </script>";

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

                <div id="team-details-content-container">

                <?php
                echo "<div style='padding-left: 10px; padding-right: 10px; padding-top: 25px'>";
                ### Flexbox for team details header ###
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
                    if ($teamLogo != 'false' && $teamLogo != '' && $teamLogo != 'N/A') {
                        echo "<img src='" . htmlspecialchars($teamLogo) . "' alt='team logo' style='height: 120px'>";
                    } else {
                        echo "<p></p>";
                    }
                    echo "</div>";
                echo "</div>";
                mysqli_data_seek($result_skaters_combined, 0); // Reset the result pointer to the first row
                
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
                
                // Sort seasons in descending order
                rsort($seasons);
                ?>
                
                <br><br>
                <!-- Season Dropdown for Filtering -->
                <div class="container filter-container" style='border: 1px solid <?php echo $teamColor2; ?>; border-radius: 5px; background-color: <?php echo $teamColor1; ?>;
                    margin: auto; color: black'>
                    <label for="seasonDropdown">Filter by Season:</label>
                    <select id="seasonDropdown" style="border: 1px solid <?php echo $teamColor2; ?>">
                        <?php foreach ($seasons as $season): ?>
                            <?php 
                                $seasonYear1 = substr($season, 0, 4);
                                $seasonYear2 = substr($season, 4, 4);
                                $gameType = substr($season, 9, 1);
                                
                                $gameTypeLabel = '';
                                if ($gameType == '1') {
                                    $gameTypeLabel = ' (Preseason)';
                                } else if ($gameType == '2') {
                                    $gameTypeLabel = ' (Regular)';
                                } else if ($gameType == '3') {
                                    $gameTypeLabel = ' (Playoffs)';
                                }
                            ?>
                            <option value="<?php echo $season; ?>">
                                <?php echo $seasonYear1 . "-" . $seasonYear2 . $gameTypeLabel; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- SKATERS COMBINED TABLE -->
                <div id="skaters-combined-table-container" class="w-full overflow-x-auto">
                    <br>
                    <h3 style='text-align: center; color: <?php echo $teamColor2; ?>'>Skaters - Season Roster & Stats</h3>
                    <table class='player-stats-table w-full min-w-[900px] table-auto' style='color: black; border: 2px solid <?php echo $teamColor2; ?>'>
                        <thead>
                            <tr style='background-color: <?php echo $teamColor1; ?>; color: <?php echo $teamColor2; ?>'>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>Season</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>Name</th>
                                <th style='border-bottom: 2px solid <?php echo $teamColor2; ?>'>Position</th>
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
                                if ($position == 'forward') {
                                    $positionDisplay = 'F';
                                } else if ($position == 'defenseman') {
                                    $positionDisplay = 'D';
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
                    <table class='goalie-stats-table table-auto' style='color: black; border: 2px solid <?php echo $teamColor2; ?>'>
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
            
            <br><br>
            <p style='color: black; text-align: center'>Select any team below to view details:</p>
            <div class="container footer-teams" style="text-align: center; color: black; background-color: <?php echo $teamColor3; ?>">
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
            <?php
                // Close database connection
                mysqli_close($conn);
            }
        } else {
            echo "<div class='container'><div class='alert alert-warning'>No team ID provided. Please select a team.</div></div>";
        }
        ?>
        
        <br><br>
        <footer class="bg-body-tertiary">
            <div class="container">
                <p class="float-right">
                <a href="#">Back to top</a>
                </p>
                <p>Copyright &copy; 2025 Connor Young</p>
            </div>
        </footer>
        
        <!-- Bootstrap core JavaScript -->
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
            
            console.log('Season dropdown:', dropdown ? dropdown.value : 'Not found');
            console.log('Skater rows found:', skaterRows.length);
            console.log('Goalie rows found:', goalieRows.length);

            // Function to filter rows by season
            function filterTableBySeason(seasonID) {
                console.log("Filtering by season:", seasonID);
                
                // Filter skater rows
                let visibleSkaters = 0;
                skaterRows.forEach(row => {
                    if (row.dataset.season === seasonID) {
                        row.style.display = ''; // Show row
                        visibleSkaters++;
                    } else {
                        row.style.display = 'none'; // Hide row
                    }
                });
                console.log(`Visible skaters: ${visibleSkaters}`);
                
                // Filter goalie rows
                let visibleGoalies = 0;
                goalieRows.forEach(row => {
                    if (row.dataset.season === seasonID || row.classList.contains('no-data-row')) {
                        row.style.display = ''; // Show row
                        visibleGoalies++;
                    } else {
                        row.style.display = 'none'; // Hide row
                    }
                });
                console.log(`Visible goalies: ${visibleGoalies}`);
                
                // Add no-data messages if needed
                if (visibleSkaters === 0) {
                    const skaterNoData = document.querySelector('#skaterStatsTable .no-data-row');
                    if (!skaterNoData) {
                        const newRow = document.createElement('tr');
                        newRow.className = 'no-data-row';
                        newRow.innerHTML = `<td colspan="13" style="text-align:center; padding: 20px;">No skaters found for this season</td>`;
                        document.querySelector('#skaterStatsTable').appendChild(newRow);
                    }
                }
                
                if (visibleGoalies === 0) {
                    const goalieNoData = document.querySelector('#goalieStatsTable .no-data-row');
                    if (!goalieNoData) {
                        const newRow = document.createElement('tr');
                        newRow.className = 'no-data-row';
                        newRow.innerHTML = `<td colspan="15" style="text-align:center; padding: 20px;">No goalies found for this season</td>`;
                        document.querySelector('#goalieStatsTable').appendChild(newRow);
                    }
                }
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






OLD:
<!-- ROSTERS BY SEASON TABLE -->

<div id='team-rosters-table-container' class="w-full overflow-x-auto">
                <br>
              <h3 style='text-align: center; color: $teamColor2'>Season Roster</h3>
                <table class='season-roster-table w-full min-w-[900px] table-auto' style='color: black; border: 2px solid <?php echo $teamColor2 ?>'>
                  <colgroup>
                    <col class='season-col-roster'>
                    <col class='playerID-col-roster'>
                    <col class='name-col-roster'>
                    <col class='position-col-roster'>
                  </colgroup>
                  <thead>
                    <tr style='background-color: <?php echo $teamColor1 ?>; color: <?php echo $teamColor2 ?>'>
                      <th style='border-bottom: 2px solid <?php echo $teamColor2 ?>'>Season</th>
                      <th style='border-bottom: 2px solid <?php echo $teamColor2 ?>'>Player ID</th>
                      <th style='border-bottom: 2px solid <?php echo $teamColor2 ?>'>Name</th>
                      <th style='border-bottom: 2px solid <?php echo $teamColor2 ?>'>Position</th>
                    </tr>
                  </thead>
                  <tbody id='seasonRosterTable'>

              <?php
              while ($row = mysqli_fetch_assoc($result_rosters)) {
                // print_r($row);
                $playerID = $row['player_id'];
                $firstName = $row['firstName'];
                $lastName = $row['lastName'];
                $position = $row['position'];
                $seasonID = $row['season'];

                // echo "<script>console.log('SeasonID for player " . $playerID . ": " . $seasonID . "');</script>";

                echo "<tr data-season='$seasonID'>";
                  $seasonYear1 = substr($seasonID, 0, 4); // Extract the year from the seasonID
                  $seasonYear2 = substr($seasonID, 4, 4); // Extract the year from the seasonID
                  echo "<td>" . $seasonYear1 . "-" . $seasonYear2 . "</td>";  // Season
                  echo "<td>" . $playerID . "</td>";  // Player ID
                  echo "<td>" . $firstName . ' ' . $lastName . "</td>";  // Name
                  if ($position == 'forward') {
                    $position = 'F';
                  } elseif ($position == 'defenseman') {
                    $position = 'D';
                  } elseif ($position == 'goalie') {
                    $position = 'G';
                  }
                  echo "<td>" . $position . "</td>";  // Position
                
                echo "</tr>";
              }
              echo "</tbody>";
              echo "</table>";
              echo "</div>";
              ?>
              
              

              <div id='player-goalie-stats-table-container' class="w-full overflow-x-auto">
                          <br>
                <?php
                echo "<h3 style='text-align: center; color: $teamColor2'>Skater Season Stats</h3>";
                echo "<table class='player-stats-table w-full min-w-[900px] table-auto' style='color: black; border: 2px solid $teamColor2'>";
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
                    echo "<tr style='background-color: $teamColor1; color: $teamColor2'>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>Season</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>Game Type</th>";
                        // echo "<th style='width: 9%'>Player ID</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>Name</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>Pos.</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>GP</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>G</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>A</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>P</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>+/-</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>Shots</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>Shot %</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>Avg TOI (min)</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>Avg Shifts/Gm</th>";
                        echo "<th style='border-bottom: 2px solid $teamColor2;'>FO %</th>";
                    echo "</tr>";
                echo "</thead>";
                echo "<tbody id='skaterStatsTable'>";
                $skater_count = mysqli_num_rows($result_skaters);
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
                            echo "<td><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $playerID . "'>" . $firstName . " " . $lastName . "</a></td>";
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
                echo "</div>";
                echo "<br>";


                ### GOALIE CURRENT SEASON STATS TABLE ###
                echo "<h3 style='text-align: center; color: $teamColor2'>Goalie Season Stats</h3>";
                echo "<div class='shadow-md rounded-lg overflow-x-auto'>";
                echo "<table class='goalie-stats-table table-auto' style='color: black; border: 2px solid $teamColor2'>";
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
                        echo "<td><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $playerID . "'>" . $firstName . " " . $lastName . "</a></td>";
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
                      echo "<tr class='no-data-row'>";  // Adding a class to identify it as a special row
                      echo "<td colspan='16' style='text-align: center;'>No goalies found for this team.</td>";
                      echo "</tr>";
                  }
                  echo "</tbody>";
                  echo "</table>";
                  echo "</div>";
