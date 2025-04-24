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

        <?php
        include('db_connection.php');

        // Check if the 'game_id' is passed in the URL
        if (isset($_GET['game_id'])) {
                $game_id = $_GET['game_id'];

                ###################### DEFINING ALL SQL QUERIES ################################
                # Fetch all roster data to allow PHP lookup instead of SQL joins for speed
                $roster_lookup = [];
                $rosterSQL = "SELECT playerID, firstName, lastName FROM nhl_rosters";
                $players = mysqli_query($conn, $rosterSQL);

                while ($row = mysqli_fetch_assoc($players)) {
                    $roster_lookup[$row['playerID']] = $row['firstName'] . ' ' . $row['lastName'];
                }
                $roster_lookup[0] = '-';

                # Fetch all team data to allow PHP lookup instead of SQL joins for speed
                $team_name_lookup = [];
                $team_tricode_lookup = [];
                $teamSQL = "SELECT id, fullName, triCode FROM nhl_teams";
                $teams = mysqli_query($conn, $teamSQL);

                while ($row = mysqli_fetch_assoc($teams)) {
                    $team_name_lookup[$row['id']] = $row['fullName'];
                    $team_tricode_lookup[$row['id']] = $row['triCode'];
                }

                # Roster Tables
                $rostertableSQL = "SELECT nhl_rosters.gameID,
                            nhl_rosters.teamID,
                            nhl_rosters.playerID,
                            nhl_rosters.firstName,
                            nhl_rosters.lastName,
                            nhl_rosters.sweaterNumber,
                            nhl_rosters.positionCode,
                            nhl_rosters.headshotURL,
                            nhl_teams.id,
                            nhl_teams.fullName,
                            nhl_teams.triCode,
                            nhl_games.homeTeamID as homeTeamID, nhl_games.awayTeamID as awayTeamID,
                            skaters_gamebygame_stats.goals AS skater_goals
                                FROM nhl_rosters 
                                JOIN nhl_teams ON nhl_rosters.teamID = nhl_teams.id
                                JOIN nhl_games ON nhl_rosters.gameID = nhl_games.id
                                JOIN skaters_gamebygame_stats ON nhl_rosters.playerID = skaters_gamebygame_stats.playerID
                                JOIN goalies_gamebygame_stats ON nhl_rosters.playerID = goalies_gamebygame_stats.playerID
                                WHERE nhl_rosters.gameID=$game_id
                                ORDER BY nhl_rosters.lastName";

                try {
                    $rosters_result = mysqli_query($conn, $rostertableSQL);
                } catch (mysqli_sql_exception $e) {
                    echo "MySQL Error: " . $e->getMessage();
                    exit;
                }

                # Play-by-Play Table
                $playsSQL = "SELECT 
                nhl_plays.gameID,
                nhl_plays.period,
                nhl_plays.timeRemaining,
                nhl_plays.typeDescKey,
                nhl_plays.xCoord,
                nhl_plays.yCoord,
                nhl_plays.eventOwnerTeamId,
                nhl_plays.zoneCode,
                nhl_plays.faceoffLoserId,
                nhl_plays.faceoffWinnerId,
                nhl_plays.hittingPlayerId,
                nhl_plays.hitteePlayerId,
                nhl_plays.shotType,
                nhl_plays.shootingPlayerId,
                nhl_plays.goalieInNetId,
                nhl_plays.awaySOG,
                nhl_plays.homeSOG,
                nhl_plays.reason,
                nhl_plays.takeawayGiveawayPlayerId,
                nhl_plays.blockingPlayerId,
                nhl_plays.scoringPlayerId,
                nhl_plays.assist1PlayerId,
                nhl_plays.awayScore,
                nhl_plays.homeScore,
                nhl_plays.penaltySeverity,
                nhl_plays.penaltyType,
                nhl_plays.committerId,
                nhl_plays.drawerId,
                nhl_games.homeTeamId as home_team_id,
                nhl_games.awayTeamId as away_team_id,
                home_teams.fullName as home_team_name,
                away_teams.fullName as away_team_name,
                event_team.triCode as event_team_tricode,
                nhl_games.gameDate,
                nhl_games.venue,
                nhl_games.venueLocation,
                nhl_games.easternStartTime,
                nhl_games.gameStateId
                FROM 
                nhl_plays

                LEFT JOIN nhl_games
                ON nhl_plays.gameID = nhl_games.id

                LEFT JOIN nhl_teams AS home_teams
                ON nhl_games.homeTeamId = home_teams.id

                LEFT JOIN nhl_teams AS away_teams
                ON nhl_games.awayTeamId = away_teams.id

                LEFT JOIN nhl_teams AS event_team
                ON nhl_plays.eventOwnerTeamId = event_team.id

                WHERE
                nhl_plays.gameID = '$game_id'

                ORDER BY 
                nhl_plays.period, nhl_plays.timeInPeriod ASC";

                try {
                $plays = mysqli_query($conn, $playsSQL);
                } catch (mysqli_sql_exception $e) {
                echo "MySQL Error: " . $e->getMessage();
                exit;
                }

                // if (mysqli_num_rows($result) > 0) {
                // print("<br> Results found: " . mysqli_num_rows($result) . "<br><br>");
                // } else {
                // print("No results found.<br>");
                // }

                $row = mysqli_fetch_assoc($plays);
                // $home_team_name = $row['home_team_name'];
                // $away_team_name = $row['away_team_name'];
                $game_date = $row['gameDate'];

                # Header Info
                $headerSQL = "SELECT nhl_games.id,
                nhl_games.gameDate,
                nhl_games.venue,
                nhl_games.venueLocation,
                nhl_games.easternStartTime,
                nhl_games.gameStateId,
                nhl_games.homeScore,
                nhl_games.awayScore,
                nhl_games.homeLogo,
                nhl_games.awayLogo,
                nhl_games.gameType,
                nhl_games.gameNumber,
                nhl_games.season,
                home_teams.fullName AS home_team_name,
                away_teams.fullName AS away_team_name
                FROM 
                nhl_games
                LEFT JOIN nhl_teams AS home_teams
                ON nhl_games.homeTeamId = home_teams.id
                LEFT JOIN nhl_teams AS away_teams
                ON nhl_games.awayTeamId = away_teams.id
                WHERE
                nhl_games.id = '$game_id'";

                // echo($headerSQL);

                try {
                    $header = mysqli_query($conn, $headerSQL);
                    // echo "<p>Successful</p>";
                    } catch (mysqli_sql_exception $e) {
                    echo "MySQL Error: " . $e->getMessage();
                    exit;
                    }

                // echo '<pre>'; print_r($header); echo '</pre>';

                $row = mysqli_fetch_assoc($header);
                if (!$row) {
                    echo "<p>No data returned from header query.</p>";
                } else {
                    $venue = $row['venue'];
                    $venueLocation = $row['venueLocation'];
                    $game_date = $row['gameDate'];
                    $homeScore = $row['homeScore'];
                    $awayScore = $row['awayScore'];
                    $homeLogo = $row['homeLogo'];
                    $awayLogo = $row['awayLogo'];
                    $homeTeamName = $row['home_team_name'];
                    $awayTeamName = $row['away_team_name'];
                    $formatted_startTime = substr($row['easternStartTime'], 11, -3);
                    $gameType_num = $row['gameType'];
                    $gameNum = $row['gameNumber'];
                    if ($gameType_num == 1) {
                        $gameType_text = "Preseason";
                    } elseif ($gameType_num == 2) {
                        $gameType_text = "Reg. Season";
                    } elseif ($gameType_num == 3) {
                        $gameType_text = "Playoffs";
                    } else {
                        $gameType_text = "Unknown";
                    }
                    $season = $row['season'];
                    $formatted_season = substr($season, 0, 4) . '-' . substr($season, 4);
                    $game_outcome = $row['gameOutcome']; #### this isn't working
                    if ($game_outcome == 'REG') {
                        echo "<p>reg</p>";
                        $formatted_outcome = '';
                    }
                    else if ($game_outcome == 'OT') {
                        $formatted_outcome = "(OT)";
                    }

                    $gameDatetime = new DateTime($game_date);
                    $formatted_gameDate = $gameDatetime->format('m/d/Y');
                
                    echo($formatted_outcome);
                    echo "<h3><br> <img src='" . htmlspecialchars($homeLogo) . "' alt='homeLogo' style='height: 65px;'>" . 
                    htmlspecialchars($homeTeamName) . " (H) vs. " . htmlspecialchars($awayTeamName) . " (A) <img src='" . 
                    htmlspecialchars($awayLogo) . "' alt='awayLogo' style='height: 65px;'></h3>";
                    echo "<h3>" . htmlspecialchars($homeScore) . "     -     " . htmlspecialchars($awayScore) . 
                    ' ' . $formatted_outcome . "</h3><br>";
                    echo "<p>" . htmlspecialchars($venue) . ", " . htmlspecialchars($venueLocation) . "<br>"
                    . htmlspecialchars($formatted_gameDate) . "<br>" . htmlspecialchars($formatted_startTime) . " EST</p>";
                    echo "<p>Season: " . $formatted_season . "<br>Game Type: " . $gameType_text . "<br>Game Number: " . $gameNum .
                    "<br>Game ID: " . htmlspecialchars($game_id) . "</p>";
                    echo "<hr style='width:80%; background-color:white'>";
                }


                ###############################################################################

                    // Create field indexes to speed up SQL query
                    // $indexes = [
                    //     "CREATE INDEX idx_plays_gameID ON nhl_plays(gameID)",
                    //     "CREATE INDEX idx_games_gameID ON nhl_games(id)",
                    //     "CREATE INDEX idx_rosters_gameID ON nhl_rosters(gameID)",
                    //     "CREATE INDEX idx_FOloserID ON nhl_plays(faceoffLoserId)",
                    //     "CREATE INDEX idx_ ON ()",
                    //     "CREATE INDEX idx_FOwinnerID ON nhl_plays(faceoffWinnerId)",
                    //     "CREATE INDEX idx_ ON ()",
                    //     "CREATE INDEX idx_ ON ()",
                    //     "CREATE INDEX idx_ ON ()",
                    //     "CREATE INDEX idx_ ON ()",
                    //     "CREATE INDEX idx_ ON ()",
                    //     "CREATE INDEX idx_ ON ()",
                    //     "CREATE INDEX idx_ ON ()",
                    //     "CREATE INDEX idx_ ON ()",

                    //     # anything in join condition
                    //     faceoff_losers.playerID (rosters)
                    //     faceoff_winners.playerID (rosters)
                    //     nhl_plays.hittingPlayerId = hitters.playerID (rosters)
                    //     nhl_plays.hitteePlayerId = hittees.playerID (rosters)
                    //     nhl_plays.shootingPlayerId = shooters.playerID (rosters)
                    //     nhl_plays.goalieInNetId = goalies.playerID (rosters)
                    //     nhl_plays.takeawayGiveawayPlayerId = takes_gives.playerID (rosters)
                    //     nhl_plays.blockingPlayerId = blockers.playerID (rosters)
                    //     nhl_plays.scoringPlayerID = scorers.playerID (rosters)
                    //     nhl_plays.assist1PlayerId = assisters.playerID (rosters)
                    //     nhl_plays.committerId = penalty_committers.playerID (rosters)
                    //     nhl_plays.committerId = penalty_drawers.playerID (rosters)
                    //     nhl_games.homeTeamId = home_teams.id
                    //     nhl_games.visitingTeamId = visiting_teams.id

                    //     # anything in order by
                    //     nhl_plays.period, nhl_plays.timeInPeriod

                    //     # anything frequently searched, filtered, or sorted

                    // ]

                    // foreach ($indexes as $indexesSQL) {
                    //     if (mysqli_query($conn, $indexesSQL)) {
                    //         echo "Success: " . htmlspecialchars($indexesSQL) . "<br>";
                    //     } else {
                    //         echo "Failed: " . htmlspecialchars($indexesSQL) . "<br>";
                    //         echo "Error: " . mysqli_error($conn) . "<br>";
                    //     }
                    // }


                    // echo '<pre>'; print_r($team_tricode_lookup); echo '</pre>';


                    ###################################### ROSTER TABLES #####################################
                    
                    $home_players = [];
                    $away_players = [];
                    while ($row = mysqli_fetch_assoc($rosters_result)) {
                        if ($row['teamID'] == $row['homeTeamID']) {
                            $home_players[] = $row;
                            $home_team_name = $row['fullName'];
                        } elseif ($row['teamID'] == $row['awayTeamID']) {
                            $away_players[] = $row;
                            $away_team_name = $row['fullName'];
                        }
                    }

                    // echo(bool($home_players));
                    // echo '<pre>'; print_r($home_players); echo '</pre>';
                    

                    function render_roster_table($players, $team_label, $roster_lookup) {
                        echo "<div style='width: 40%;'>"; # table wrapper

                        echo "<h4 style='text-align: center;'>$team_label</h4><br>";
                        
                        echo "<table style='width: 100%; border: 1px solid #bcd6e7'>";
                        echo "<tr style='color: white; font-weight: bold; background-color:#2e5b78'>";
                        echo "<td>ID</td><td>Name</td><td>Number</td><td>Position</td>";
                        // echo "<td>Headshot</td>";
                        echo "</tr>";

                        foreach ($players as $player) {
                            // echo "<p>TEST" . $row['skater_goals'] . "</p>"; THIS BREAKS IT
                            echo "<tr style='background-color:#939da3'>";
                            $player_id = $player['playerID'];
                            $player_name = isset($roster_lookup[$player_id]) ? $roster_lookup[$player_id] : 'Unknown';
                            echo "<td><a style='color:navy' href='player_details.php?player_id=" . htmlspecialchars($player_id) ."'>$player_id</a></td>";
                            echo "<td style='color:#000000'><a style='color:navy' href='player_details.php?player_id=" . $player_id . "'" . "</a>" . $player_name . "</td>";
                            // echo "<td style='color:#000000'>" . htmlspecialchars($player_name) . "</td>";
                            echo "<td style='color:#000000'>" . $player['sweaterNumber'] . "</td>";
                            if ($player['positionCode'] == 'L') {
                                $positionDisplay = 'LW';
                            } else if ($player['positionCode'] == 'R') {
                                $positionDisplay = 'RW';
                            } else {
                                $positionDisplay = $player['positionCode'];
                            }
                            echo "<td style='color:#000000'>" . $positionDisplay . "</td>";
                            // echo "<td>" . $player['triCode'] . "</td>";
                            // echo "<td><img src='" . htmlspecialchars($player['headshotURL']) . "' alt='headshot' style='height: 65px;'></td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                        echo "</div>"; # table wrapper
                    }
                echo "<div style='display: flex; justify-content: center; gap: 40px; margin: 40px auto;'>"; # flex container
        }

                    if (empty($home_players)) {
                        echo "<p>No roster data available.</p>";
                    } else {
                        render_roster_table($home_players, $home_team_name, $roster_lookup);
                        render_roster_table($away_players, $away_team_name, $roster_lookup);
                    }

            echo "</div>"; # flex container
            echo "<hr style='width:80%; background-color:white'>";

        ?>

        <br><br>


<!-- -------------------------------------------- PLAY-BY-PLAY --------------------------------------- -->
        <?php
        if (mysqli_num_rows($plays) > 0) {
        ?>

<div class="rink-key-wrapper">
    <!-- Left column: Title + Rink Image -->
    <div class="rink-key-column">
        <h4 style='font-weight: bold;'>Rink Diagram/Coordinates</h4>
        <img src='../resources/images/hockey-rink.jpg' style='max-width: 100%; height: auto;'>
    </div>

    <!-- Right column: Title + Legend -->
    <div class="rink-key-column">
        <h4 style='font-weight: bold; text-align: center;'>Play-by-Play Key</h4>
        <div style='text-align: left;'>
            <p style='margin-top: 10px'>
            FO - Faceoff<br>
            SOG - Shot on Goal<br>
            Pen. - Penalty<br>
            Block - Blocked Shot<br>
            Miss - Missed Shot<br>
            Stop - Stoppage<br>
            Give - Giveaway<br>
            Take - Takeaway<br>
            D. Pen. - Delayed Penalty<br>
            Back - Backhand<br>
            Tip - Tip-in
            </p>
        </div>
    </div>
</div>
<br>


<h4 style="text-align: center; margin-top:20px">Play-by-Play Events</h4>
    <div class="overflow-x-auto">
        <table id="play-by-play-table" class="min-w-max table-auto">
            <thead>
                <tr style>
                        <th class='pbp-col-time-left'>Per. Time Left</th>
                        <th class='pbp-col-type'>Type</th>
                        <th class='pbp-col-coords'>Coords.</th>
                        <th class='pbp-col-team'>Team</th>
                        <th class='pbp-col-fo-winner'>F/O Winner</th>
                        <th class='pbp-col-fo-loser'>F/O Loser</th>
                        <th class='pbp-col-hitter'>Hitter</th>
                        <th class='pbp-col-hittee'>Hittee</th>
                        <th class='pbp-col-shot-type'>Shot Type</th>
                        <th class='pbp-col-shooter'>Shooter</th>
                        <th class='pbp-col-goalie'>Goalie</th>
                        <th class='pbp-col-reason'>Reason</th>
                        <th class='pbp-col-take-give'>Taker / Giver</th>
                        <th class='pbp-col-blocker'>Blocker</th>
                        <th class='pbp-col-scorer'>Scorer</th>
                        <th class='pbp-col-primary-assister'>1st Assist</th>
                        <th class='pbp-col-penalty'>Penalty</th>
                        <th class='pbp-col-committer'>Committer</th>
                        <th class='pbp-col-drawer'>Drawer</th>
                    </tr>
                </thead>
                    <tbody>
                    <?php
                    
                        while ($row = $plays->fetch_assoc()){
                            echo "<tr style='color: white; border: 1px solid #bcd6e7'>";
                            $rowClass = '';
                            // echo "<td>".$row['eventID']."</td>";
                            // echo "<td>".$row['timeInPeriod']."</td>";

                            # Period/Time Remaining
                            $formatted_time = $row['period'] . ' - ' . substr($row['timeRemaining'],0,5);
                            echo "<td>".$formatted_time."</td>";

                            // echo "<td>".$row['situationCode']."</td>";
                            // echo "<td>".$row['typeCode']."</td>";
                            $eventType = $row['typeDescKey'];
                            if ($eventType == 'goal') {
                                $rowClass = 'goal-row';
                            }

                            # Play Type
                            // echo "<td>".$row['typeDescKey']."</td>";
                            $playType = $row['typeDescKey'];
                            if ($playType == 'period-start') {
                                $formatted_playType = 'Per. Start';
                            } else if ($playType == 'faceoff') {
                                $formatted_playType = 'FO';
                            } else if ($playType == 'hit') {
                                $formatted_playType = 'Hit';
                            } else if ($playType == 'shot-on-goal') {
                                $formatted_playType = 'SOG';
                            } else if ($playType == 'goal') {
                                $formatted_playType = 'Goal';
                            } else if ($playType == 'stoppage') {
                                $formatted_playType = 'Stop';
                            } else if ($playType == 'giveaway') {
                                $formatted_playType = 'Give';
                            } else if ($playType == 'takeaway') {
                                $formatted_playType = 'Takea';
                            } else if ($playType == 'blocked-shot') {
                                $formatted_playType = 'Block';
                            } else if ($playType == 'missed-shot') {
                                $formatted_playType = 'Miss';
                            } else if ($playType == 'penalty') {
                                $formatted_playType = 'Pen.';
                            } else if ($playType == 'delayed-penalty') {
                                $formatted_playType = 'D. Pen.';
                            } else if ($playType == 'period-end') {
                                $formatted_playType = 'Per. End';
                            } else {
                                $formatted_playType = $playType;
                            }
                            echo "<td>".htmlspecialchars($formatted_playType)."</td>";

                            # Coordinates
                            $formatted_coordinates = $row['xCoord'] . '/' . $row['yCoord'];
                            // echo "<td>".$row['xCoord']."</td>";
                            // echo "<td>".$row['yCoord']."</td>";
                            echo "<td>" . $formatted_coordinates . "</td>";

                            # Event Team
                            echo "<td>".$row['event_team_tricode']."</td>";

                            # Faceoffs
                            $faceoff_winner_id = $row['faceoffWinnerId'];
                            $faceoff_winner_name = isset($roster_lookup[$faceoff_winner_id]) ? $roster_lookup[$faceoff_winner_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($faceoff_winner_name)."</td>";
                            
                            $faceoff_loser_id = $row['faceoffLoserId'];
                            $faceoff_loser_name = isset($roster_lookup[$faceoff_loser_id]) ? $roster_lookup[$faceoff_loser_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($faceoff_loser_name)."</td>";

                            # Hits
                            $hitter_id = $row['hittingPlayerId'];
                            $hitter_name = isset($roster_lookup[$hitter_id]) ? $roster_lookup[$hitter_id] : 'Unknown';
                            $hittee_id = $row['hitteePlayerId'];
                            $hittee_name = isset($roster_lookup[$hittee_id]) ? $roster_lookup[$hittee_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($hitter_name)."</td>";
                            echo "<td>".htmlspecialchars($hittee_name)."</td>";
                            
                            # Shot Type
                            $formatted_shotType = ucfirst($row['shotType']);
                            if ($formatted_shotType == 'Backhand') {
                                $formatted_shotType = 'Back';
                            } else if ($formatted_shotType == 'Tip-in') {
                                $formatted_shotType = 'Tip';
                            } else if ($formatted_shotType == ''){
                                $formatted_shotType = '-';
                            } else {
                                $formatted_shotType = $formatted_shotType;
                            }
                            
                            echo "<td>".htmlspecialchars($formatted_shotType)."</td>";

                            # Shooting Player
                            $shooter_id = $row['shootingPlayerId'];
                            $shooter_name = isset($roster_lookup[$shooter_id]) ? $roster_lookup[$shooter_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($shooter_name)."</td>";

                            # Goalie
                            $goalie_id = $row['goalieInNetId'];
                            $goalie_name = isset($roster_lookup[$goalie_id]) ? $roster_lookup[$goalie_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($goalie_name)."</td>";

                            // echo "<td>".$row['awaySOG']."</td>";
                            // echo "<td>".$row['homeSOG']."</td>";

                            # Reason
                            $reason = $row['reason'];
                            if ($reason == 'wide-right') {
                                $formatted_reason = 'Wide right';
                            } else if ($reason == 'high-and-wide-right') {
                                $formatted_reason = 'High / wide right';
                            } else if ($reason == 'wide-left') {
                                $formatted_reason = 'Wide left';
                            } else if ($reason == 'high-and-wide-left') {
                                $formatted_reason = 'High / wide left';
                            } else if ($reason == 'puck-frozen') {
                                $formatted_reason = 'Puck frozen';
                            } else if ($reason == 'goalie-stopped-after-sog') {
                                $formatted_reason = 'Goalie freeze';
                            } else if ($reason == 'tv-timeout') {
                                $formatted_reason = 'TV';
                            } else if ($reason == 'hit-crossbar') {
                                $formatted_reason = 'Crossbar';
                            } else if ($reason == 'above-crossbar') {
                                $formatted_reason = 'Over net';
                            } else if ($reason == 'hit-right-post') {
                                $formatted_reason = 'Right post';
                            } else if ($reason == 'hit-left-post') {
                                $formatted_reason = 'Left post';
                            } else if ($reason == 'puck-in-netting' || $reason == 'puck-in-benches' || $reason == 'puck-in-penalty-benches') {
                                $formatted_reason = 'Out of play';
                            } else if ($reason == 'player-injury') {
                                $formatted_reason = 'Injury';
                            } else if ($reason == 'offside') {
                                $formatted_reason = 'Offside';
                            } else if ($reason == 'icing') {
                                $formatted_reason = 'Icing';
                            } else if ($reason == 'hand-pass') {
                                $formatted_reason = 'Hand pass';
                            } else {
                                $formatted_reason = '-';
                            }
                            echo "<td>".$formatted_reason."</td>";

                            # Takeaway/Giveaway Player
                            $take_give_id = $row['takeawayGiveawayPlayerId'];
                            $take_give_name = isset($roster_lookup[$take_give_id]) ? $roster_lookup[$take_give_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($take_give_name)."</td>";

                            # Shot Blocker
                            $blocker_id = $row['blockingPlayerId'];
                            $blocker_name = isset($roster_lookup[$blocker_id]) ? $roster_lookup[$blocker_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($blocker_name)."</td>";

                            # Scorer
                            $scorer_id = $row['scoringPlayerId'];
                            $scorer_name = isset($roster_lookup[$scorer_id]) ? $roster_lookup[$scorer_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($scorer_name)."</td>";

                            # Assister
                            $assister_id = $row['assist1PlayerId'];
                            $assister_name = isset($roster_lookup[$assister_id]) ? $roster_lookup[$assister_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($assister_name)."</td>";

                            # Score
                            // echo "<td>".$row['awayScore']."</td>";
                            // echo "<td>".$row['homeScore']."</td>";

                            # Penalty
                            if ($row['penaltySeverity'] == 'MIN') {
                                $formatted_severity = '(2)';
                            } else if ($row['penaltySeverity'] == 'MAJ') {
                                $formatted_severity = '(4)';
                            } else if ($row['penaltySeverity'] == ''){
                                $formatted_severity = '-';
                            } else {
                                $formatted_severity = 'ERROR';
                            }
                            echo "<td>" . $row['penaltyType'] . ' ' . $formatted_severity . "</td>";
                            $committer_id = $row['committerId'];
                            $committer_name = isset($roster_lookup[$committer_id]) ? $roster_lookup[$committer_id] : 'Unknown';
                            $drawer_id = $row['drawerId'];
                            $drawer_name = isset($roster_lookup[$drawer_id]) ? $roster_lookup[$drawer_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($committer_name)."</td>";
                            echo "<td>".htmlspecialchars($drawer_name)."</td>";
                            

                            echo "</tr>";
                        }
                echo "</tbody>";

                echo "</table>";
        

            mysqli_close($conn);
            
        } else {
                echo "<p style='text-align: center'>No play-by-play data available for this game.</p>";
        }
        ?>
            <br>
        </div>

        <footer class="bg-body-tertiary">
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