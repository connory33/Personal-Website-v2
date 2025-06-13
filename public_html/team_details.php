
<?php include('db_connection.php'); 

        function getFlagSVG($countryCode) {
            $filePath = __DIR__ . "/resources/images/countryflags/" . strtoupper($countryCode) . ".svg";

            if (file_exists($filePath)) {
                $svg = file_get_contents($filePath);
                // Inject class into the SVG for styling
                $svg = preg_replace('/<svg\b/', '<svg class="inline w-10 h-8 align-middle"', $svg);
                return '<span title="' . htmlspecialchars($countryCode) . '">' . $svg . '</span>';
            } else {
                return htmlspecialchars($countryCode); // Fallback: just show the code
            }
        }

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the 'team_id' is passed in the URL
if (isset($_GET['team_id'])) {
    $team_id = $_GET['team_id'];

    // Query for getting overall season stats for the team
$overallSQL = "SELECT * FROM nhl_EOY_team_stats WHERE team_id = $team_id";
$overallStatsResult = mysqli_query($conn, $overallSQL);


    $teamSQL = "SELECT * FROM nhl_teams WHERE id = $team_id";
    $teamResult = mysqli_query($conn, $teamSQL);
    $teamRow = mysqli_fetch_assoc($teamResult);
    $teamName = $teamRow['fullName'];
    $teamLogo = $teamRow['teamLogo'];
    // Team Colors - get team colors and contrast colors for text
    $teamColor1 = $teamRow['teamColor1'];
    $teamColor2 = $teamRow['teamColor2'];
    $teamColor3 = $teamRow['teamColor3'];
    if ($teamColor3 == null) {
        $teamColor3 = $teamRow['teamColor1'];
    }
    $teamColor4 = $teamRow['teamColor4'];
    if ($teamColor4 == null) {
        $teamColor4 = $teamRow['teamColor2'];
    }
    $teamColor5 = $teamRow['teamColor5'];
    function getTextColorForBackground($bgColorHex) {
        // Remove the hash if present
        $bgColorHex = ltrim($bgColorHex, '#');
        
        // Split into R, G, B
        $r = hexdec(substr($bgColorHex, 0, 2));
        $g = hexdec(substr($bgColorHex, 2, 2));
        $b = hexdec(substr($bgColorHex, 4, 2));
        
        // Calculate luminance (brightness)
        $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
        
        // Return black or white depending on brightness
        return ($brightness > 128) ? '#000000' : '#FFFFFF';
    }
    $teamColor1Contrast = getTextColorForBackground($teamColor1);
    $teamColor2Contrast = getTextColorForBackground($teamColor2);
    ?>


    <!doctype html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta name="description" content="">
            <meta name="author" content="">
            <link rel="icon" href=<?php echo $teamLogo ?>>
            <title>Team Details: <?php echo $teamName ?></title>
            <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" href="team-styles.php?team_id=<?php echo $team_id; ?>">
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        nhl: {
                            dark: '#131A24',
                            darkblue: '#1C2333',
                            medium: '#263044',
                            accent: '#00E6FF',
                            accent2: '#45CC8F',
                            text: '#FFFFFF',
                            muted: '#8A97B1'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'inner-highlight': 'inset 0 1px 0 0 rgba(255, 255, 255, 0.1)',
                    }
                }
            }
        }
    </script>

    <style>
    /* Dynamic styles based on team colors */
    .season-tab-button {
    border: 1px solid <?php echo $teamColor2; ?>;
    color: <?php echo $teamColor1Contrast; ?>;
    background: <?php echo $teamColor1.'60'; ?>;
    cursor: pointer;
    transition: all 0.2s ease;
    flex: 1 0 auto;
    padding: 12px 16px;
    text-align: center;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.2s ease;
    }

    .season-tab-button:hover {
    background: <?php echo $teamColor1.'80'; ?>;
    }

    .season-tab-button.active {
    background: <?php echo $teamColor2.'90'; ?> !important;
    font-weight: bold;
    }

    /* Hide inactive tab panes - this is the key fix */
.season-tab-pane {
  display: none;
}

/* Show only active tab pane */
.season-tab-pane.active {
  display: flex;
}


/* Make tables more consistent */
.team-stats-table {
  width: 100%;
  border-collapse: collapse;
}

/* For wide tables that need horizontal scrolling */
@media (max-width: 1200px) {
  .team-stats-table {
    min-width: 1000px;
  }
}

/* Improve tab container layout */
.season-tab {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 16px;
}

/* Ensure tab content container doesn't create layout issues */
.season-tab-content {
  width: 100%;
}
    </style>
        </head>
        <body>
            <header>
                <?php include 'header.php'; ?>
            </header>

                                                                                <!-- COMBINED QUERY FOR SKATERS -->
            <?php
            // Step 1: Create the temp_forwards table
            $sql1 = "
            CREATE TEMPORARY TABLE temp_forwards AS
            SELECT 
                team_season_rosters.team_id, nhl_teams.triCode, team_season_rosters.season, nhl_players.position, exploded_forwards.player_id, nhl_players.firstName, nhl_players.lastName
            FROM team_season_rosters
            JOIN JSON_TABLE(team_season_rosters.forwards, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_forwards
                ON 1=1
            JOIN nhl_players ON nhl_players.playerID = exploded_forwards.player_id
            JOIN nhl_teams ON nhl_teams.id = team_season_rosters.team_id
            WHERE team_season_rosters.team_id = $team_id;
            ";
            mysqli_query($conn, $sql1);

            // Step 2: Create the temp_defensemen table
            $sql2 = "
            CREATE TEMPORARY TABLE temp_defensemen AS
            SELECT 
                team_season_rosters.team_id, nhl_teams.triCode, team_season_rosters.season, nhl_players.position, exploded_defensemen.player_id, nhl_players.firstName, nhl_players.lastName
            FROM team_season_rosters
            JOIN JSON_TABLE(team_season_rosters.defensemen, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_defensemen
                ON 1=1
            JOIN nhl_players ON nhl_players.playerID = exploded_defensemen.player_id
            JOIN nhl_teams ON nhl_teams.id = team_season_rosters.team_id
            WHERE team_season_rosters.team_id = $team_id;
            ";
            mysqli_query($conn, $sql2);

            // Step 3: Create the temp_roster table by combining temp_forwards and temp_defensemen
            $sql3 = "
            CREATE TEMPORARY TABLE temp_roster AS
            SELECT * FROM temp_forwards
            UNION ALL
            SELECT * FROM temp_defensemen;
            ";
            mysqli_query($conn, $sql3);

            // Step 4: Run the main query to fetch the results
            $sql4 = "
            SELECT 
                temp_roster.team_id, teams.triCode, temp_roster.position, temp_roster.player_id, temp_roster.firstName, temp_roster.lastName, temp_roster.season, 
                CONCAT(temp_roster.season, '-2') as seasonWithType, teams.id, teams.fullName, teams.teamLogo, teams.teamColor1, teams.teamColor2, teams.teamColor3, 
                teams.teamColor4, teams.teamColor5, stats.seasonGamesPlayed, stats.seasonGoals, stats.seasonAssists, stats.seasonPoints,  stats.seasonPlusMinus, 
                stats.seasonShots, stats.seasonShootingPct, stats.seasonAvgTOI, stats.seasonAvgShifts, stats.seasonFOWinPct, nhl_contracts.capHit
            FROM temp_roster AS temp_roster
            LEFT JOIN nhl_teams AS teams ON teams.id = temp_roster.team_id
            LEFT JOIN team_season_stats AS stats 
                ON stats.teamID = temp_roster.team_id 
                AND stats.playerID = temp_roster.player_id 
                AND CONCAT(temp_roster.season, '-2') = stats.seasonID
            LEFT JOIN nhl_contracts ON nhl_contracts.playerID = temp_roster.player_id
            ORDER BY temp_roster.season DESC, temp_roster.lastName
            ";
            $result_skaters_combined = mysqli_query($conn, $sql4);

            // Step 6: Drop temporary tables after use
            mysqli_query($conn, "DROP TEMPORARY TABLE IF EXISTS temp_forwards");
            mysqli_query($conn, "DROP TEMPORARY TABLE IF EXISTS temp_defensemen");
            mysqli_query($conn, "DROP TEMPORARY TABLE IF EXISTS temp_roster");


            // Combined query for goalies
            // Step 1: Create the temp_goalies table
            $sql1 = "
            CREATE TEMPORARY TABLE temp_goalies AS
            SELECT 
                team_season_rosters.team_id, nhl_teams.triCode, team_season_rosters.season, CAST('goalie' AS VARCHAR(10)) AS position, exploded_goalies.player_id,
                nhl_players.firstName, nhl_players.lastName
            FROM team_season_rosters
            JOIN JSON_TABLE(team_season_rosters.goalies, '$[*]' COLUMNS(player_id INT PATH '$')) AS exploded_goalies
                ON 1=1
            JOIN nhl_players ON nhl_players.playerID = exploded_goalies.player_id
            JOIN nhl_teams ON nhl_teams.id = team_season_rosters.team_id
            WHERE team_season_rosters.team_id = $team_id
            ";

            mysqli_query($conn, $sql1);


            // Step 2: Run the main query to fetch the results
            $sql4 = "
            SELECT 
                teams.triCode, temp_goalies.position, temp_goalies.player_id, temp_goalies.firstName, temp_goalies.lastName, temp_goalies.season, 
                CONCAT(temp_goalies.season, '-2') as seasonWithType, teams.id, teams.fullName, teams.teamLogo, teams.teamColor1, teams.teamColor2, teams.teamColor3, 
                teams.teamColor4, teams.teamColor5, stats.seasonGamesPlayed, stats.seasonGS, stats.seasonWins, stats.seasonLosses, stats.seasonTies, stats.seasonOTLosses, 
                stats.seasonGAA, stats.seasonSavePct, stats.seasonSA, stats.seasonSaves, stats.seasonGA, stats.seasonSO, stats.seasonTOI, nhl_contracts.capHit
            FROM temp_goalies
            LEFT JOIN nhl_teams AS teams ON teams.id = temp_goalies.team_id
            LEFT JOIN team_season_stats AS stats 
                ON stats.teamID = temp_goalies.team_id 
                AND stats.playerID = temp_goalies.player_id 
                AND CONCAT(temp_goalies.season, '-2') = stats.seasonID
            LEFT JOIN nhl_contracts ON nhl_contracts.playerID = temp_goalies.player_id
            ORDER BY temp_goalies.season DESC, temp_goalies.lastName
            ";
            $result_goalies_combined = mysqli_query($conn, $sql4);

            // Step 6: Drop temporary tables after use
            mysqli_query($conn, "DROP TEMPORARY TABLE IF EXISTS temp_goalies");

            if (!$result_skaters_combined) {
                die("Query failed: " . mysqli_error($conn));
            } elseif (mysqli_num_rows($result_skaters_combined) == 0) {
                echo "No players found for this team.";
            } else {
                // Fetch the row to get the team logo and build header
                $team = mysqli_fetch_assoc($result_skaters_combined);
                ?>
                

                    <div class="bg-gradient-to-br from-[<?php echo $teamColor1; ?>]/20 to-[<?php echo $teamColor2; ?>]/15">  
                      <div class="max-w-[90%] mx-auto"> <!-- Open div for centered container for content -->
                        <br><br>

                        <!-- Team Header - Slightly Less Intense -->
                        <div class="team-header flex justify-between items-center mb-8 p-6 rounded-lg shadow-md" 
                            style="background: linear-gradient(135deg, <?php echo $teamColor1.'DD'; ?> 0%, <?php echo $teamColor2.'DD'; ?> 100%); 
                                border: 1px solid <?php echo $teamColor2; ?>;">
                    
                            <!-- Left side: Team Name -->
                            <div class="flex flex-col">
                                <h3 class="text-xl font-medium mb-1" style="color: <?php echo $teamColor1Contrast; ?>;">Team Details</h3>
                                <h1 class="text-4xl font-bold" style="color: <?php echo $teamColor1Contrast; ?>;">
                                    <?php echo $teamName; ?>
                                </h1>
                            </div>
                    
                            <!-- Right side: Team Logo -->
                            <div class="team-logo-container p-2">
                                <?php
                                if ($teamLogo != 'false' && $teamLogo != '' && $teamLogo != 'N/A') {
                                    echo "<img src='" . htmlspecialchars($teamLogo) . "' alt='Team Logo' class='h-32 w-auto'>";
                                } else {
                                    echo "<p class='text-lg font-medium'>No Logo Available</p>"; 
                                }
                                ?>
                            </div>
                        </div>

                        <?php  
                        // mysqli_data_seek($result_skaters_combined, 0);
                        } // end else for check if final query failed
              

                                                                                        // AWARDS TABLES
             
        // Expanded query to include all four trophy types
        $sql = "SELECT * FROM season_awards 
            WHERE stanleyCupWinnerID = $team_id 
            OR presidentsTrophyWinnerID = $team_id";

        $awardsResult = mysqli_query($conn, $sql);

        // Initialize arrays to store different types of awards by season
        $stanleyCups = [];
        $presidentsTrophies = [];

        // Process the results
        if ($awardsResult && mysqli_num_rows($awardsResult) > 0) {
        while ($award = mysqli_fetch_assoc($awardsResult)) {
            $season = $award['seasonID']; // Assuming you have a 'season' column
            
            // Sort the awards into their respective arrays
            if ($award['stanleyCupWinnerID'] == $team_id) {
                $stanleyCups[] = $season;
            }
            if ($award['presidentsTrophyWinnerID'] == $team_id) {
                $presidentsTrophies[] = $season;
            }
        }
        }

        // Helper function to format season IDs like "20222023" to "2022-23"
        function formatSeason($seasonId) {
        if (strlen($seasonId) == 8) {
        $year1 = substr($seasonId, 0, 4);
        $year2 = substr($seasonId, 4, 4);
        return $year1 . "-" . substr($year2, 2, 2);
        }
        return $seasonId;
        }
        ?>

        <?php 
        // Query for just Stanley Cup and Presidents' Trophy
        $sql = "SELECT * FROM season_awards 
            WHERE stanleyCupWinnerID = $team_id 
            OR presidentsTrophyWinnerID = $team_id";

        $awardsResult = mysqli_query($conn, $sql);

        // Initialize arrays to store different types of awards by season
        $stanleyCups = [];
        $presidentsTrophies = [];

        // Process the results
        if ($awardsResult && mysqli_num_rows($awardsResult) > 0) {
        while ($award = mysqli_fetch_assoc($awardsResult)) {
            // Check if season column exists, otherwise use year or seasonId
            $season = $award['seasonID'];
            
            // Sort the awards into their respective arrays
            if ($award['stanleyCupWinnerID'] == $team_id) {
                $stanleyCups[] = $season;
            }
            if ($award['presidentsTrophyWinnerID'] == $team_id) {
                $presidentsTrophies[] = $season;
            }
        }
        }
        ?>

        <!-- Sleek Awards Display UI - Subtler Styling -->
        <div class="my-8 p-6 rounded-lg shadow-sm">
            <h2 class="text-2xl font-bold mb-4 text-white">Team Achievements</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Stanley Cup -->
                <div class="rounded-lg p-5 border shadow-sm" style="background: linear-gradient(60deg, <?php echo $teamColor1.'15'; ?> 0%, <?php echo $teamColor2.'20'; ?> 100%); border-color: <?php echo $teamColor3.'50'; ?>">
                    <div class="flex items-center mb-3">
                        <img src="../resources/images/stanley_cup.png" alt="Stanley Cup" class="h-14 mr-3 object-contain">
                        <h3 class="text-lg font-semibold text-white">Stanley Cup</h3>
                    </div>

                    <?php if (count($stanleyCups) > 0): ?>
                        <div class="award-seasons flex flex-wrap gap-2">
                            <?php foreach ($stanleyCups as $season): ?>
                                <span class="px-3 py-1 rounded-full text-xs font-medium" style='background-color: <?php echo $teamColor2.'60'; ?>; color: <?php echo $teamColor2Contrast; ?>;'>
                                    <?php echo formatSeason($season); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <p class="mt-3 text-sm text-white">
                            <?php echo count($stanleyCups); ?> time<?php echo count($stanleyCups) > 1 ? 's' : ''; ?> champion
                        </p>
                    <?php else: ?>
                        <p class="text-sm text-white">No Stanley Cup championships</p>
                    <?php endif; ?>
                </div>

                <!-- Presidents' Trophy -->
                <div class="rounded-lg p-5 border shadow-sm" style="background: linear-gradient(60deg, <?php echo $teamColor1.'15'; ?> 0%, <?php echo $teamColor2.'20'; ?> 100%); border-color: <?php echo $teamColor3.'50'; ?>">
                    <div class="flex items-center mb-3">
                        <img src="../resources/images/prestrophy.png" alt="Presidents' Trophy" class="h-14 mr-3 object-contain">
                        <h3 class="text-lg font-semibold text-white">Presidents' Trophy</h3>
                    </div>

                    <?php if (count($presidentsTrophies) > 0): ?>
                        <div class="award-seasons flex flex-wrap gap-2">
                            <?php foreach ($presidentsTrophies as $season): ?>
                                <span class="px-3 py-1 rounded-full text-xs font-medium" style='background-color: <?php echo $teamColor2.'60'; ?>; color: <?php echo $teamColor2Contrast; ?>;'>
                                    <?php echo formatSeason($season); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <p class="mt-3 text-sm text-white">
                            <?php echo count($presidentsTrophies); ?> regular season title<?php echo count($presidentsTrophies) > 1 ? 's' : ''; ?>
                        </p>
                    <?php else: ?>
                        <p class="text-sm text-white">No Presidents' Trophy wins</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Subtle Divider -->
        <div class="my-6 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
        <br>

<!-- Season Statistics Section -->
<div class="mb-8 flex justify-between">
    <div>
    <h2 class="text-2xl font-bold mb-2 text-white">Season Statistics</h2>
    <p class="text-nhl-muted mb-4">Select a season to view detailed stats</p>
                    </div>
    
    <!-- Season Selector -->
    <div class="max-w-xs mb-8">
        <div class="relative">
            <?php
            // Get unique seasons for the dropdown
            $seasons = [];
            // Get seasons from skaters
            mysqli_data_seek($result_skaters_combined, 0);
            while ($row = mysqli_fetch_assoc($result_skaters_combined)) {
                $seasonID = $row['season'];
                $seasonWithType = $row['seasonWithType']; 
                if (!in_array($seasonWithType, $seasons)) {
                    $seasons[] = $seasonWithType;
                }
            }
            // Get seasons from goalies
            mysqli_data_seek($result_goalies_combined, 0);
            while ($row = mysqli_fetch_assoc($result_goalies_combined)) {
                $seasonWithType = $row['seasonWithType']; 
                if (!in_array($seasonWithType, $seasons)) {
                    $seasons[] = $seasonWithType;
                }
            }
            rsort($seasons);
            ?>
            
            <select id="seasonDropdown" 
                    class="block w-full rounded-lg border-0 py-3 pl-4 pr-10 bg-[rgba(255, 255, 255, 0.8)] text-gray-800 shadow-sm text-lg font-medium cursor-pointer min-w-[125px] focus:outline-none focus:ring-2 focus:ring-<?php echo $teamColor1; ?> focus:ring-opacity-50"
                    style="border: 2px solid <?php echo $teamColor2.'70'; ?>; box-shadow: 0 1px 3px rgba(0,0,0,0.1);"
                    onchange="updateSeason()">
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
            
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3">
                <!-- SVG Chevron/Arrow icon using team color -->
                <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" 
                     style="color: <?php echo $teamColor1; ?>;">
                    <path d="M7 7l3 3 3-3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

                    


<?php
 // Main container
echo "<div class='season-stats-section mx-auto'>"; // Centered container for content

?>
<div class="season-tabs-container">
    <div class="season-tab">
        <?php
        $tabs = [
            'tab1' => 'Overview',
            'tab2' => 'Home/Road Stats',
            'tab3' => 'Skaters',
            'tab4' => 'Goalies',
            'tab5' => 'Draft Picks',
            'tab6' => 'Current Prospects'
        ];
        
        foreach ($tabs as $tabId => $tabLabel) {
            $activeClass = ($tabId === 'tab1') ? 'active' : '';
            echo "<button class='season-tab-button $activeClass' data-tab='$tabId'>";
            echo $tabLabel;
            echo "</button>";
        }
        ?>
    </div>
</div>

<?php
// Tab content container
echo "<div class='season-tab-content mx-auto'>";
?>


                <!-- GET END OF SEASON RESULTS FOR THE SELECTED SEASON -->
<div class="w-full overflow-x-auto px-0">
  <div class='season-tab-pane active' id='tab1'>
    <table class='team-stats-table' style='border: 2px solid <?php echo $teamColor2; ?>;'>
                    <colgroup>
                    <col class='overall-team-stats-season'>
                    <col class='overall-team-stats-gp'>
                    <col class='overall-team-stats-w'>
                    <col class='overall-team-stats-l'>
                    <col class='overall-team-stats-otl'>
                    <col class='overall-team-stats-pts'>
                    <col class='overall-team-stats-t'>
                    <col class='overall-team-stats-reg-wins'>
                    <col class='overall-team-stats-ot-wins'>
                    <col class='overall-team-stats-so-wins'>
                    <col class='overall-team-stats-fo-win-pct'>
                    <col class='overall-team-stats-sa-gp'>
                    <col class='overall-team-stats-sf-gp'>
                    <col class='overall-team-stats-gf'>
                    <col class='overall-team-stats-gf-gp'>
                    <col class='overall-team-stats-ga'>
                    <col class='overall-team-stats-ga-gp'>
                    <col class='overall-team-stats-pk-pct'>
                    <col class='overall-team-stats-pt-pct'>
                    <col class='overall-team-stats-pp-net-pct'>
                    <col class='overall-team-stats-pp-pct'>
                    </colgroup>
                <thead>
                    <tr style="background: linear-gradient(90deg, <?php echo $teamColor1.'50'; ?> 0%, <?php echo $teamColor2.'50'; ?> 100%);">
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Season</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GP</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>W</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>L</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>OTL</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Pts</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>T</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Reg W</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>OT W</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>SO W</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>FOW %</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>SA / G</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>SF / G</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GF</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GF / G</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GA</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GA / G</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>PK %</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Pt %</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>PP Net %</th>
                        <th class='border' style='border-color: <?php echo $teamColor2; ?>'>PP %</th>
                    </tr>
                    </thead>
                    <tbody id='overallStatsTable'>

                <?php
                while ($row = mysqli_fetch_assoc($overallStatsResult)) {
                
                $overallSeason = $row['season_id'];
                $overallGP = $row['gp'];
                $overallW = $row['win'];
                $overallL = $row['loss'];
                $overallOTL = $row['otLoss'];
                $overallPts = $row['pts'];
                if ($row['ties'] == null) {
                    $overallTies = 0;
                } else {
                    $overallTies = $row['ties'];
                }
                $overallRegWins = $row['winsInRegulation'];
                $overallRegOTWins = $row['regulationAndOtWins'];
                $overallSOWins = $row['winsInShootout'];
                $overallFOWinPct = $row['faceoffWinPctg'];
                $overallShotsAgainstPerGame = $row['shotAgainstPerGame'];
                $overallShotsForPer = $row['shotForPerGame'];
                $overallGF = $row['goalFor'];
                $overallGFper = $row['goalForPerGame'];
                $overallGA = $row['goalAgainst'];
                $overallGAper = $row['goalAgainstPerGame'];
                $overallPKPct = $row['penaltyKillPctg'];
                $overallPtPct = $row['pointPct'];
                $overallPPNetPct = $row['powerPlayNetPctg'];
                $overallPPPct = $row['powerPlayPctg'];

                # derived variables
                $overallOTWins = $overallRegOTWins - $overallRegWins; // Overtime Wins = Reg OT Wins - Reg Wins
                $overall_totalWins = $overallW + $overallRegWins + $overallSOWins;
                # Corsi For = (Corsi For) / (Corsi For + Corsi Against) * 100 = Total Shot Attempts (for) / Total Shot Attempts (for + against) * 100
                # = 
                // $corsi_for
                

                echo "<tr data-season='$overallSeason'>";
                echo "<td class='border' style='border-color: $teamColor2'>" . substr($overallSeason, 0, 4) . "-" . substr($overallSeason, 4, 4) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallGP . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallW . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallL . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallOTL . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallPts . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallTies . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallRegWins . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallOTWins . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallSOWins . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float)$overallFOWinPct,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float)$overallShotsAgainstPerGame,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float)$overallShotsForPer,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallGF . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float)$overallGFper,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $overallGA . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float)$overallGAper,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float)$overallPKPct,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float)$overallPtPct,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float)$overallPPNetPct,2) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float)$overallPPPct,2) . "</td>";
                echo "</tr>";

            }

            ?>
                </tbody>
                </table>
                </div>

                <!-- TAB 2: HOME/ROAD SPLITS -->
  <div class='season-tab-pane w-full flex justify-center' id='tab2'>
    <table class='team-stats-table w-full md:w-[98%]' style='border: 2px solid <?php echo $teamColor2; ?>;'>
                <thead>
                    <tr style="background: linear-gradient(90deg, <?php echo $teamColor1.'50'; ?> 0%, <?php echo $teamColor2.'50'; ?> 100%);">
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">Season</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>; 
                            color: <?php echo $teamColor1Contrast; ?>">Home/Away</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">GP</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">Goal Diff.</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">GA</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">GF</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">L</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">OTL</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">W</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">Pts</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">Reg + OT W</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">Reg W</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">T</th>
                            <th class="py-3 px-4 text-left text-sm font-medium border-b" style="border-color: <?php echo $teamColor2.'30'; ?>;
                            color: <?php echo $teamColor1Contrast; ?>">W</th>

                        </tr>
                </thead>
                <tbody id='standingsTable'>
                 <?php
                 $statsSQL = "SELECT * FROM nhl_EOY_team_stats WHERE team_id = $team_id"; 
                 $stats = mysqli_query($conn, $statsSQL);

                 
                while ($row = mysqli_fetch_assoc($stats)) {
                    // Team identification and general info
                    // $clinchIndicator = $row['clinchIndicator'];
                    // $conferenceAbbrev = $row['conferenceAbbrev'];
                    // $conferenceHomeSequence = $row['conferenceHomeSequence'];
                    // $conferenceL10Sequence = $row['conferenceL10Sequence'];
                    // $conferenceName = $row['conferenceName'];
                    // $conferenceRoadSequence = $row['conferenceRoadSequence'];
                    // $conferenceSequence = $row['conferenceSequence'];
                    // $date = $row['date'];
                    // $divisionAbbrev = $row['divisionAbbrev'];
                    // $divisionHomeSequence = $row['divisionHomeSequence'];
                    // $divisionL10Sequence = $row['divisionL10Sequence'];
                    // $divisionName = $row['divisionName'];
                    // $divisionRoadSequence = $row['divisionRoadSequence'];
                    // $divisionSequence = $row['divisionSequence'];
                    // $gameTypeId = $row['gameTypeId'];
                    // $gp = $row['gp'];
                    // $teamID = $row['teamID'];
                    // $teamName = $row['teamName'];
                    $seasonID = $row['season_id'];

                    // Overall statistics
                    $goalDifferential = $row['goalDifferential'];
                    $goalDifferentialPctg = $row['goalDifferentialPctg'];
                    $goalAgainst = $row['goalAgainst'];
                    $goalFor = $row['goalFor'];
                    $goalsForPctg = $row['goalsForPctg'];
                    $pointPctg = $row['pointPctg'];
                    $points = $row['points'];
                    $regulationPlusOtWinPctg = $row['regulationPlusOtWinPctg'];
                    // $waiversSequence = $row['waiversSequence'];
                    // $wildcardSequence = $row['wildcardSequence'];
                    $winPctg = $row['winPctg'];
                    // $streakCode = $row['streakCode'];
                    // $streakCount = $row['streakCount'];

                    // Home statistics
                    $homeGamesPlayed = $row['homeGamesPlayed'];
                    $homeGoalDifferential = $row['homeGoalDifferential'];
                    $homeGoalsAgainst = $row['homeGoalsAgainst'];
                    $homeGoalsFor = $row['homeGoalsFor'];
                    $homeLosses = $row['homeLosses'];
                    $homeOtLosses = $row['homeOtLosses'];
                    $homePoints = $row['homePoints'];
                    $homeRegulationPlusOtWins = $row['homeRegulationPlusOtWins'];
                    $homeRegulationWins = $row['homeRegulationWins'];
                    $homeTies = $row['homeTies'];
                    $homeWins = $row['homeWins'];

                    // Road statistics
                    $roadGamesPlayed = $row['roadGamesPlayed'];
                    $roadGoalDifferential = $row['roadGoalDifferential'];
                    $roadGoalsAgainst = $row['roadGoalsAgainst'];
                    $roadGoalsFor = $row['roadGoalsFor'];
                    $roadLosses = $row['roadLosses'];
                    $roadOtLosses = $row['roadOtLosses'];
                    $roadPoints = $row['roadPoints'];
                    $roadRegulationPlusOtWins = $row['roadRegulationPlusOtWins'];
                    $roadRegulationWins = $row['roadRegulationWins'];
                    $roadTies = $row['roadTies'];
                    $roadWins = $row['roadWins'];

                    // if ($clinchIndicator == 'x') {
                    //     $clinchStatus = 'Made Playoffs';
                    // } else if ($clinchIndicator == 'y') {
                    //     $clinchStatus = 'Won Division (' . $divisionName . ')';
                    // } else if ($clinchIndicator == 'z') {
                    //     $clinchStatus = 'Won Conference (' . $conferenceName . ')';
                    // } else if ($clinchIndicator == 'p') {
                    //     $clinchStatus = 'Won Presidents Trophy';
                    // } else if ($clinchIndicator == 'e') {
                    //     $clinchStatus = 'Missed Playoffs';
                    // } else {
                    //     $clinchStatus = 'Unknown';
                    // }

                ?>
                        <tr data-season="<?php echo $seasonID . '-2'; ?>">
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo substr($seasonID, 0, 4) . '-' . substr($seasonID, 4, 4) ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'>Home</td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homeGamesPlayed ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homeGoalDifferential ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homeGoalsAgainst ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homeGoalsFor ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homeLosses ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homeOtLosses ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homeWins ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homePoints ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homeRegulationPlusOtWins ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homeRegulationWins ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homeTies ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $homeWins ?></td>
                        </tr>

                        <tr data-season="<?php echo $seasonID . '-2'; ?>">
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo substr($seasonID, 0, 4) . '-' . substr($seasonID, 4, 4) ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'>Away</td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadGamesPlayed ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadGoalDifferential ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadGoalsAgainst ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadGoalsFor ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadLosses ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadOtLosses ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadWins ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadPoints ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadRegulationPlusOtWins ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadRegulationWins ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadTies ?></td>
                        <td class='border' style='border-color: <?php echo $teamColor2; ?>'><?php echo $roadWins ?></td>
                        </tr>

                        <?php
                }
                ?>
                </tbody>
                </table>
                </div>
            

                <!-- TAB 3: SKATERS COMBINED TABLE -->
  <div class='season-tab-pane w-full flex justify-center' id='tab3'>
    <table class='team-stats-table w-full md:w-[98%]' style='border: 2px solid <?php echo $teamColor2; ?>;'>
                    <colgroup>
                    <col class='skaters-combined-season'>
                    <col class='skaters-combined-name'>
                    <col class='skaters-combined-position'>
                    <col class='skaters-combined-cap-hit'>
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
                    <tr data-season='$seasonWithType' style="background: linear-gradient(90deg, <?php echo $teamColor1.'50'; ?> 0%, <?php echo $teamColor2.'50'; ?> 100%);">
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Season</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Name</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Pos.</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Cap Hit</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GP</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>G</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>A</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>P</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>+/-</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Shots</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Shot %</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Avg TOI</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Avg Shifts</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>FO %</th>
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

                                // Format cap hit
                                $capHit = $row['capHit'];
                                if ($capHit == null || $capHit == '') {
                                    $capHit = "-"; // Show dash if cap hit is zero or negative

                                } else {
                                    $capHit = substr($capHit, 1); // Remove first character (e.g., $)
                                    $capHit = floatval(str_replace(',', '', $capHit)); // Remove commas and convert to float

                                
                                    $capHit = number_format($capHit / 1000000, 2); // Convert to millions and format
                                }

                                
                                // Extract season years for display
                                $seasonYear1 = substr($seasonID, 0, 4);
                                $seasonYear2 = substr($seasonID, 4, 4);
                                
                                echo "<tr data-season='$seasonWithType'>"; // For filtering by season with type
                                echo "<td class='border' style='border-color: $teamColor2'>" . $seasonYear1 . "-" . $seasonYear2 . "</td>";  // Season display
                                echo "<td class='border' style='border-color: $teamColor2'><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $playerID . "'>" . $firstName . " " . $lastName . "</a></td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . $positionDisplay . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . $row['capHit'] . "</td>"; // Salary display
                                
                                // Display stats if available, otherwise show dash
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonGamesPlayed'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonGoals'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonAssists'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonPoints'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonPlusMinus'] !== null && $row['seasonPlusMinus'] !== '' ? $row['seasonPlusMinus'] : "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonShots'] ?? "-") . "</td>";
                                
                                // Handle percentages and formatting
                                if (isset($row['seasonShootingPct'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float) $row['seasonShootingPct']*100, 1) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                // Format time on ice if available
                                if (isset($row['seasonAvgTOI'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . gmdate("i:s", (int) $row['seasonAvgTOI']) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                // Format shifts
                                if (isset($row['seasonAvgShifts'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float) $row['seasonAvgShifts'], 1) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                // Format faceoff percentage
                                if (isset($row['seasonFOWinPct'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float) $row['seasonFOWinPct']*100, 1) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    </div>
                        

                <!-- TAB 4: GOALIES COMBINED TABLE -->

                
  <div class='season-tab-pane w-full flex justify-center' id='tab4'>
    <table class='team-stats-table w-full md:w-[98%]' style='border: 2px solid <?php echo $teamColor2; ?>;'>
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
                    <tr style="background: linear-gradient(90deg, <?php echo $teamColor1.'50'; ?> 0%, <?php echo $teamColor2.'50'; ?> 100%);">
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Season</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Name</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GP</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GS</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>W</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>L</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>T</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>OTL</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GAA</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Sv. %</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>SA</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Saves</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>GA</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>SO</th>
                                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>TOI</th>
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
                                echo "<td class='border' style='border-color: $teamColor2'><a style='color:rgb(15, 63, 152)' href='season_overview.php?season_id=" . $seasonYear1 . $seasonYear2 . "'>" . $seasonYear1 . "-" . $seasonYear2 . "</a></td>";  // Season display
                                echo "<td class='border' style='border-color: $teamColor2'><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $playerID . "'>" . $firstName . " " . $lastName . "</a></td>";
                                
                                // Display stats if available, otherwise show dash
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonGamesPlayed'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonGS'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonWins'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonLosses'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonTies'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonOTLosses'] ?? "-") . "</td>";
                                
                                // Format GAA
                                if (isset($row['seasonGAA'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float) $row['seasonGAA'], 2) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                // Format save percentage
                                if (isset($row['seasonSavePct'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . number_format((float) $row['seasonSavePct'], 3) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonSA'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonSaves'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonGA'] ?? "-") . "</td>";
                                echo "<td class='border' style='border-color: $teamColor2'>" . ($row['seasonSO'] ?? "-") . "</td>";
                                
                                // Format TOI
                                if (isset($row['seasonTOI'])) {
                                    echo "<td class='border' style='border-color: $teamColor2'>" . gmdate("i:s", (int) $row['seasonTOI']) . "</td>";
                                } else {
                                    echo "<td class='border' style='border-color: $teamColor2'>-</td>";
                                }
                                
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

        
            <?php
            echo "<div>";
                $draftSQL = "SELECT * FROM draft_history WHERE teamID = $team_id";

                $draftResult = mysqli_query($conn, $draftSQL);
                // echo "<h3 class='section-title text-2xl font-bold text-center mb-6 text-white'>Draft Picks</h3><br>";
  echo "<div class='season-tab-pane w-full flex justify-center' id='tab5'>";
    echo "<table class='team-stats-table w-full md:w-[98%]' style='border: 2px solid $teamColor2;'> ";
                echo "<colgroup>";
                    echo "<col class='draft-year'>";
                    echo "<col class='draft-round'>";
                    echo "<col class='draft-pick-in-round'>";
                    echo "<col class='draft-overall-pick'>";
                    echo "<col class='draft-player-name'>";
                    echo "<col class='draft-player-position'>";
                    echo "<col class='draft-player-country'>";
                    echo "<col class='draft-player-id'>";
                echo "</colgroup>";
                ?>
                <thead>
                    <tr style="background: linear-gradient(90deg, <?php echo $teamColor1.'50'; ?> 0%, <?php echo $teamColor2.'50'; ?> 100%);">
                <?php
                    echo "<th class='border' style='border-color: $teamColor2'>Year</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Round</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Pick In Round</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Overall Pick</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Player Name</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Position</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Country</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>ID</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody id='draftHistoryTable'>";

                while ($row = mysqli_fetch_assoc($draftResult)) {
                    $draftPlayerID = $row['playerId'];
                    $draftYear = $row['draftYear'];
                    $draftRound = $row['round'];
                    $draftPickInRound = $row['pickInRound'];
                    $draftPickOvr = $row['overallPick'];
                    $draftPlayerFirstName = $row['firstName'];
                    $draftPlayerLastName = $row['lastName'];
                    $draftPlayerName = $draftPlayerFirstName . " " . $draftPlayerLastName;
                    $draftPlayerPosition = $row['position'];
                    $draftPlayerCountry = $row['country'];

                    echo "<tr data-season='$draftYear'>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftYear . "</td>";
                        // echo "<td class='border' style='border-color: $teamColor2'><a style='color:rgb(15, 63, 152)' href='draft_history.php?season_id=" . $seasonYear1 . $seasonYear2 . "'>" . $seasonYear1 . "-" . $seasonYear2 . "</a></td>";  // Season display
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftRound . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftPickInRound . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftPickOvr . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'> ". $draftPlayerName . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . $draftPlayerPosition . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . getFlagSVG($draftPlayerCountry) . "</td>";
                        // echo "<td class='border' style='border-color: $teamColor2'>" . $draftPlayerID . "</td>";
                        echo "<td class='border' style='border-color: $teamColor2'><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $draftPlayerID . "'>" . $draftPlayerID . "</a></td>";
                    
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
            echo "</div>";
        ?>
        <!-- <div class="divider" style="background: linear-gradient(to right, rgba(<?php echo hexdec(substr($teamColor1, 1, 2)); ?>, <?php echo hexdec(substr($teamColor1, 3, 2)); ?>, <?php echo hexdec(substr($teamColor1, 5, 2)); ?>, 0.1), 
                                                                rgba(<?php echo hexdec(substr($teamColor1, 1, 2)); ?>, <?php echo hexdec(substr($teamColor1, 3, 2)); ?>, <?php echo hexdec(substr($teamColor1, 5, 2)); ?>, 0.6), 
                                                                rgba(<?php echo hexdec(substr($teamColor1, 1, 2)); ?>, <?php echo hexdec(substr($teamColor1, 3, 2)); ?>, <?php echo hexdec(substr($teamColor1, 5, 2)); ?>, 0.1));"></div> -->
        <?php


            ### TAB 6: PROSPECTS ###

    
                $prospectSQL = "SELECT team_prospects.*, nhl_players.sweaterNumber, nhl_players.firstName, nhl_players.lastName, nhl_players.birthCountry,
                nhl_players.position, nhl_players.heightInInches, nhl_players.heightInCentimeters, nhl_players.weightInPounds, nhl_players.weightInKilograms,
                nhl_players.birthDate
                FROM team_prospects LEFT JOIN nhl_players ON team_prospects.prospect_id=nhl_players.playerId WHERE team_id = $team_id";
                $prospectResult = mysqli_query($conn, $prospectSQL);
                // echo "<h3 class='section-title text-2xl font-bold text-center mb-6 text-white'>Current Prospects</h3><br>";
  echo "<div class='season-tab-pane w-full flex justify-center' id='tab6'>";
    echo "<table class='team-stats-table w-full md:w-[98%]' style='border: 2px solid $teamColor2;'> ";
                echo "<colgroup>";
                    echo "<col class='prospect-id'>";
                    echo "<col class='prospect-name'>";
                    echo "<col class='prospect-position'>";
                    echo "<col class='prospect-age'>";
                    echo "<col class='prospect-height'>";
                    echo "<col class='prospect-weight'>";
                    echo "<col class='prospect-country'>";
                echo "</colgroup>";
                ?>
                <thead>
                    <tr style="background: linear-gradient(90deg, <?php echo $teamColor1.'50'; ?> 0%, <?php echo $teamColor2.'50'; ?> 100%);">
                <?php
                    echo "<th class='border' style='border-color: $teamColor2'>Prospect ID</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Name</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Number</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Position</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Age</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Height</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Weight</th>";
                    echo "<th class='border' style='border-color: $teamColor2'>Country</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody id='prospectTable'>";
                while ($row = mysqli_fetch_assoc($prospectResult)) {
                    // print_r($row);
                    $prospectID = $row['prospect_id'];
                    if ($prospectID == null || $prospectID == '') {
                        $prospectID = "-"; // Show dash if ID is not available
                    }
                    $firstName = $row['firstName'];
                    // echo 'first' . $firstName;
                    $lastName = $row['lastName'];
                    // echo 'last'.  $lastName;
                    $number = $row['sweaterNumber'];
                    if ($number == null || $number == '') {
                        $number = "-"; // Show dash if number is not available
                    }
                    $position = $row['position'];
                    $prospectBirthDate = $row['birthDate'];
                    $prospectAge = date_diff(date_create($prospectBirthDate), date_create('now'))->y;
                    $prospectHeightIn = $row['heightInInches'];
                    $prospectHeightCm = $row['heightInCentimeters'];
                    $prospectHeight = $prospectHeightIn . " in / " . $prospectHeightCm . " cm";
                    $prospectWeightLbs = $row['weightInPounds'];
                    $prospectWeightKg = $row['weightInKilograms'];
                    $prospectWeight = $prospectWeightLbs . " lbs / " . $prospectWeightKg . " kg";
                    $prospectCountry = $row['birthCountry'];

                    echo "<tr>";
                        // echo "<td class='border' style='border-color: $teamColor2'>$prospectID</td>";
                        echo "<td class='border' style='border-color: $teamColor2'><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $prospectID . "'>" . $prospectID . "</a></td>";
                        echo "<td class='border' style='border-color: $teamColor2'><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $prospectID . "'>" . $firstName . " " . $lastName . "</a></td>";
                        echo "<td class='border' style='border-color: $teamColor2'>$number</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>$position</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>$prospectAge</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>$prospectHeight</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>$prospectWeight</td>";
                        echo "<td class='border' style='border-color: $teamColor2'>" . getFlagSVG($prospectCountry) . "</td>";

                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
            echo "</div>"; // END TAB 6

            
                

            echo "</div>"; // END TABS CONTAINER
            echo "</div>"; // END TABS
            echo "</div>"; // END FULL PAGE




        } else {
            echo "<div class='container'><div class='alert alert-warning'>No team ID provided. Please select a team.</div></div>";
        }
        // Close database connection
        mysqli_close($conn);
            ?>
            <div>
            <br>
        <div class="my-6 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>


                <div class="container mx-auto px-4">
                    <?php include 'team_links_footer.php'; ?>
                </div>      

        <div class="my-6 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>

            </div>
            
    </div>

            <?php include 'footer.php'; ?>

            <script>
                // Make sure the DOM is fully loaded before running the script
                document.addEventListener("DOMContentLoaded", function() {
                    function updateSeason() {
                        // Get the selected season from the dropdown
                        var selectedSeason = document.getElementById("seasonDropdown").value;

                        // Extract the season years from the selected value
                        var seasonYear1 = selectedSeason.substr(0, 4);
                        var seasonYear2 = selectedSeason.substr(4, 4);

                        // Update the <h3> element with the selected season
                        document.getElementById("seasonTitle").textContent = "Skaters " + seasonYear1 + "-" + seasonYear2;
                    }

                    // Trigger the updateSeason function on page load to match the initial dropdown value
                    updateSeason();

                    // Add event listener for the dropdown change
                    document.getElementById("seasonDropdown").addEventListener("change", updateSeason);
                });

                    document.addEventListener('DOMContentLoaded', function () {
                    const dropdown = document.getElementById('seasonDropdown');
                    const skaterRows = document.querySelectorAll('#skaterStatsTable tr');
                    const goalieRows = document.querySelectorAll('#goalieStatsTable tr');
                    const rosterRows = document.querySelectorAll('#seasonRosterTable tr');
                    const overallRows = document.querySelectorAll('#overallStatsTable tr');
                    const draftRows = document.querySelectorAll('#draftHistoryTable tr');
                    const standingsRows = document.querySelectorAll('#standingsTable tr');
                    const statsRows = document.querySelectorAll('#statsTable tr');

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
                                alert(row.dataset.season);
                            } else {
                                row.style.display = 'none'; // Hide row
                                
                            }
                        });

                        // Filter overall rows
                        overallRows.forEach(row => {
                            if (row.dataset.season === seasonID || row.dataset.season === baseSeasonID) {
                            row.style.display = ''; // Show row
                            } else {
                                row.style.display = 'none'; // Hide row
                            }
                        });

                        // Filter draft rows
                        draftRows.forEach(row => {
                            const draftYear = row.dataset.season; // Use draftYear directly
                            const selectedYear = seasonID.substring(0, 4); // Extract the first year of the season
                            if (draftYear === selectedYear) {
                                row.style.display = ''; // Show row
                            } else {
                                row.style.display = 'none'; // Hide row
                            }
                        });

                        // Filter standings rows
                        standingsRows.forEach(row => {
                            if (row.dataset.season === seasonID) {
                                row.style.display = ''; // Show row
                            } else {
                                row.style.display = 'none'; // Hide row
                            }
                        });

                        // Filter stats rows
                        statsRows.forEach(row => {
                            if (row.dataset.season === seasonID) {
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

                        // Handle season selection change
        document.getElementById('seasonDropdown').addEventListener('change', function () {
        const selectedSeason = this.value;
        const seasonYear1 = selectedSeason.substring(0, 4); // Extract first 4 digits
        const seasonYear2 = selectedSeason.substring(4, 8); // Extract last 4 digits

        // Update the Skaters table header
        const skatersHeader = document.getElementById('skatersHeader');
        skatersHeader.textContent = `Skaters ${seasonYear1}-${seasonYear2}`;

        // Optionally, you can add logic here to load data dynamically or filter table rows
        // based on the selected season.
        });
                });
                            </script>


    <script>
      document.addEventListener('DOMContentLoaded', function() {
    // Get all tab buttons
    const tabButtons = document.querySelectorAll('.season-tab-button');
    
    // Add click event to each tab button
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the tab ID from data-tab attribute
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and tab panes
            document.querySelectorAll('.season-tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.querySelectorAll('.season-tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });
            
            // Add active class to clicked button and corresponding tab pane
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
});
    </script>
    </body>
</html>