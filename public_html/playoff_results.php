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
    <div class="bg-slate-700 text-white text-center">
        <br><br>

        <?php
        include('db_connection.php');

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Check if the 'game_id' is passed in the URL
        if (isset($_GET['season_id'])) {
                $season_id = $_GET['season_id'];


                $sql = "SELECT playoff_results.*, 
                               bottomSeedTeam.id AS bottomSeedTeamID,
                               bottomSeedTeam.fullName AS bottomSeedTeamName,
                               bottomSeedTeam.triCode AS bottomSeedTeamTriCode,
                               bottomSeedTeam.teamLogo AS bottomSeedTeamLogo,
                               bottomSeedTeam.teamColor1 AS bottomSeedTeamColor1,
                               bottomSeedTeam.teamColor2 AS bottomSeedTeamColor2,
                               topSeedTeam.id AS topSeedTeamID,
                               topSeedTeam.fullName AS topSeedTeamName,
                               topSeedTeam.triCode AS topSeedTeamTriCode,
                               topSeedTeam.teamLogo AS topSeedTeamLogo,
                               topSeedTeam.teamColor1 AS topSeedTeamColor1,
                               topSeedTeam.teamColor2 AS topSeedTeamColor2 
                        FROM playoff_results
                        LEFT JOIN nhl_teams AS bottomSeedTeam ON playoff_results.bottomSeedIDs = bottomSeedTeam.id
                        LEFT JOIN nhl_teams AS topSeedTeam ON playoff_results.topSeedIDs = topSeedTeam.id
                        WHERE playoff_results.seasonID = '$season_id'";

                $result = mysqli_query($conn, $sql);
                if (!$result) {
                    die("Query failed: " . mysqli_error($conn));
                }


                ?>

                <h2 class="text-4xl font-bold text-slate-800">Playoff Results</h2><br>
                <table class='default-zebra-table'>
                    <thead>
                        <tr>
                            <th>Season</th>
                            <th>Round</th>
                            <th>Series</th>
                            <th>Series Link</th>
                            <th>Bottom Seed ID</th>
                            <th>Bottom Seed Wins</th>
                            <th>Top Seed ID</th>
                            <th>Top Seed Wins</th>
                        </tr>
                    </thead>
                    <tbody>

                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    $seasonID = $row['seasonID'];
                    $roundNum = $row['roundNums'];
                    $seriesLetter = $row['seriesLetters'];
                    $seriesLink = $row['seriesLinks'];
                    $bottomSeedID = $row['bottomSeedIDs'];
                    $bottomSeedWins = $row['bottomSeedWins'];
                    $topSeedIDs = $row['topSeedIDs'];
                    $topSeedWins = $row['topSeedWins'];
                    $bottomSeedTeamID = $row['bottomSeedTeamID'];
                    $bottomSeedTeamName = $row['bottomSeedTeamName'];
                    $bottomSeedTeamTriCode = $row['bottomSeedTeamTriCode'];
                    $bottomSeedTeamLogo = $row['bottomSeedTeamLogo'];
                    $bottomSeedTeamColor1 = $row['bottomSeedTeamColor1'];
                    $bottomSeedTeamColor2 = $row['bottomSeedTeamColor2'];
                    $topSeedTeamID = $row['topSeedTeamID'];
                    $topSeedTeamName = $row['topSeedTeamName'];
                    $topSeedTeamTriCode = $row['topSeedTeamTriCode'];
                    $topSeedTeamLogo = $row['topSeedTeamLogo'];
                    $topSeedTeamColor1 = $row['topSeedTeamColor1'];
                    $topSeedTeamColor2 = $row['topSeedTeamColor2'];
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($seasonID) . "</td>";
                echo "<td>" . htmlspecialchars($roundNum) . "</td>";
                echo "<td>" . htmlspecialchars($seriesLetter) . "</td>";
                echo "<td>" . htmlspecialchars($seriesLink) . "</td>";
                if ($bottomSeedWins > $topSeedWins) {
                    echo "<td class='font-bold'>" . htmlspecialchars($bottomSeedTeamName) . "</td>";
                    echo "<td class='font-bold'>" . htmlspecialchars($bottomSeedWins) . "</td>";
                } else {
                    echo "<td>" . htmlspecialchars($bottomSeedTeamName) . "</td>";
                    echo "<td>" . htmlspecialchars($bottomSeedWins) . "</td>";
                }
                if ($topSeedWins > $bottomSeedWins) {
                    echo "<td class='font-bold'>" . htmlspecialchars($topSeedTeamName) . "</td>";
                    echo "<td class='font-bold'>" . htmlspecialchars($topSeedWins) . "</td>";
                } else {
                    echo "<td>" . htmlspecialchars($topSeedTeamName) . "</td>";
                    echo "<td>" . htmlspecialchars($topSeedWins) . "</td>";
                }

                echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
        }
?>


    </body>
</html>