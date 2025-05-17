<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../resources/images/stick_icon.png">

  <title>Game Details</title>

  <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* Dashboard-specific styles */
    .dashboard-container {
      display: grid;
      grid-template-columns: repeat(12, 1fr);
      grid-gap: 1rem;
      width: 100%;
      max-width: 1800px;
      margin: 0 auto;
      padding: 1rem;
    }
    
    .dashboard-card {
      background: linear-gradient(to bottom, rgba(31, 41, 55, 0.8), rgba(17, 24, 39, 0.9));
      border: 1px solid rgba(69, 162, 158, 0.4);
      border-radius: 0.75rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      padding: 1rem;
      overflow: hidden;
    }
    
    .header-card { grid-column: span 12; }
    .shot-chart-card { grid-column: span 8; }
    .pbp-key-card { grid-column: span 4; }
    .roster-card { grid-column: span 12; }
    .pbp-card { grid-column: span 12; }
    
    @media (max-width: 1200px) {
      .shot-chart-card { grid-column: span 12; }
      .pbp-key-card { grid-column: span 12; }
    }

    .roster-tabs {
      display: flex;
      gap: 0.5rem;
      margin-bottom: 1rem;
    }
    
    .roster-tab {
      padding: 0.5rem 1rem;
      background: rgba(31, 41, 55, 0.6);
      border-radius: 0.5rem;
      cursor: pointer;
      border: 1px solid rgba(69, 162, 158, 0.4);
    }
    
    .roster-tab.active {
      /* background: linear-gradient(to right, #3b82f6, #2563eb); */
      background: linear-gradient(to right, rgba(69, 162, 158, 0.2), rgba(102, 252, 241, 0.1));
      /* border: 1px solid rgba(69, 162, 158, 0.4); */
      color: #66FCF1;
    }

    .roster-tab:hover {
    background: linear-gradient(to right, rgba(69, 162, 158, 0.3), rgba(102, 252, 241, 0.2));
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
} 

    .shift-charts-link {
    /* display: inline-block; */
    background: linear-gradient(to right, rgba(69, 162, 158, 0.2), rgba(102, 252, 241, 0.1));
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    border: 1px solid rgba(69, 162, 158, 0.4);
    /* font-weight: 600; */
    text-decoration: none;
    /* color: #66FCF1; */
    /* transition: all 0.3s ease; */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin: 1rem auto;
}

.shift-charts-link:hover {
    background: linear-gradient(to right, rgba(69, 162, 158, 0.3), rgba(102, 252, 241, 0.2));
    /* transform: translateY(-2px); */
    box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
}
    
    .roster-content {
      display: none;
    }
    
    .roster-content.active {
      display: block;
    }

    .card-header {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 0.4rem;
      padding-bottom: 0.25rem;
      border-bottom: 1px solid rgba(69, 162, 158, 0.4);
    }
  </style>
</head>

<body>
  <!-- Include header -->
  <?php include 'header.php'; ?>

          <?php
        include('db_connection.php');

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Check if the 'game_id' is passed in the URL
        if (isset($_GET['game_id'])) {
                $game_id = $_GET['game_id'];

                ###################### DEFINING ALL SQL QUERIES ################################
                # Fetch all roster data to allow PHP lookup instead of SQL joins for speed
                $roster_lookup = [];
                $rosterSQL = "SELECT nhl_rosters.playerID, nhl_players.firstName, nhl_players.lastName 
                              FROM nhl_rosters
                              JOIN nhl_players ON nhl_rosters.playerID = nhl_players.playerId
                              WHERE nhl_rosters.gameID='$game_id'";
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
                            nhl_players.firstName,
                            nhl_players.lastName,
                            nhl_players.headshot,
                            nhl_teams.id,
                            nhl_teams.fullName,
                            nhl_teams.triCode,
                            -- SUM(skaters_gamebygame_stats.sog) AS total_shots,
                            -- SUM(skaters_gamebygame_stats.blockedShots) AS total_blocked_shots,
                            -- SUM(skaters_gamebygame_stats.missedShots) AS total_missed_shots,
                            nhl_games.homeTeamID as homeTeamID, nhl_games.awayTeamID as awayTeamID,
                            skaters_gamebygame_stats.playerID AS skater_playerID,
                            skaters_gamebygame_stats.sweaterNumber AS skater_sweaterNumber,
                            skaters_gamebygame_stats.position AS skater_position,
                            skaters_gamebygame_stats.goals AS skater_goals,
                            skaters_gamebygame_stats.assists AS skater_assists,
                            skaters_gamebygame_stats.points AS skater_points,
                            skaters_gamebygame_stats.plusMinus AS skater_plusMinus,
                            skaters_gamebygame_stats.pim AS skater_pim,
                            skaters_gamebygame_stats.hits AS skater_hits,
                            skaters_gamebygame_stats.powerPlayGoals AS skater_powerPlayGoals,
                            skaters_gamebygame_stats.sog AS skater_sog,
                            skaters_gamebygame_stats.faceoffWinningPctg AS skater_faceoffWinningPctg,
                            skaters_gamebygame_stats.toi AS skater_toi,
                            skaters_gamebygame_stats.blockedShots AS skater_blockedShots,
                            skaters_gamebygame_stats.shifts AS skater_shifts,
                            skaters_gamebygame_stats.giveaways AS skater_giveaways,
                            skaters_gamebygame_stats.takeaways AS skater_takeaways,
                            goalies_gamebygame_stats.sweaterNumber AS goalie_sweaterNumber,
                            goalies_gamebygame_stats.position AS goalie_position,
                            goalies_gamebygame_stats.pim AS goalie_pim,
                            goalies_gamebygame_stats.toi AS goalie_toi,
                            goalies_gamebygame_stats.evenStrengthShotsAgainst AS goalie_evenStrengthShotsAgainst,
                            goalies_gamebygame_stats.powerPlayShotsAgainst AS goalie_powerPlayShotsAgainst,
                            goalies_gamebygame_stats.shorthandedShotsAgainst AS goalie_shorthandedShotsAgainst,
                            goalies_gamebygame_stats.saveShotsAgainst AS goalie_saveShotsAgainst,
                            goalies_gamebygame_stats.savePctg AS goalie_savePctg,
                            goalies_gamebygame_stats.evenStrengthGoalsAgainst AS goalie_evenStrengthGoalsAgainst,
                            goalies_gamebygame_stats.powerPlayGoalsAgainst AS goalie_powerPlayGoalsAgainst,
                            goalies_gamebygame_stats.shorthandedGoalsAgainst AS goalie_shorthandedGoalsAgainst,
                            goalies_gamebygame_stats.goalsAgainst AS goalie_goalsAgainst,
                            goalies_gamebygame_stats.starter AS goalie_starter,
                            goalies_gamebygame_stats.shotsAgainst AS goalie_shotsAgainst,
                            goalies_gamebygame_stats.saves AS goalie_saves
                                FROM nhl_rosters 
                                JOIN nhl_teams ON nhl_rosters.teamID = nhl_teams.id
                                JOIN nhl_games ON nhl_rosters.gameID = nhl_games.id
                                LEFT JOIN nhl_players 
                                    ON nhl_rosters.playerID = nhl_players.playerId
                                LEFT JOIN skaters_gamebygame_stats 
                                    ON nhl_rosters.playerID = skaters_gamebygame_stats.playerID 
                                    AND nhl_rosters.gameID = skaters_gamebygame_stats.gameID
                                    AND skaters_gamebygame_stats.position IS NOT NULL

                                LEFT JOIN goalies_gamebygame_stats 
                                    ON nhl_rosters.playerID = goalies_gamebygame_stats.playerID 
                                    AND nhl_rosters.gameID = goalies_gamebygame_stats.gameID
                                    AND goalies_gamebygame_stats.position IS NOT NULL

                                WHERE nhl_rosters.gameID='$game_id'
                                ORDER BY nhl_players.lastName";

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
                home_teams.teamColor1 as home_team_color1,
                home_teams.teamColor2 as home_team_color2,
                away_teams.fullName as away_team_name,
                away_teams.teamColor1 as away_team_color1,
                away_teams.teamColor2 as away_team_color2,
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

                if ($row !== null && isset($row['gameDate'])) {
                    $game_date = $row['gameDate'];
                    $homeTeamColor1 = $row['home_team_color1'];
                    $homeTeamColor2 = $row['home_team_color2'];
                    $awayTeamColor1 = $row['away_team_color1'];
                    $awayTeamColor2 = $row['away_team_color2'];
                } else {
                    $game_data = '';
                }
                

                # Header Info
                $headerSQL = "SELECT nhl_games.id,
                nhl_games.gameDate,
                nhl_games.venue,
                nhl_games.venueLocation,
                nhl_games.easternStartTime,
                nhl_games.gameStateId,
                nhl_games.homeScore,
                nhl_games.awayScore,
                home_teams.teamLogo AS homeLogo,
                away_teams.teamLogo AS awayLogo,
                nhl_games.gameType,
                nhl_games.gameNumber,
                nhl_games.season,
                home_teams.id AS homeTeamId,
                home_teams.fullName AS home_team_name,
                away_teams.fullName AS away_team_name,
                away_teams.id AS awayTeamId,
                nhl_games.gameOutcome
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
                    $homeTeamID = $row['homeTeamId'];
                    $awayTeamID = $row['awayTeamId'];
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
                        $formatted_outcome = '';
                    }
                    else if ($game_outcome == 'OT') {
                        $formatted_outcome = "(OT)";
                    } else if ($game_outcome == 'SO') {
                        $formatted_outcome = "(SO)";
                    }

                    $gameDatetime = new DateTime($game_date);
                    $formatted_gameDate = $gameDatetime->format('m/d/Y');
                }

?>

  <div class="bg-slate-900 text-white py-4">
    <!-- Dashboard Layout -->
    <div class="dashboard-container">
      
      <!-- Game Header Card -->
      <div class="dashboard-card header-card">
        <div class='flex items-center justify-between space-x-8'>
          <!-- Home Team -->
          <a href='team_details.php?team_id=<?= htmlspecialchars($homeTeamID) ?>' class='flex flex-col items-center gap-2'>
            <img src='<?= htmlspecialchars($homeLogo) ?>' alt='<?= htmlspecialchars($homeTeamName) ?>' class='team-logo h-24 w-auto'>
            <span class='team-name text-xl'><?= htmlspecialchars($homeTeamName) ?> (H)</span>
          </a>
          
          <!-- <div class='flex flex-col items-center'> -->
            <div class='score-display my-2'>
              <?= htmlspecialchars($homeScore) ?> - <?= htmlspecialchars($awayScore) ?>
            </div>
            <span class='game-outcome ml-2 text-xl'><?= $formatted_outcome ?></span>
            <div class="mt-2 text-center text-sm">
              <p><?= htmlspecialchars($venue) ?>, <?= htmlspecialchars($venueLocation) ?></p>
              <p><?= htmlspecialchars($formatted_gameDate) ?> <?= htmlspecialchars($formatted_startTime) ?> EST</p>
              <p class="text-base italic"><?= $formatted_season ?> <?= $gameType_text ?> - Game <?= $gameNum ?></p>
            </div>
          
          <!-- Away Team -->
          <a href='team_details.php?team_id=<?= htmlspecialchars($awayTeamID) ?>' class='flex flex-col items-center gap-2'>
            <img src='<?= htmlspecialchars($awayLogo) ?>' alt='<?= htmlspecialchars($awayTeamName) ?>' class='team-logo h-24 w-auto'>
            <span class='team-name text-xl'><?= htmlspecialchars($awayTeamName) ?> (A)</span>
          </a>
        </div>
        
      </div>

            <!-- Roster Card with Tabs -->
<?php
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


                    $home_skaters = array_filter($home_players, fn($p) => $p['skater_position'] !== null && $p['skater_position'] !== '');
                    $home_goalies = array_filter($home_players, fn($p) => $p['goalie_position'] !== null && $p['goalie_position'] !== '');

                    $away_skaters = array_filter($away_players, fn($p) => $p['skater_position'] !== null && $p['skater_position'] !== '');
                    $away_goalies = array_filter($away_players, fn($p) => $p['goalie_position'] !== null && $p['goalie_position'] !== '');


                    function render_skater_table($players, $team_label, $roster_lookup, $teamColor) {
                        echo "<h4 class='mb-2.5 text-2xl text-center'>$team_label</h4>";
                        echo "<div class='w-full overflow-x-auto'>";
                        echo "<table class='roster-table default-zebra-table mx-auto text-center' style='zoom: 0.9'>";
                        echo "<colgroup>";
                        echo "<col class='game_details_skater_stats_name'>";
                        echo "<col class='game_details_skater_stats_number'>";
                        echo "<col class='game_details_skater_stats_position'>";
                        echo "<col class='game_details_skater_stats_goals'>";
                        echo "<col class='game_details_skater_stats_assists'>";
                        echo "<col class='game_details_skater_stats_points'>";
                        echo "<col class='game_details_skater_stats_plusminus'>";
                        echo "<col class='game_details_skater_stats_pim'>";
                        echo "<col class='game_details_skater_stats_hits'>";
                        echo "<col class='game_details_skater_stats_powerplaygoals'>";
                        echo "<col class='game_details_skater_stats_sog'>";
                        echo "<col class='game_details_skater_stats_faceoffpctg'>";
                        echo "<col class='game_details_skater_stats_toi'>";
                        echo "<col class='game_details_skater_stats_blockedshots'>";
                        echo "<col class='game_details_skater_stats_shifts'>";
                        echo "<col class='game_details_skater_stats_giveaways'>";
                        echo "<col class='game_details_skater_stats_takeaways'>";
                        echo "<thead class='default-zebra-table'>";
                        echo "<tr style='color: white; font-weight: bold; background-color: #1F2833' class='default-zebra-table'>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Name</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>#</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Pos</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>G</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>A</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>P</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>+/-</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>PIM</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Hits</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>PPG</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>SOG</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>FO %</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>TOI</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Blocks</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Shifts</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Give</th>";
                        echo "<th class='border border-slate-600 px-2 py-1'>Take</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        foreach ($players as $player) {
                            $player_id = $player['playerID'];
                            $player_name = $roster_lookup[$player_id] ?? 'Unknown';
                            $position = $player['skater_position'];
                            $positionDisplay = ($position == 'L') ? 'LW' : (($position == 'R') ? 'RW' : $position);
                            $formatted_FOPctg = number_format($player['skater_faceoffWinningPctg'] * 100, 1);
                            if ($formatted_FOPctg == 0) {
                                $formatted_FOPctg = '-';
                            }
                            // $totalShots = $row['total_shots'] ?? 0;
                            // $totalBlockedShots = $row['total_blocked_shots'] ?? 0;
                            // $totalMissedShots = $row['total_missed_shots'] ?? 0;
                            
                            echo "<tr class='default-zebra-table'>";
                            echo "<td class='border border-slate-600 px-2 py-1 text-left'><a style='color:navy' href='player_details.php?player_id=" . htmlspecialchars($player_id) ."'>$player_name</a></td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['skater_sweaterNumber'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $position . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_goals']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_assists']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_points']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_plusMinus']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_pim']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_hits']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_powerPlayGoals']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_sog']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($formatted_FOPctg) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_toi']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_blockedShots']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_shifts']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_giveaways']) . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . htmlspecialchars($player['skater_takeaways']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                        echo "</table></div>";
                    }
                    
                    function render_goalie_table($players, $team_label, $roster_lookup) {
                        echo "<div class='roster-table-wrapper'>";
                        echo "<table class='roster-table default-zebra-table border-2 border-slate-600 mx-auto text-center' style='zoom: 0.9'>";
                            echo "<colgroup>";
                                echo "<col class='game_details_goalie_stats_name'>";
                                echo "<col class='game_details_goalie_stats_number'>";
                                echo "<col class='game_details_goalie_stats_pim'>";
                                echo "<col class='game_details_goalie_stats_toi'>";
                                echo "<col class='game_details_goalie_stats_evenStrengthShotsAgainst'>";
                                echo "<col class='game_details_goalie_stats_powerPlayShotsAgainst'>";
                                echo "<col class='game_details_goalie_stats_shorthandedShotsAgainst'>";
                                echo "<col class='game_details_goalie_stats_saveShotsAgainst'>";
                                echo "<col class='game_details_goalie_stats_savePctg'>";
                                echo "<col class='game_details_goalie_stats_evenStrengthGoalsAgainst'>";
                                echo "<col class='game_details_goalie_stats_powerPlayGoalsAgainst'>";
                                echo "<col class='game_details_goalie_stats_shorthandedGoalsAgainst'>";
                                echo "<col class='game_details_goalie_stats_goalsAgainst'>";
                                echo "<col class='game_details_goalie_stats_starter'>";
                                echo "<col class='game_details_goalie_stats_shotsAgainst'>";
                                echo "<col class='game_details_goalie_stats_saves'>";
                            echo "</colgroup>";
                            echo "<thead>";
                                echo "<tr style='color: white; font-weight: bold;' class='default-zebra-table bg-slate-800'>"; // Added missing opening <tr> tag
                                    echo "<th class='border border-slate-600 px-2 py-1 text-left'>Name</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Number</t class='border border-slate-600 px-2 py-1'h>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>PIM</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>TOI</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Even SA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>PP SA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>SH SA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Sv SA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Sv %</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Even GA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>PP GA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>SH GA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>GA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Starter</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>SA</th>";
                                    echo "<th class='border border-slate-600 px-2 py-1'>Saves</th>";
                                echo "</tr>";
                            echo "</thead>";
                            echo "<tbody>";
                    
                        foreach ($players as $player) {
                            $player_id = $player['playerID'];
                            $player_name = $roster_lookup[$player_id] ?? 'Unknown';
                            $goalie_savePctg = $player['goalie_savePctg'];
                            if ($goalie_savePctg == '') {
                                $goalie_savePctg = '-';
                            }
                            $starter = $player['goalie_starter'];
                            if ($starter == 'True') {
                                $player_name = $player_name . " (S)";
                            }
                                                
                            echo "<tr class='default-zebra-table'>";
                            echo "<td class='border border-slate-600 px-2 py-1'><a style='color:navy' href='player_details.php?player_id=" . htmlspecialchars($player_id) ."'>$player_name</a></td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_sweaterNumber'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_pim'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_toi'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_evenStrengthShotsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_powerPlayShotsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_shorthandedShotsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_saveShotsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $goalie_savePctg . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_evenStrengthGoalsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_powerPlayGoalsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_shorthandedGoalsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_goalsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_starter'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_shotsAgainst'] . "</td>";
                            echo "<td class='border border-slate-600 px-2 py-1'>" . $player['goalie_saves'] . "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody></table></div>";
                    }
                    ?>
      <div class="dashboard-card roster-card">
        <div class='flex justify-between items-center' style='border-bottom: 1px solid rgba(69, 162, 158, 0.4);'>
                <h2 class="card-header" style='border: none !important'>Game Rosters & Statistics</h2>
                
                <div class="roster-tabs">
                <div class="roster-tab active" data-team="home">Home Team</div>
                <div class="roster-tab" data-team="away">Away Team</div>
                </div>
        </div>
        <br>
        
        <div class="roster-content active" id="home-roster">
          <?php render_skater_table($home_skaters, $home_team_name, $roster_lookup, 'white' ); ?>
          <?php render_goalie_table($home_goalies, $home_team_name, $roster_lookup); ?>
        </div>
        
        <div class="roster-content" id="away-roster">
          <?php render_skater_table($away_skaters, $away_team_name, $roster_lookup, 'white'); ?>
          <?php render_goalie_table($away_goalies, $away_team_name, $roster_lookup); ?>
        </div>
        
        <p class="text-center text-sm mt-2">(S) indicates the starting goalie</p>
      </div>
      
      <!-- Shot Chart Card -->
      <div class="dashboard-card shot-chart-card">
        <h2 class="card-header">Shot Chart</h2>
        <div class="rink-container relative">
          <img src="../resources/images/hockey-rink2.jpg" id="rink-image" class="rink-image" />
          <canvas id="heatmap-canvas" class="absolute top-0 left-0 w-full h-full"></canvas>
        </div>
        <div id="filters" class="filter-controls max-w-xl play-by-play-key mt-4 flex flex-wrap justify-center gap-3">
          <label class="filter-label">
            <input type="checkbox" class="shot-filter filter-checkbox" value="goal" checked> 
            <span>Goals</span>
          </label>
          <label class="filter-label">
            <input type="checkbox" class="shot-filter filter-checkbox" value="missed-shot" checked> 
            <span>Missed Shots</span>
          </label>
          <label class="filter-label">
            <input type="checkbox" class="shot-filter filter-checkbox" value="shot-on-goal" checked> 
            <span>SOG</span>
          </label>
          <label class="filter-label">
            <input type="checkbox" class="shot-filter filter-checkbox" value="blocked-shot" checked> 
            <span>Blocks</span>
          </label>
          <label class="filter-label">
            <input type="checkbox" class="shot-filter filter-checkbox" value="hit"> 
            <span>Hits</span>
          </label>
        </div>
      </div>
      
      <!-- Play-by-Play Key Card -->
      <div class="dashboard-card pbp-key-card">
        <h2 class="card-header">Play-by-Play Key</h2>
        <div class="grid grid-cols-2 md:grid-cols-2 gap-y-3 gap-x-5">
          <p><strong class="text-blue-300">FO</strong> – Faceoff</p>
          <p><strong class="text-blue-300">SOG</strong> – Shot on Goal</p>
          <p><strong class="text-blue-300">Pen.</strong> – Penalty</p>
          <p><strong class="text-blue-300">Block</strong> – Blocked Shot</p>
          <p><strong class="text-blue-300">Miss</strong> – Missed Shot</p>
          <p><strong class="text-blue-300">Stop</strong> – Stoppage</p>
          <p><strong class="text-blue-300">Give</strong> – Giveaway</p>
          <p><strong class="text-blue-300">Take</strong> – Takeaway</p>
          <p><strong class="text-blue-300">D. Pen.</strong> – Delayed Penalty</p>
          <p><strong class="text-blue-300">Back</strong> – Backhand</p>
          <p><strong class="text-blue-300">Tip</strong> – Tip-in</p>
        </div>
      </div>
      
      
      <!-- Play-by-Play Card -->
<?php
        ### Pagination logic ###
        $limit = 25; // Results per page
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $limit;


        // Get total count (for Load More logic)
        $count_sql = "SELECT COUNT(*) as total FROM nhl_plays WHERE nhl_plays.gameID = $game_id";
        $count_result = mysqli_query($conn, $count_sql);
        $total_rows = mysqli_fetch_assoc($count_result)['total'];

        $start = $offset + 1;
        $end = min($offset + $limit, $total_rows);
        $total_pages = ceil($total_rows / $limit);

        

        $plays_sql = "SELECT * FROM nhl_plays WHERE nhl_plays.gameID = $game_id ORDER BY nhl_plays.period, nhl_plays.timeInPeriod ASC";
        $plays = mysqli_query($conn, $plays_sql);
        ?>
      
      <div class="dashboard-card pbp-card">

        <div class='flex justify-between items-center' style='border-bottom: 1px solid rgba(69, 162, 158, 0.4);'>
                <h2 class="card-header" style='border: none !important'>Play-by-Play</h2>
                
                <div class="shift-chart-link">
                <a href='shift_charts.php?game_id=<?= $game_id ?>' class="shift-charts-link inline-flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    View Shift Charts
                </a>
                </div>
        </div>
<br>


            <!-- Search Filter Fields -->
    <div class="flex flex-wrap justify-center items-center gap-4 mb-4 max-w-[75%] mx-auto">
        <input type="text" id="searchByType" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Type (e.g., 'goal')">
        <input type="text" id="searchByTeam" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Team (tricode, e.g., 'NYR')">
        <input type="text" id="searchByShotType" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Shot Type">
        <input type="text" id="searchByPenalty" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Penalty">
    </div>
        <div class="overflow-x-auto">
          <table id="play-by-play-table" class="min-w-max table-auto border-2 border-slate-600 border-collapse default-zebra-table">
            <thead class='default-zebra-table'>
              <tr class='default-zebra-table'>
                <th class='pbp-col-time-left border-2 border-slate-600'>Per. Time Left</th>
                <th class='pbp-col-type border-2 border-slate-600'>Type</th>
                <th class='pbp-col-coords border-2 border-slate-600'>Coords.</th>
                <th class='pbp-col-team border-2 border-slate-600'>Team</th>
                <th class='pbp-col-fo-winner border-2 border-slate-600'>F/O Winner</th>
                <th class='pbp-col-fo-loser border-2 border-slate-600'>F/O Loser</th>
                <th class='pbp-col-hitter border-2 border-slate-600'>Hitter</th>
                <th class='pbp-col-hittee border-2 border-slate-600'>Hittee</th>
                <th class='pbp-col-shot-type border-2 border-slate-600'>Shot Type</th>
                <th class='pbp-col-shooter border-2 border-slate-600'>Shooter</th>
                <th class='pbp-col-goalie border-2 border-slate-600'>Goalie</th>
                <th class='pbp-col-reason border-2 border-slate-600'>Reason</th>
                <th class='pbp-col-take-give border-2 border-slate-600'>Taker / Giver</th>
                <th class='pbp-col-blocker border-2 border-slate-600'>Blocker</th>
                <th class='pbp-col-scorer border-2 border-slate-600'>Scorer</th>
                <th class='pbp-col-primary-assister border-2 border-slate-600'>1st Assist</th>
                <th class='pbp-col-penalty border-2 border-slate-600'>Penalty</th>
                <th class='pbp-col-committer border-2 border-slate-600'>Committer</th>
                <th class='pbp-col-drawer border-2 border-slate-600'>Drawer</th>
              </tr>
            </thead>
            <tbody class='default-zebra-table'>
              <!-- Your existing PHP code to output table rows -->
              <?php
            // ALL PLAYS DATA FOR HEATMAP
                        $all_plays = [];
                        while ($row = $plays->fetch_assoc()){
                            # Coordinates
                            $formatted_coordinates = $row['xCoord'] . '/' . $row['yCoord'];
                            // echo "<td>".$row['xCoord']."</td>";
                            // echo "<td>".$row['yCoord']."</td>";

                            # saving values for use in onclick event
                            $xCoord = $row['xCoord'];
                            $yCoord = $row['yCoord'];
                            $type = $row['typeDescKey'];

                            echo "<tr class='play-row' 
                            data-x='{$xCoord}' 
                            data-y='{$yCoord}' 
                            data-typedesckey='{$type}'
                            style='color: white; border: 1px solid #bcd6e7'>";
                            


                            $all_plays[] = [
                                'x' => $xCoord,
                                'y' => $yCoord,
                                'typedesckey' => $type
                            ];



                            # Period/Time Remaining
                            $formatted_time = $row['period'] . ' - ' . substr($row['timeRemaining'],0,5);
                            // echo "<td>".$formatted_time."</td>";

                            // echo "<td>".$row['situationCode']."</td>";
                            // echo "<td>".$row['typeCode']."</td>";
                            $eventType = $row['typeDescKey'];
                            if ($eventType == 'goal') {
                                $rowClass = 'goal-row';
                            }

                            $faceoff_winner_id = $row['faceoffWinnerId'];
                            $faceoff_winner_name = isset($roster_lookup[$faceoff_winner_id]) ? $roster_lookup[$faceoff_winner_id] : 'Unknown';
                            $faceoff_loser_id = $row['faceoffLoserId'];
                            $faceoff_loser_name = isset($roster_lookup[$faceoff_loser_id]) ? $roster_lookup[$faceoff_loser_id] : 'Unknown';
                            $hitter_id = $row['hittingPlayerId'];
                            $hitter_name = isset($roster_lookup[$hitter_id]) ? $roster_lookup[$hitter_id] : 'Unknown';
                            $hittee_id = $row['hitteePlayerId'];
                            $hittee_name = isset($roster_lookup[$hittee_id]) ? $roster_lookup[$hittee_id] : 'Unknown';
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
                            $shooter_id = $row['shootingPlayerId'];
                            $shooter_name = isset($roster_lookup[$shooter_id]) ? $roster_lookup[$shooter_id] : 'Unknown';
                            $goalie_id = $row['goalieInNetId'];
                            $goalie_name = isset($roster_lookup[$goalie_id]) ? $roster_lookup[$goalie_id] : 'Unknown';

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
                            // echo "<td>".$formatted_reason."</td>";

                            # Takeaway/Giveaway Player
                            $take_give_id = $row['takeawayGiveawayPlayerId'];
                            $take_give_name = isset($roster_lookup[$take_give_id]) ? $roster_lookup[$take_give_id] : 'Unknown';
                            // echo "<td>".htmlspecialchars($take_give_name)."</td>";

                            # Shot Blocker
                            $blocker_id = $row['blockingPlayerId'];
                            $blocker_name = isset($roster_lookup[$blocker_id]) ? $roster_lookup[$blocker_id] : 'Unknown';
                            // echo "<td>".htmlspecialchars($blocker_name)."</td>";

                            # Scorer
                            $scorer_id = $row['scoringPlayerId'];
                            $scorer_name = isset($roster_lookup[$scorer_id]) ? $roster_lookup[$scorer_id] : 'Unknown';
                            // echo "<td>".htmlspecialchars($scorer_name)."</td>";

                            # Assister
                            $assister_id = $row['assist1PlayerId'];
                            $assister_name = isset($roster_lookup[$assister_id]) ? $roster_lookup[$assister_id] : 'Unknown';
                            // echo "<td>".htmlspecialchars($assister_name)."</td>";

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
                            // echo "<td>" . $row['penaltyType'] . ' ' . $formatted_severity . "</td>";
                            $committer_id = $row['committerId'];
                            $committer_name = isset($roster_lookup[$committer_id]) ? $roster_lookup[$committer_id] : 'Unknown';
                            $drawer_id = $row['drawerId'];
                            $drawer_name = isset($roster_lookup[$drawer_id]) ? $roster_lookup[$drawer_id] : 'Unknown';
                            // echo "<td>".htmlspecialchars($committer_name)."</td>";
                            // echo "<td>".htmlspecialchars($drawer_name)."</td>";

                            
                            

                            echo "</tr>";

                        }
              ?>


                        <!-- GET ALL DATA FOR FILTERING -->
              <script>
const allPlays = <?php 
    // Re-query to get all play data for JavaScript
    mysqli_data_seek($plays, 0);
    $plays_data = [];
    while ($row = mysqli_fetch_assoc($plays)) {
        $formatted_time = $row['period'] . ' - ' . substr($row['timeRemaining'],0,5);
        $playType = $row['typeDescKey'];
        // Format play type (your existing logic)
        $formatted_playType = $playType == 'period-start' ? 'Per. Start' : 
                             ($playType == 'faceoff' ? 'FO' : 
                             ($playType == 'hit' ? 'Hit' : 
                             ($playType == 'shot-on-goal' ? 'SOG' : 
                             ($playType == 'goal' ? 'Goal' : 
                             ($playType == 'stoppage' ? 'Stop' : 
                             ($playType == 'giveaway' ? 'Give' : 
                             ($playType == 'takeaway' ? 'Takea' : 
                             ($playType == 'blocked-shot' ? 'Block' : 
                             ($playType == 'missed-shot' ? 'Miss' : 
                             ($playType == 'penalty' ? 'Pen.' : 
                             ($playType == 'delayed-penalty' ? 'D. Pen.' : 
                             ($playType == 'period-end' ? 'Per. End' : $playType))))))))))));
        
        // Format reason
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
        
        // Format penalty
        if ($row['penaltySeverity'] == 'MIN') {
            $formatted_penalty = $row['penaltyType'] . ' (2)';
        } else if ($row['penaltySeverity'] == 'MAJ') {
            $formatted_penalty = $row['penaltyType'] . ' (4)';
        } else if (empty($row['penaltySeverity'])) {
            $formatted_penalty = '-';
        } else {
            $formatted_penalty = $row['penaltyType'] . ' ' . $row['penaltySeverity'];
        }
        
        // Format shot type
        $formatted_shotType = ucfirst($row['shotType']);
        if ($formatted_shotType == 'Backhand') {
            $formatted_shotType = 'Back';
        } else if ($formatted_shotType == 'Tip-in') {
            $formatted_shotType = 'Tip';
        } else if ($formatted_shotType == ''){
            $formatted_shotType = '-';
        }
        
        $plays_data[] = [
            'formatted_time' => $formatted_time,
            'formatted_playType' => $formatted_playType,
            'formatted_coordinates' => $row['xCoord'] . '/' . $row['yCoord'],
            'event_team_tricode' => $row['eventOwnerTeamId'] ? $team_tricode_lookup[$row['eventOwnerTeamId']] : 'N/A',
            'typeDescKey' => $row['typeDescKey'],
            'faceoff_winner_name' => isset($roster_lookup[$row['faceoffWinnerId']]) ? $roster_lookup[$row['faceoffWinnerId']] : '-',
            'faceoff_loser_name' => isset($roster_lookup[$row['faceoffLoserId']]) ? $roster_lookup[$row['faceoffLoserId']] : '-',
            'hitter_name' => isset($roster_lookup[$row['hittingPlayerId']]) ? $roster_lookup[$row['hittingPlayerId']] : '-',
            'hittee_name' => isset($roster_lookup[$row['hitteePlayerId']]) ? $roster_lookup[$row['hitteePlayerId']] : '-',
            'formatted_shotType' => $formatted_shotType,
            'shooter_name' => isset($roster_lookup[$row['shootingPlayerId']]) ? $roster_lookup[$row['shootingPlayerId']] : '-',
            'goalie_name' => isset($roster_lookup[$row['goalieInNetId']]) ? $roster_lookup[$row['goalieInNetId']] : '-',
            'formatted_reason' => $formatted_reason,
            'take_give_name' => isset($roster_lookup[$row['takeawayGiveawayPlayerId']]) ? $roster_lookup[$row['takeawayGiveawayPlayerId']] : '-',
            'blocker_name' => isset($roster_lookup[$row['blockingPlayerId']]) ? $roster_lookup[$row['blockingPlayerId']] : '-',
            'scorer_name' => isset($roster_lookup[$row['scoringPlayerId']]) ? $roster_lookup[$row['scoringPlayerId']] : '-',
            'assister_name' => isset($roster_lookup[$row['assist1PlayerId']]) ? $roster_lookup[$row['assist1PlayerId']] : '-',
            'formatted_penalty' => $formatted_penalty,
            'committer_name' => isset($roster_lookup[$row['committerId']]) ? $roster_lookup[$row['committerId']] : '-',
            'drawer_name' => isset($roster_lookup[$row['drawerId']]) ? $roster_lookup[$row['drawerId']] : '-',
            
            // Store original values for filtering
            'xCoord' => $row['xCoord'],
            'yCoord' => $row['yCoord'],
            'shotType' => $row['shotType'] ?? '',
            'penaltyType' => $row['penaltyType'] ?? '',
            'event_team_tricode' => $row['eventOwnerTeamId'] ? $team_tricode_lookup[$row['eventOwnerTeamId']] : ''
        ];
    }
    
    echo json_encode($plays_data);
?>
</script>
            </tbody>
          </table>
        </div>

        
        
        <!-- Pagination OLD BLOCK CAN GO HERE -->
            <div id="pagination" class="flex justify-center space-x-4 mt-6 text-white">
        <!-- Pagination buttons will be dynamically generated -->
    </div>
    <br>
</div>

      <?php
// Fetch ALL plays for the heatmap (no LIMIT)
$all_plays_query = "SELECT * FROM nhl_plays WHERE gameID = $game_id";
$plays_result = mysqli_query($conn, $all_plays_query);
$all_plays = [];
while ($row = mysqli_fetch_assoc($plays_result)) {
    $all_plays[] = [
        'x' => $row['xCoord'],
        'y' => $row['yCoord'],
        'typedesckey' => $row['typeDescKey']
    ];
}?>
    </div>
  </div>
        



  <?php 
        } else {
            echo "<p>No game ID provided.</p>";
        }
 ?>

  <!-- Roster Tab Switching Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const tabs = document.querySelectorAll('.roster-tab');
      
      tabs.forEach(tab => {
        tab.addEventListener('click', function() {
          // Remove active class from all tabs
          tabs.forEach(t => t.classList.remove('active'));
          
          // Add active class to clicked tab
          this.classList.add('active');
          
          // Hide all content
          const contents = document.querySelectorAll('.roster-content');
          contents.forEach(c => c.classList.remove('active'));
          
          // Show selected content
          const team = this.getAttribute('data-team');
          document.getElementById(`${team}-roster`).classList.add('active');
        });
      });
    });
  </script>


<!-- PBP FILTERING SCRIPTS -->
 <script>
document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector("#play-by-play-table tbody");
    const searchByType = document.getElementById("searchByType");
    const searchByTeam = document.getElementById("searchByTeam");
    const searchByShotType = document.getElementById("searchByShotType");
    const searchByPenalty = document.getElementById("searchByPenalty");
    const pagination = document.getElementById("pagination");

    let currentPage = 1;
    const pageSize = 50; // Number of rows per page

        console.log("Total plays in dataset:", allPlays.length);


    // Function to render rows dynamically
    function renderTable(data) {
        tableBody.innerHTML = ""; // Clear the table first
        const start = (currentPage - 1) * pageSize;
        const end = start + pageSize;
        const paginatedData = data.slice(start, end);

        paginatedData.forEach(row => {
            const tr = document.createElement("tr");
            tr.className = "play-row";
            if (row.typeDescKey === "goal") {
                tr.className += " goal-row";
            }
            
            // Add data attributes for visualization if needed
            if (row.xCoord && row.yCoord) {
                tr.setAttribute("data-x", row.xCoord);
                tr.setAttribute("data-y", row.yCoord);
                tr.setAttribute("data-typedesckey", row.typeDescKey);
            }
            
            tr.style.color = "white";
            tr.style.border = "1px solid #bcd6e7";

            // Create cells with proper styling
            tr.innerHTML = `
                <td class='pbp-col-time-left border-2 border-slate-600'>${row.formatted_time || '-'}</td>
                <td class='pbp-col-type border-2 border-slate-600'>${row.formatted_playType || '-'}</td>
                <td class='pbp-col-coords border-2 border-slate-600'>${row.formatted_coordinates || '-'}</td>
                <td class='pbp-col-team border-2 border-slate-600'>${row.event_team_tricode || '-'}</td>
                <td class='pbp-col-fo-winner border-2 border-slate-600'>${row.faceoff_winner_name || '-'}</td>
                <td class='pbp-col-fo-loser border-2 border-slate-600'>${row.faceoff_loser_name || '-'}</td>
                <td class='pbp-col-hitter border-2 border-slate-600'>${row.hitter_name || '-'}</td>
                <td class='pbp-col-hittee border-2 border-slate-600'>${row.hittee_name || '-'}</td>
                <td class='pbp-col-shot-type border-2 border-slate-600'>${row.formatted_shotType || '-'}</td>
                <td class='pbp-col-shooter border-2 border-slate-600'>${row.shooter_name || '-'}</td>
                <td class='pbp-col-goalie border-2 border-slate-600'>${row.goalie_name || '-'}</td>
                <td class='pbp-col-reason border-2 border-slate-600'>${row.formatted_reason || '-'}</td>
                <td class='pbp-col-take-give border-2 border-slate-600'>${row.take_give_name || '-'}</td>
                <td class='pbp-col-blocker border-2 border-slate-600'>${row.blocker_name || '-'}</td>
                <td class='pbp-col-scorer border-2 border-slate-600'>${row.scorer_name || '-'}</td>
                <td class='pbp-col-primary-assister border-2 border-slate-600'>${row.assister_name || '-'}</td>
                <td class='pbp-col-penalty border-2 border-slate-600'>${row.formatted_penalty || '-'}</td>
                <td class='pbp-col-committer border-2 border-slate-600'>${row.committer_name || '-'}</td>
                <td class='pbp-col-drawer border-2 border-slate-600'>${row.drawer_name || '-'}</td>
            `;
            tableBody.appendChild(tr);
        });
    }

    // Function to render pagination controls
    function renderPagination(data) {
        pagination.innerHTML = ""; // Clear existing pagination controls
        const totalPages = Math.ceil(data.length / pageSize);
        
        if (totalPages <= 1) {
            // No need for pagination if there's only one page
            return;
        }

        // Previous button
        if (currentPage > 1) {
            const prevButton = document.createElement("button");
            prevButton.textContent = "Previous";
            prevButton.className = "px-3 py-1 bg-blue-600 text-white rounded mr-2";
            prevButton.addEventListener("click", () => {
                currentPage--;
                updateTableAndPagination(data);
            });
            pagination.appendChild(prevButton);
        }

        // Page numbers - only show up to 5 pages with ellipsis
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, startPage + 4);

        // First page if we're showing ellipsis
        if (startPage > 1) {
            const firstButton = document.createElement("button");
            firstButton.textContent = "1";
            firstButton.className = "px-3 py-1 bg-slate-700 text-white rounded mx-1";
            firstButton.addEventListener("click", () => {
                currentPage = 1;
                updateTableAndPagination(data);
            });
            pagination.appendChild(firstButton);
            
            if (startPage > 2) {
                const ellipsis = document.createElement("span");
                ellipsis.textContent = "...";
                ellipsis.className = "px-2 text-white";
                pagination.appendChild(ellipsis);
            }
        }

        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement("button");
            pageButton.textContent = i;
            pageButton.className = i === currentPage 
                ? "px-3 py-1 bg-blue-600 text-white rounded mx-1" 
                : "px-3 py-1 bg-slate-700 text-white rounded mx-1";
            pageButton.addEventListener("click", () => {
                currentPage = i;
                updateTableAndPagination(data);
            });
            pagination.appendChild(pageButton);
        }

        // Last page if needed
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                const ellipsis = document.createElement("span");
                ellipsis.textContent = "...";
                ellipsis.className = "px-2 text-white";
                pagination.appendChild(ellipsis);
            }
            
            const lastButton = document.createElement("button");
            lastButton.textContent = totalPages;
            lastButton.className = "px-3 py-1 bg-slate-700 text-white rounded mx-1";
            lastButton.addEventListener("click", () => {
                currentPage = totalPages;
                updateTableAndPagination(data);
            });
            pagination.appendChild(lastButton);
        }

        // Next button
        if (currentPage < totalPages) {
            const nextButton = document.createElement("button");
            nextButton.textContent = "Next";
            nextButton.className = "px-3 py-1 bg-blue-600 text-white rounded ml-2";
            nextButton.addEventListener("click", () => {
                currentPage++;
                updateTableAndPagination(data);
            });
            pagination.appendChild(nextButton);
        }
    }

    function filterTable() {
        const typeFilter = searchByType.value.toLowerCase();
        const teamFilter = searchByTeam.value.toLowerCase();
        const shotTypeFilter = searchByShotType.value.toLowerCase();
        const penaltyFilter = searchByPenalty.value.toLowerCase();

        return allPlays.filter(row => {
            const matchesType = !typeFilter || (row.typeDescKey && row.typeDescKey.toLowerCase().includes(typeFilter));
            const matchesTeam = !teamFilter || (row.event_team_tricode && row.event_team_tricode.toLowerCase().includes(teamFilter));
            const matchesShotType = !shotTypeFilter || (row.shotType && row.shotType.toLowerCase().includes(shotTypeFilter));
            const matchesPenalty = !penaltyFilter || (row.penaltyType && row.penaltyType.toLowerCase().includes(penaltyFilter));

            return matchesType && matchesTeam && matchesShotType && matchesPenalty;
        });
    }

    // Function to update table and pagination
    function updateTableAndPagination(data) {
        renderTable(data);
        renderPagination(data);
        
        // Add a counter to show how many results were found
        const resultCount = document.createElement("div");
        resultCount.className = "mt-2 text-center text-white";
        resultCount.textContent = `Showing ${Math.min(data.length, pageSize)} of ${data.length} results`;
        pagination.appendChild(resultCount);
    }

    // Attach event listeners for filtering
    const filterInputs = [searchByType, searchByTeam, searchByShotType, searchByPenalty];
    filterInputs.forEach(input => {
        input.addEventListener("input", () => {
            currentPage = 1; // Reset to first page on filter change
            const filteredData = filterTable();
            updateTableAndPagination(filteredData);
        });
    });

    // Initially render all rows and pagination
    updateTableAndPagination(allPlays);
});
</script>



<!-- HEATMAP SCRIPTS -->

<script src="https://cdn.jsdelivr.net/npm/simpleheat/simpleheat.js"></script>
<script>
    const heatmapPlays = <?= json_encode($all_plays) ?>;
    const rinkXMin = -100, rinkXMax = 100;
    const rinkYMin = -42.5, rinkYMax = 42.5;
    const rink = document.getElementById("rink-image");
    const heatmapCanvas = document.getElementById("heatmap-canvas");
    const heat = simpleheat(heatmapCanvas);
    
    let shotPoints = []; // Declare this globally so resize can access it
    
    function updateCanvasSize() {
        heatmapCanvas.width = rink.clientWidth;
        heatmapCanvas.height = rink.clientHeight;
        heat.resize();
    }
    
    function transformCoords(x, y) {
        const width = rink.clientWidth;
        const height = rink.clientHeight;
        
        const paddingX = 0.1;
        const paddingY = 0.1;
        
        const usableWidth = width * (1 - 2 * paddingX);
        const usableHeight = height * (1 - 2 * paddingY);
        
        const xPx = (x - rinkXMin) / (rinkXMax - rinkXMin) * usableWidth + width * paddingX;
        const yPx = height - ((y - rinkYMin) / (rinkYMax - rinkYMin) * usableHeight + height * paddingY);
        
        return [Math.max(0, Math.min(width, xPx)), Math.max(0, Math.min(height, yPx))];
    }
    
    function filterShotData(selectedTypes) {
        const pointMap = new Map();
        
        heatmapPlays.forEach(play => {
            const x = parseFloat(play.x);
            const y = parseFloat(play.y);
            const type = play.typedesckey;
            
            if (!isNaN(x) && !isNaN(y) && selectedTypes.includes(type)) {
                const [xPx, yPx] = transformCoords(x, y);
                const key = `${Math.round(xPx)},${Math.round(yPx)}`;
                
                if (!pointMap.has(key)) {
                    pointMap.set(key, [xPx, yPx, 1]);
                } else {
                    pointMap.get(key)[2] += 1;
                }
            }
        });
        
        return Array.from(pointMap.values()).map(point => {
            point[2] = Math.log(point[2] + 1);
            return point;
        });
    }
    
    function updateHeatmap() {
        const selectedTypes = Array.from(document.querySelectorAll('.shot-filter:checked')).map(cb => cb.value);
        shotPoints = filterShotData(selectedTypes);
        drawHeatmap(shotPoints);
    }
    
    function drawHeatmap(points) {
        heat.clear();
        heat.data(points);
        
        const maxIntensity = Math.max(...points.map(point => point[2]), 1);
        heat.max(maxIntensity);
        
        heat.radius(30, 15);
        heat.gradient({
            0.3: 'rgba(0, 0, 255, 0.7)',
            0.5: 'rgba(0, 255, 0, 0.7)',
            0.8: 'rgba(255, 165, 0, 0.7)',
            1.0: 'rgba(255, 0, 0, 0.7)'
        });
        
        heat.draw();
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        updateCanvasSize();
        
        // Initialize with all valid shot types
        const validTypes = ['shot-on-goal', 'missed-shot', 'goal', 'blocked-shot', 'hit'];
        const filters = document.querySelectorAll('.shot-filter');
        filters.forEach(filter => filter.addEventListener('change', updateHeatmap));
        
        // Initial render
        updateHeatmap();
    });
    
    window.addEventListener('resize', () => {
        updateCanvasSize();
        drawHeatmap(shotPoints);
    });
</script>

</div> <!-- Close dashboard-container -->
            </div>
<?php include 'footer.php'; ?>
</body>
</html>