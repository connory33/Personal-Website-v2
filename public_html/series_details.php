<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Connor Young</title>


    <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />

       <script src="https://cdn.tailwindcss.com"></script>

  </head>
 <!-- Header -->
 <?php include 'header.php'; ?>
  <body>
    <div class="text-white text-center min-h-screen" style='background-color: #343a40'>
        <br><br>

        <?php
        include('db_connection.php');

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Check if the 'game_id' is passed in the URL
        if (isset($_GET['series_id'])) {
          $series_id = $_GET['series_id'];
          // print($series_id);
          $season = substr($series_id, 0, 8);
          $letter = substr($series_id, 8, 1);

          $headerSQL = "SELECT playoff_results.*, away_teams.fullName AS awayTeamName, home_teams.fullName AS homeTeamName,
                        home_teams.teamLogo AS homeLogo, away_teams.teamLogo AS awayLogo, away_teams.id AS awayTeamId, home_teams.id AS homeTeamId,
                        winning_teams.fullName AS winningTeamName, losing_teams.fullName AS losingTeamName
                        FROM playoff_results
                        LEFT JOIN nhl_teams AS home_teams ON playoff_results.winningTeamIds = home_teams.id
                        LEFT JOIN nhl_teams AS away_teams ON playoff_results.losingTeamIds = away_teams.id
                        LEFT JOIN nhl_teams AS winning_teams ON playoff_results.winningTeamIds = winning_teams.id
                        LEFT JOIN nhl_teams AS losing_teams ON playoff_results.losingTeamIds = losing_teams.id
                        WHERE playoff_results.seasonID = '$season' AND playoff_results.seriesLetters = '$letter'";


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

            echo "<div class='max-w-[95%] mx-auto bg-slate-800 text-white py-6 px-4 rounded-lg shadow-lg mb-8 border-2 border-slate-600'>";
            echo "<div class='flex flex-col items-center space-y-4'>"; // Removed flex-grow on the outer container
            echo "<h2 class='text-4xl font-bold text-white'>Playoff Series Details</h2>";
            // Team logos and names
            echo "<div class='flex items-center justify-center space-x-6'>";
            
            echo "<a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($row['homeTeamId']) . "'>" . "<img src='" 
              . htmlspecialchars($row['homeLogo']) . "' alt='homeLogo' class='h-20 max-w-xs'>" . "</a>";


              # THIS IS WRONG - NEED TO MAP TOP/BOTTOM SEEDS TO HOME/AWAY
            echo "<a class='text-3xl font-bold text-center whitespace-nowrap' href='https://connoryoung.com/team_details.php?team_id=>" 
              . htmlspecialchars($row['homeTeamId']) . "'>" . htmlspecialchars($row['homeTeamName']) . " (" . $row['topSeedRanks'] . ', ' . $row['topSeedRankAbbrevs'] .")</a> <span class='mx-2 text-3xl'>vs.</span>"
              . "<a class='text-3xl font-bold text-center whitespace-nowrap' href='https://connoryoung.com/team_details.php?team_id=>" 
              . htmlspecialchars($row['awayTeamId']) . "'>" . htmlspecialchars($row['awayTeamName']) . " (" . $row['bottomSeedRanks'] . ")</a></h3>";

            echo "<a href='https://connoryoung.com/team_details.php?team_id=" . htmlspecialchars($row['awayTeamId']) . "'>" . "<img src='" 
              . htmlspecialchars($row['awayLogo']) . "' alt='homeLogo' class='h-20 max-w-xs'>" . "</a>";
            echo "</div>";

            echo "<p>Winner: " . htmlspecialchars($row['winningTeamName']) . "</p>";
            echo "<p>" . $row['topSeedWins'] . " - " . $row['bottomSeedWins'] . "</p>";
            echo "</div>"; // flex-col container
            echo "<p>Round " . htmlspecialchars($row['roundNums']) . "</p>";
            echo "<p>Best of " . htmlspecialchars($row['length']) . "</p>";
            echo "<a href='https://nhl.com" . $row['seriesLinks'] . "' class='text-blue-500 hover:underline'>NHL.com Series URL</a>";
            echo "</div>"; // banner wrapper

            echo "<hr style='width:80%; background-color:white' class='mx-auto'>";
            echo "<br>";
            echo "<p class='text-2xl font-bold'>Game Results</p>";
            echo "<p class='text-sm'>Click on the game number to view the game details.</p>";


}

          $sql = "SELECT * FROM playoff_results WHERE seasonID = '$season' AND seriesLetters = '$letter'";
          $result = mysqli_query($conn, $sql);

          $topSeedSchedule = ['Home', 'Home', 'Away', 'Away', 'Home', 'Away', 'Home'];
          ?>
          <br>
          <div>
            
            <table class='default-zebra-table border-2 rounded-lg border-slate-600 w-4/5 mx-auto'>
              <thead class='bg-slate-800 text-white'>
                <tr>
                  <th>Game</th>
                  <th>Top Seed H/A</th>
                  <!-- <th>Bracket Logos</th> -->
                  <!-- <th>Top Seed Ranks</th> -->
                  <!-- <th>Top Seed Rank Abbrev</th> -->
                  <!-- <th>Bottom Seed Ranks</th> -->
                  <!-- <th>Bottom Seed Rank Abbrev</th> -->
                  <!-- <th>Winning Team ID</th>
                  <th>Losing Team ID</th> -->
                  <!-- <th>Game ID</th> -->
                  <th>Away Team Score</th>
                  <th>Home Team Score</th>
                  <th>Gamecenter</th>
                  <th>Series Status Top Seed Wins</th>
                  <th>Series Status Bottom Seed Wins</th>
                  <!-- <th>Full Coverage URL</th> -->
                  <th>Game Length</th>
                </tr>
              </thead>
              <tbody>

          <?php

          
          $count=1;
          while ($row = mysqli_fetch_assoc($result)) {
            $bracketLogos = $row['bracketLogos'];
            // $topSeedRanks = $row['topSeedRanks'];
            // $topSeedRankAbbrev = $row['topSeedRankAbbrevs'];
            // $bottomSeedRanks = $row['bottomSeedRanks'];
            // $bottomSeedRankAbbrev = $row['bottomSeedRankAbbrevs'];
            $winningTeamID = $row['winningTeamIds'];
            $losingTeamID = $row['losingTeamIds'];
            $gameID = $row['gameId'];
            $awayTeamScore = $row['awayTeamScore'];
            $homeTeamScore = $row['homeTeamScore'];
            $gameCenterLink = $row['gameCenterLink'];
            $gameCenterLink = "<a href='https://nhl.com" . $gameCenterLink . "' class='text-blue-500 hover:underline'>Link</a>";
            $seriesStatusTopSeedWins = $row['seriesStatusTopSeedWins'];
            $seriesStatusBottomSeedWins = $row['seriesStatusBottomSeedWins'];
            $fullCoverageURL = $row['fullCoverageURL'];
            $lastPeriodType = $row['lastPeriodType'];

            echo "<tr>";
            echo "<td><a href='https://connoryoung.com/game_details.php?game_id={$gameID}'>" . $count . "</a></td>";
            echo "<td>" . htmlspecialchars($topSeedSchedule[$count - 1]) . "</td>";
            // echo "<td>$bracketLogos</td>";
            // echo "<td>$topSeedRanks</td>";
            // echo "<td>$topSeedRankAbbrev</td>";
            // echo "<td>$bottomSeedRanks</td>";
            // echo "<td>$bottomSeedRankAbbrev</td>";
            // echo "<td>$winningTeamID</td>";
            // echo "<td>$losingTeamID</td>";
            // echo "<td>$gameID</td>";
            echo "<td>$awayTeamScore</td>";
            echo "<td>$homeTeamScore</td>";
            echo "<td>$gameCenterLink</td>";
            echo "<td>$seriesStatusTopSeedWins</td>";
            echo "<td>$seriesStatusBottomSeedWins</td>";
            // echo "<td>$fullCoverageURL</td>";
            echo "<td>$lastPeriodType</td>";
            echo "</tr>";

            $count+=1;
          }

          echo "</tbody>";
          echo "</table>";
          echo "<br><br>";



        }
?>

    </div>
    </div>
    </body>
    <?php include 'footer.php'; ?>
</html>