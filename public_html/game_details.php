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
                            nhl_games.homeTeamID as homeTeamID, nhl_games.awayTeamID as awayTeamID
                                FROM nhl_rosters 
                                JOIN nhl_teams ON nhl_rosters.teamID = nhl_teams.id
                                JOIN nhl_games ON nhl_rosters.gameID = nhl_games.id
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
                        echo "<td>ID</td><td>Name</td><td>Number</td><td>Position</td><td>Headshot</td>";
                        echo "</tr>";

                        foreach ($players as $player) {
                            echo "<tr style='background-color:#939da3'>";
                            $player_id = $player['playerID'];
                            $player_name = isset($roster_lookup[$player_id]) ? $roster_lookup[$player_id] : 'Unknown';
                            echo "<td><a style='color:navy' href='player_details.php?player_id=" . htmlspecialchars($player_id) ."'>$player_id</a></td>";
                            echo "<td style='color:#000000'>" . htmlspecialchars($player_name) . "</td>";
                            echo "<td style='color:#000000'>" . $player['sweaterNumber'] . "</td>";
                            echo "<td style='color:#000000'>" . $player['positionCode'] . "</td>";
                            // echo "<td>" . $player['triCode'] . "</td>";
                            echo "<td><img src='" . htmlspecialchars($player['headshotURL']) . "' alt='headshot' style='height: 65px;'></td>";
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
            <div>
                <table style="width: 75%; margin: 0px auto; border: 3px solid #bcd6e7">

                    <tr style="color: white; font-weight: bold; background-color: #2e5b78; border: 2px solid #bcd6e7">
                        <td>Period - Time Left</td>
                        <td>Type</td>
                        <td>Coordinates</td>
                        <td>Event Team</td>
                        <td>F/O Winner</td>
                        <td>F/O Loser</td>
                        <td>Hitter</td>
                        <td>Hittee</td>
                        <td>Shot Type</td>
                        <td>Shooter</td>
                        <td>Goalie</td>
                        <td>Reason</td>
                        <td>Take/Giveaway Player</td>
                        <td>Blocker</td>
                        <td>Scorer</td>
                        <td>Primary Assister</td>
                        <td>Penalty</td>
                        <td>Committer</td>
                        <td>Drawer</td>
                    </tr>

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
                                $formatted_playType = 'Period Start';
                            } else if ($playType == 'faceoff') {
                                $formatted_playType = 'Faceoff';
                            } else if ($playType == 'hit') {
                                $formatted_playType = 'Hit';
                            } else if ($playType == 'shot-on-goal') {
                                $formatted_playType = 'SOG';
                            } else if ($playType == 'goal') {
                                $formatted_playType = 'Goal';
                            } else if ($playType == 'stoppage') {
                                $formatted_playType = 'Stoppage';
                            } else if ($playType == 'giveaway') {
                                $formatted_playType = 'Giveaway';
                            } else if ($playType == 'takeaway') {
                                $formatted_playType = 'Takeaway';
                            } else if ($playType == 'blocked-shot') {
                                $formatted_playType = 'Blocked Shot';
                            } else if ($playType == 'missed-shot') {
                                $formatted_playType = 'Missed Shot';
                            } else if ($playType == 'penalty') {
                                $formatted_playType = 'Penalty';
                            } else if ($playType == 'delayed-penalty') {
                                $formatted_playType = 'Delayed Penalty';
                            } else if ($playType == 'period-end') {
                                $formatted_playType = 'Period End';
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
                            echo "<td>".$row['eventOwnerTeamId']."</td>";

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
                            // if (len($row['shotType']) == 0) {
                            //     $formatted_shotType = '-';
                            // } else {
                            //     $formatted_shotType = ucfirst($row['shotType']);
                            // }
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
                                $formatted_reason = 'High/wide right';
                            } else if ($reason == 'wide-left') {
                                $formatted_reason = 'Wide left';
                            } else if ($reason == 'high-and-wide-left') {
                                $formatted_reason = 'High/wide left';
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
                                $formatted_severity = 'Minor';
                            } else if ($row['penaltySeverity'] == 'MAJ') {
                                $formatted_severity = 'Major';
                            } else {
                                $formatted_severity = '';
                            }
                            echo "<td>".$formatted_severity.' - '.$row['penaltyType']."</td>";
                            $committer_id = $row['committerId'];
                            $committer_name = isset($roster_lookup[$committer_id]) ? $roster_lookup[$committer_id] : 'Unknown';
                            $drawer_id = $row['drawerId'];
                            $drawer_name = isset($roster_lookup[$drawer_id]) ? $roster_lookup[$drawer_id] : 'Unknown';
                            echo "<td>".htmlspecialchars($committer_name)."</td>";
                            echo "<td>".htmlspecialchars($drawer_name)."</td>";
                            

                            echo "</tr>";
                        }

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