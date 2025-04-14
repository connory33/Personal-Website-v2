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
    <div class="bg-dark text-white text-center">

        <?php
        include('db_connection.php');

        // Check if the 'game_id' is passed in the URL
        if (isset($_GET['game_id'])) {
            $game_id = $_GET['game_id'];

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


            # Fetch all roster data
            $roster_lookup = [];
            $rosterSQL = "SELECT playerID, firstName, lastName FROM nhl_rosters";
            $players = mysqli_query($conn, $sql_roster);

            while ($row = mysqli_fetch_assoc($players)) {
                $roster_lookup[$row['playerID']] = $row['firstName'] . ' ' . $row['lastName'];
            }

            // Query the database to get detailed information about the game
            $sql = "SELECT 
                        nhl_plays.gameID,
                        nhl_plays.period,
                        nhl_plays.timeRemaining,
                        nhl_plays.typeDescKey,
                        nhl_plays.xCoord,
                        nhl_plays.yCoord,
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
                        nhl_plays.
                        nhl_games.homeTeamId AS home_team_id,
                        nhl_games.visitingTeamId AS visiting_team_id,
                        -- rosters.playerID AS player_id,
                        -- rosters.firstName AS player_first_name,
                        -- rosters.lastName AS player_last_name,
                        -- faceoff_losers.firstName AS faceoff_loser_first_name,
                        -- faceoff_losers.lastName AS faceoff_loser_last_name,
                        -- faceoff_winners.firstName AS faceoff_winner_first_name,
                        -- faceoff_winners.lastName AS faceoff_winner_last_name,
                        -- hitters.firstName AS hitter_first_name,
                        -- hitters.lastName AS hitter_last_name,
                        -- hittees.firstName AS hittee_first_name,
                        -- hittees.lastName AS hittee_last_name,
                        -- shooters.firstName AS shooter_first_name,
                        -- shooters.lastName AS shooter_last_name,
                        -- goalies.firstName AS goalie_first_name,
                        -- goalies.lastName AS goalie_last_name,
                        -- takes_gives.firstName AS take_give_first_name,
                        -- takes_gives.lastName AS take_give_last_name,
                        -- blockers.firstName AS blocker_first_name,
                        -- blockers.lastName AS blocker_last_name,
                        -- scorers.firstName AS scorer_first_name,
                        -- scorers.lastName AS scorer_last_name,
                        -- assisters.firstName AS assister_first_name,
                        -- assisters.lastName AS assister_last_name,
                        home_teams.fullName AS home_team_name,
                        visiting_teams.fullName AS visiting_team_name,
                        -- penalty_committers.firstName AS committer_first_name,
                        -- penalty_committers.lastName AS committer_last_name,
                        -- penalty_drawers.firstName AS drawer_first_name,
                        -- penalty_drawers.lastName AS drawer_last_name,
                        nhl_games.gameDate AS gameDate
                    FROM 
                        nhl_plays
                    LEFT JOIN nhl_games
                        ON nhl_plays.gameID = nhl_games.id
                    -- LEFT JOIN rosters_test AS rosters
                    --     ON nhl_plays.gameID = rosters.gameID
                    LEFT JOIN nhl_rosters AS faceoff_losers
                        ON nhl_plays.faceoffLoserId = faceoff_losers.playerID
                    LEFT JOIN nhl_rosters AS faceoff_winners
                        ON nhl_plays.faceoffWinnerId = faceoff_winners.playerID
                    LEFT JOIN nhl_rosters AS hitters
                        ON nhl_plays.hittingPlayerId = hitters.playerID

                    -- TROUBLESHOOT LEFT JOIN HERE - THINK THROUGH JOIN LOGIC
                    LEFT JOIN nhl_rosters AS hittees
                        ON nhl_plays.hitteePlayerId = hittees.playerID

                    LEFT JOIN nhl_rosters AS shooters
                        ON nhl_plays.shootingPlayerId = shooters.playerID
                    
                    LEFT JOIN nhl_rosters AS goalies
                        ON nhl_plays.goalieInNetId = goalies.playerID

                    LEFT JOIN nhl_rosters AS takes_gives
                        ON nhl_plays.takeawayGiveawayPlayerId = takes_gives.playerID

                    LEFT JOIN nhl_rosters AS blockers
                        ON nhl_plays.blockingPlayerId = blockers.playerID

                    LEFT JOIN nhl_rosters AS scorers
                        ON nhl_plays.scoringPlayerID = scorers.playerID

                    LEFT JOIN nhl_rosters AS assisters
                        ON nhl_plays.assist1PlayerId = assisters.playerID

                    LEFT JOIN nhl_rosters AS penalty_committers
                        ON nhl_plays.committerId = penalty_committers.playerID

                    LEFT JOIN nhl_rosters AS penalty_drawers
                        ON nhl_plays.committerId = penalty_drawers.playerID

                    LEFT JOIN nhl_teams AS home_teams
                        ON nhl_games.homeTeamId = home_teams.id

                    LEFT JOIN nhl_teams AS visiting_teams
                        ON nhl_games.visitingTeamId = visiting_teams.id

                    WHERE
                        nhl_plays.gameID = '$game_id'
                    ORDER BY 
                        nhl_plays.period, nhl_plays.timeInPeriod ASC";



        }
                        
            try {
                $result = mysqli_query($conn, $sql);
            } catch (mysqli_sql_exception $e) {
                echo "MySQL Error: " . $e->getMessage();
                exit;
            }

        if (mysqli_num_rows($result) > 0) {
            print("<br> Results found: " . mysqli_num_rows($result) . "<br><br>");
        } else {
            print("No results found.<br>");
        }

            echo "<td>Game details for: </td><br><br>";
            $row = mysqli_fetch_assoc($result);
            $home_team_name = $row['home_team_name'];
            $visiting_team_name = $row['visiting_team_name'];
            echo "<td>Game ID: " . htmlspecialchars($game_id) . "</td><br>";
            echo "<td>" . htmlspecialchars($row['gameDate']) . "</td><br>";
            echo "<td>". htmlspecialchars($home_team_name) . " (H) vs. " . htmlspecialchars($visiting_team_name) . " (A)</td><br><br>";

            mysqli_data_seek($result, 0); 
        ?>

        <div>


                <table style="width: 95%; margin: 0px auto; border: 3px solid #bcd6e7">

                    <tr style="color: white; font-weight: bold; background-color: #2e5b78; border: 2px solid #bcd6e7">
                        <!-- <td>eventID</td> -->
                        <!-- <td>Time Into Period</td> -->
                        <td>Period</td>
                        <td>Time Remaining</td>
                        <!-- <td>Sit. Code</td> -->
                        <!-- <td>Type Code</td> -->
                        <td>Type</td>
                        <td>xCoord</td>
                        <td>yCoord</td>
                        <td>Event Team</td>
                        <td>F/O Loser</td>
                        <td>F/O Winner</td>
                        <td>Hitter</td>
                        <td>Hittee</td>
                        <td>Shot Type</td>
                        <td>Shooter</td>
                        <td>Goalie</td>
                        <td>Away SOG</td>
                        <td>Home SOG</td>
                        <td>Reason</td>
                        <td>Take/Giveaway Player</td>
                        <td>Blocker</td>
                        <td>Scorer</td>
                        <td>Primary Assister</td>
                        <!-- <td>Primary Assister Total</td> -->
                        <td>Away Score</td>
                        <td>Home Score</td>
                        <td>Penalty</td>
                        <td>Committer</td>
                        <td>Drawer</td>
                    </tr>

                    <?php
                    while ($row = $result->fetch_assoc()){
                        echo "<tr style='color: white; border: 1px solid #bcd6e7'>";
                        $rowClass = '';
                        // echo "<td>".$row['eventID']."</td>";
                        // echo "<td>".$row['timeInPeriod']."</td>";
                        echo "<td>".$row['period']."</td>";

                        # Time Remaining
                        $formatted_time = substr($row['timeRemaining'],0,5);
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
                        }
                        else {
                            $formatted_playType = $playType;
                        }
                        echo "<td>".htmlspecialchars($formatted_playType)."</td>";

                        echo "<td>".$row['xCoord']."</td>";
                        echo "<td>".$row['yCoord']."</td>";
                        echo "<td>".$row['eventOwnerTeamId']."</td>";

                        # Faceoffs
                        if ($row['faceoff_loser_first_name'] == 'First Name') {
                            echo "<td>"."-"."</td>";
                            echo "<td>"."-"."</td>";
                        } else {
                            echo "<td>".$row['faceoff_loser_first_name'].' '.$row['faceoff_loser_last_name']."</td>";
                            echo "<td>".$row['faceoff_winner_first_name'].' '.$row['faceoff_winner_last_name']."</td>";
                        }

                        # Hits
                        if ($row['hitter_first_name'] == 'First Name') {
                            echo "<td>"."-"."</td>";
                            echo "<td>"."-"."</td>";
                        } else {
                            echo "<td>".$row['hitter_first_name'].' '.$row['hitter_last_name']."</td>";
                            echo "<td>".$row['hittee_first_name'].' '.$row['hittee_last_name']."</td>";
                        }
                        
                        # Shot Type
                        $formatted_shotType = ucfirst($row['shotType']);
                        echo "<td>".htmlspecialchars($formatted_shotType)."</td>";

                        # Shooting Player
                        // echo "<td>".$row['shootingPlayerId']."</td>";
                        if ($row['shooter_first_name'] == 'First Name') {
                            echo "<td>"."-"."</td>";
                        } else {
                            echo "<td>".$row['shooter_first_name'].' '.$row['shooter_last_name']."</td>";
                        }

                        # Goalie
                        // echo "<td>".$row['goalieInNetId']."</td>";
                        if ($row['goalie_first_name'] == 'First Name') {
                            echo "<td>"."-"."</td>";
                        } else {
                            echo "<td>".$row['goalie_first_name'].' '.$row['goalie_last_name']."</td>";
                        }

                        echo "<td>".$row['awaySOG']."</td>";
                        echo "<td>".$row['homeSOG']."</td>";
                        echo "<td>".$row['reason']."</td>";

                        # Takeaway/Giveaway Player
                        // echo "<td>".$row['takeawayGiveawayPlayerId']."</td>";
                        if ($row['take_give_first_name'] == 'First Name') {
                            echo "<td>"."-"."</td>";
                        } else {
                            echo "<td>".$row['take_give_first_name'].' '.$row['take_give_last_name']."</td>";
                        }

                        # Shot Blocker
                        // echo "<td>".$row['blockingPlayerId']."</td>";
                        if ($row['blocker_first_name'] == 'First Name') {
                            echo "<td>"."-"."</td>";
                        } else {
                            echo "<td>".$row['blocker_first_name'].' '.$row['blocker_last_name']."</td>";
                        }

                        # Scorer
                        // echo "<td>".$row['scoringPlayerId']."</td>";
                        if ($row['scorer_first_name'] == 'First Name') {
                            echo "<td>"."-"."</td>";
                        } else {
                            echo "<td>".$row['scorer_first_name'].' '.$row['scorer_last_name']."</td>";
                        }

                        # Assister
                        // echo "<td>".$row['assist1PlayerId']."</td>";
                        if ($row['assister_first_name'] == 'First Name') {
                            echo "<td>"."-"."</td>";
                        } else {
                            echo "<td>".$row['assister_first_name'].' '.$row['assister_last_name']."</td>";
                        }

                        // echo "<td>".$row['assist1PlayerTotal']."</td>";
                        echo "<td>".$row['awayScore']."</td>";
                        echo "<td>".$row['homeScore']."</td>";

                        # Penalty
                        if ($row['penaltySeverity'] == 'MIN') {
                            $formatted_severity = 'Minor';
                        } else if ($row['penaltySeverity'] == 'MAJ') {
                            $formatted_severity = 'Major';
                        } else {
                            $formatted_severity = '-';
                        }
                        echo "<td>".$formatted_severity.' - '.$row['penaltyType']."</td>";


                        if ($row['committer_first_name'] == 'First Name') {
                            echo "<td>"."-"."</td>";
                            echo "<td>"."-"."</td>";
                        } else {
                            echo "<td>".$row['committer_first_name'].' '.$row['committer_last_name']."</td>";
                            echo "<td>".$row['drawer_first_name'].' '.$row['drawer_last_name']."</td>";
                        }
                        
                        echo "</tr>";
                    }

                    echo "</table>";


            $rosterSQL = "SELECT * FROM nhl_rosters WHERE gameID=$game_id";
            echo($rosterSQL);

            try {
                $rosters_result = mysqli_query($conn, $sql);
            } catch (mysqli_sql_exception $e) {
                echo "MySQL Error: " . $e->getMessage();
                exit;
            }

            ?>

            <table style="width: 70%; margin: 0px auto; border: 1px solid #bcd6e7">
                <tr style="color: white; font-weight: bold; background-color: #2e5b78">
                    <td>gameID</td>
                    <td>teamID</td>
                    <td>playerID</td>
                    <td>firstName</td>
                    <td>lastName</td>
                    <td>sweaterNumber</td>
                    <td>positionCode</td>
                    <td>headshotURL</td>
                </tr>

            <?php

            while ($rosters_row = $rosters_result->fetch_assoc()){
                echo "<tr>";
                echo "<td>" . $rosters_row['gameID'] . "</td>";
                echo "<td>" . $rosters_row['teamID'] . "</td>";
                echo "<td>" . $rosters_row['playerID'] . "</td>";
                echo "<td>" . $rosters_row['firstName'] . "</td>";
                echo "<td>" . $rosters_row['lastName'] . "</td>";
                echo "<td>" . $rosters_row['sweaterNumber'] . "</td>";
                echo "<td>" . $rosters_row['positionCode'] . "</td>";
                echo "<td>" . $rosters_row['headshotURL'] . "</td>";
                echo "</tr>";
            }

            echo "</table>";


                    $conn->close();

            ?>
                </div>

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