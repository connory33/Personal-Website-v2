<!doctype html>
<html lang="en" class="min-h-screen">
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
  <body>
  <?php include 'header.php'; ?>
    <div class="text-white text-center w-full oveflow-x-auto" style='background-color: #343a40'>
        <br><br>

        <?php
        include('db_connection.php');

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Check if the 'game_id' is passed in the URL
        if (isset($_GET['season_id'])) {
                $season_id = $_GET['season_id'];
                $currentSeason = $_GET['season_id'] ?? '20232024'; // Default if not set
                $seasonYear1 = substr($currentSeason, 0, 4);
                $seasonYear2 = substr($currentSeason, 4, 4);
                

                $seasons = ['19171918', '19181919', '19201921', '19211922', '19221923', '19231924', '19241925', '19251926', '19261927', '19271928',
                '19281929', '19291930', '19301931', '19311932', '19321933', '19331934', '19341935', '19351936', '19361937', '19371938',
                '19381939', '19391940', '19401941', '19411942', '19421943', '19431944', '19441945', '19451946', '19461947', '19471948',
                '19481949', '19491950', '19501951', '19511952', '19521953', '19531954', '19541955', '19551956', '19561957', '19571958',
                '19581959', '19591960', '19601961', '19611962', '19621963', '19631964', '19641965', '19651966', '19661967', '19671968',
                '19681969', '19691970', '19701971', '19711972', '19721973', '19731974', '19741975', '19751976', '19761977', '19771978',
                '19781979', '19791980', '19801981', '19811982', '19821983', '19831984', '19841985', '19851986', '19861987', '19871988',
                '19881989', '19891990', '19901991', '19911992', '19921993', '19931994', '19941995', '19951996', '19961997', '19971998',
                '19981999', '19992000', '20002001', '20012002', '20022003', '20032004', '20042005', '20052006', '20062007', '20072008',
                '20082009', '20092010', '20102011', '20112012', '20122013', '20132014', '20142015', '20152016', '20162017', '20172018',
                '20182019', '20192020', '20202021', '20212022', '20222023', '20232024', '20242025'];

                $seasons = array_reverse($seasons); // Reverse the order of seasons to show the latest first
            ?>
            <!-- Bracket Header -->
            <h2 class="text-2xl font-bold mb-4 text-white text-center">
            Playoff Bracket (<?php echo $seasonYear1 . '-' . $seasonYear2; ?>)
            </h2>

            <div class="mx-auto w-fit px-6 py-4 rounded-md text-black flex items-center space-x-4 border border-slate-600 bg-slate-800">
            <label for="seasonSelect" class="mr-2 text-white font-semibold">Select Season:</label>
            <select id="seasonSelect" class="px-3 py-1 rounded text-black" onchange="changeSeason(this.value)">
                <option value="">Season</option>
                <?php foreach ($seasons as $seasonID): ?>
                <?php 
                    $seasonYear1 = substr($seasonID, 0, 4);
                    $seasonYear2 = substr($seasonID, 4, 4);
                    $selected = ($seasonID === $currentSeason) ? 'selected' : '';
                ?>
                <!-- Bracket Header -->
                    <h2 class="text-2xl font-bold mb-4 text-white text-center">
                    Bracket (<?php echo $seasonYear1 . '-' . $seasonYear2; ?>)
                    </h2>
                <option value="<?php echo $seasonID; ?>" <?php echo $selected; ?>>
                    <?php echo $seasonYear1 . "-" . $seasonYear2; ?>
                </option>
                <?php endforeach; ?>
            </select>
            </div>
<br>
<hr class='w-4/5 border-white align-center mx-auto'>
            <script>
            function changeSeason(seasonId) {
            if (seasonId) {
                const url = new URL(window.location.href);
                url.searchParams.set('season_id', seasonId);
                window.location.href = url.toString();
            }
            }
            </script>
<?php
                $sql = "SELECT playoff_results.*, 
                               bottomSeedTeam.id AS bottomSeedTeamID,
                               bottomSeedTeam.fullName AS bottomSeedTeamName,
                               bottomSeedTeam.triCode AS bottomSeedTeamTriCode,
                               bottomSeedTeam.teamLogo AS bottomSeedTeamLogo,
                               bottomSeedTeam.teamColor1 AS bottomSeedTeamColor1,
                               bottomSeedTeam.teamColor2 AS bottomSeedTeamColor2,
                               bottomSeedTeam.division AS bottomSeedTeamDivision,
                               topSeedTeam.id AS topSeedTeamID,
                               topSeedTeam.fullName AS topSeedTeamName,
                               topSeedTeam.triCode AS topSeedTeamTriCode,
                               topSeedTeam.teamLogo AS topSeedTeamLogo,
                               topSeedTeam.teamColor1 AS topSeedTeamColor1,
                               topSeedTeam.teamColor2 AS topSeedTeamColor2,
                               topSeedTeam.division AS topSeedTeamDivision
                        FROM playoff_results
                        LEFT JOIN nhl_teams AS bottomSeedTeam ON playoff_results.bottomSeedIDs = bottomSeedTeam.id
                        LEFT JOIN nhl_teams AS topSeedTeam ON playoff_results.topSeedIDs = topSeedTeam.id
                        WHERE playoff_results.seasonID = '$season_id'
                        GROUP BY playoff_results.roundNums, playoff_results.seriesLetters";

                $result = mysqli_query($conn, $sql);
                if (!$result) {
                    die("Query failed: " . mysqli_error($conn));
                }


                ?>

                <?php
                $rounds = [];
                while ($row = mysqli_fetch_assoc($result)) {
                    $rounds[$row['roundNums']][] = $row;
                }
                
                // ✅ Now, only after building all rounds, render the bracket
                
                echo "<div class='flex justify-center gap-8 p-6 text-white'>";
                
                foreach ($rounds as $round => $matchups) {
                    echo "<div class='flex flex-col items-center gap-6'>";
                    echo "<div class='text-lg font-bold mb-2'>Round $round</div>";
                
                    foreach ($matchups as $match) {
                        $bottomWins = (int)$match['bottomSeedWins'];
                        $topWins = (int)$match['topSeedWins'];
                
                        $bottomBold = $bottomWins > $topWins ? 'font-bold text-green-600' : '';
                        $topBold = $topWins > $bottomWins ? 'font-bold text-green-600' : '';
                
                        $seriesId = $match['seasonID'] . $match['seriesLetters'];
                        echo "<a href='series_details.php?series_id={$seriesId}' class='no-underline'>";
                        echo "<div class='bg-slate-800 border border-slate-600 p-3 rounded shadow text-center w-60 hover:bg-slate-700 transition'>";
                        // echo "<p>" . $match['division'] . "</p>";
                        echo "<div class='flex justify-between w-full'>";
                        echo "<div class='flex flex-col items-center w-1/2'>";
                        echo "<div class='$bottomBold text-sm'>" . $match['bottomSeedTeamName'] . "<br>(" . $match['bottomSeedTeamDivision'] . ", ". $match['bottomSeedRanks'] . ")</div>";
                        echo "<div class='$bottomBold text-lg'>{$bottomWins}</div>";
                        echo "</div>";
                
                        echo "<div class='flex flex-col items-center w-1/2'>";
                        echo "<div class='$topBold text-sm'>" . $match['topSeedTeamName'] . "<br>(" . $match['topSeedTeamDivision'] . ", ". $match['topSeedRanks'] . ")</div>";
                        echo "<div class='$topBold text-lg'>{$topWins}</div>";
                        echo "</div>";
                
                        echo "</div>"; // flex row
                        echo "</div>"; // box
                        echo "</a>";
                    }
                
                    echo "</div>"; // round
                }
                
                echo "</div>"; // all rounds
                
        
        
        
        }
?>

    </div>
    </body>
    <?php include 'footer.php'; ?>
</html>