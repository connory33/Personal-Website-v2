
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

/* Hide inactive tab panes - make this more specific */
.season-tab-pane {
  display: none !important;
}

/* Show only active tab pane */
.season-tab-pane.active {
  display: block !important;
}

/* For tabs that need flexbox layout when active */
.season-tab-pane.active.flex {
  display: flex !important;
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
             
// Single comprehensive query for all awards
$sql = "SELECT * FROM season_awards
    WHERE stanleyCupWinnerID = $team_id 
    OR presidentsTrophyWinnerID = $team_id
    OR jackAdamsWinnerTeam = $team_id
    OR jenningsTeamID = $team_id";

$awardsResult = mysqli_query($conn, $sql);

// Initialize arrays to store different types of awards by season
$stanleyCups = [];
$presidentsTrophies = [];
$jackAdamsSeasons = [];
$jackAdamsCoaches = [];
$jenningsSeasons = [];
$jenningsPlayerIDs = [];

// Process the results in one pass
if ($awardsResult && mysqli_num_rows($awardsResult) > 0) {
    while ($award = mysqli_fetch_assoc($awardsResult)) {
        $season = $award['seasonID'];
        
        // Sort the awards into their respective arrays
        if ($award['stanleyCupWinnerID'] == $team_id) {
            $stanleyCups[] = $season;
        }
        if ($award['presidentsTrophyWinnerID'] == $team_id) {
            $presidentsTrophies[] = $season;
        }
        if ($award['jackAdamsWinnerTeam'] == $team_id) {
            $jackAdamsSeasons[] = $season;
            $jackAdamsCoaches[] = $award['jackAdamsWinnerCoach'];
        }
        if ($award['jenningsTeamID'] == $team_id) {
            $jenningsSeasons[] = $season;
            $jenningsPlayerIDs[] = $award['jenningsWinnerID'];
        }
    }
}

// Function to get player names for Jennings winners
function getJenningsPlayerNames($playerIDs, $conn) {
    if (empty($playerIDs)) return [];
    
    $playerNames = [];
    
    foreach ($playerIDs as $idString) {
        // Split by semicolon to handle multiple players
        $ids = explode(';', $idString);
        $seasonPlayers = [];
        
        foreach ($ids as $playerId) {
            $playerId = trim($playerId); // Remove any whitespace
            if (!empty($playerId)) {
                // Query to get player name
                $playerQuery = "SELECT firstName, lastName FROM nhl_players WHERE playerId = '$playerId'";
                $playerResult = mysqli_query($conn, $playerQuery);
                
                if ($playerResult && mysqli_num_rows($playerResult) > 0) {
                    $player = mysqli_fetch_assoc($playerResult);
                    $seasonPlayers[] = $player['firstName'] . ' ' . $player['lastName'];
                } else {
                    $seasonPlayers[] = "Unknown Player (ID: $playerId)"; // Fallback
                }
            }
        }
        
        $playerNames[] = $seasonPlayers;
    }
    
    return $playerNames;
}

// Get the actual player names for Jennings winners
$jenningsPlayerNames = getJenningsPlayerNames($jenningsPlayerIDs, $conn);

// Helper function to format season IDs like "20222023" to "2022-23"
function formatSeason($seasonId) {
    if (strlen($seasonId) == 8) {
        $year1 = substr($seasonId, 0, 4);
        $year2 = substr($seasonId, 4, 4);
        return $year1 . "-" . substr($year2, 2, 2);
    }
    return $seasonId;
}

// Sort awards by season (most recent first)
rsort($stanleyCups);
rsort($presidentsTrophies);
rsort($jackAdamsSeasons);
rsort($jenningsSeasons);
?>

<?php
// Single comprehensive query to get ALL individual award winners that were on this team when they won
$individualAwardsSql = "
    -- Hart Trophy (Most Valuable Player - Any skater)
    SELECT 
        sa.seasonID,
        sa.hartTrophyWinnerID as playerID,
        np.firstName,
        np.lastName,
        'Hart Trophy' as awardName,
        'Most Valuable Player' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.hartTrophyWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.hartTrophyWinnerID IS NOT NULL 
    AND sa.hartTrophyWinnerID != ''
    AND (
        FIND_IN_SET(sa.hartTrophyWinnerID, REPLACE(REPLACE(REPLACE(tsr.forwards, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.hartTrophyWinnerID, REPLACE(REPLACE(REPLACE(tsr.defensemen, '[', ''), ']', ''), ' ', '')) > 0
    )

    UNION ALL

    -- Vezina Trophy (Best Goaltender)
    SELECT 
        sa.seasonID,
        sa.vezinaTrophyWinnerID as playerID,
        np.firstName,
        np.lastName,
        'Vezina Trophy' as awardName,
        'Best Goaltender' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.vezinaTrophyWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.vezinaTrophyWinnerID IS NOT NULL 
    AND sa.vezinaTrophyWinnerID != ''
    AND FIND_IN_SET(sa.vezinaTrophyWinnerID, REPLACE(REPLACE(REPLACE(tsr.goalies, '[', ''), ']', ''), ' ', '')) > 0

    UNION ALL

    -- Calder Trophy (Best Rookie - Any position)
    SELECT 
        sa.seasonID,
        sa.calderWinnerID as playerID,
        np.firstName,
        np.lastName,
        'Calder Trophy' as awardName,
        'Best Rookie' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.calderWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.calderWinnerID IS NOT NULL 
    AND sa.calderWinnerID != ''
    AND (
        FIND_IN_SET(sa.calderWinnerID, REPLACE(REPLACE(REPLACE(tsr.forwards, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.calderWinnerID, REPLACE(REPLACE(REPLACE(tsr.defensemen, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.calderWinnerID, REPLACE(REPLACE(REPLACE(tsr.goalies, '[', ''), ']', ''), ' ', '')) > 0
    )

    UNION ALL

    -- Ted Lindsay Award (Most Outstanding Player as voted by players)
    SELECT 
        sa.seasonID,
        sa.lindsayWinnerID as playerID,
        np.firstName,
        np.lastName,
        'Ted Lindsay Award' as awardName,
        'Most Outstanding Player (Players Vote)' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.lindsayWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.lindsayWinnerID IS NOT NULL 
    AND sa.lindsayWinnerID != ''
    AND (
        FIND_IN_SET(sa.lindsayWinnerID, REPLACE(REPLACE(REPLACE(tsr.forwards, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.lindsayWinnerID, REPLACE(REPLACE(REPLACE(tsr.defensemen, '[', ''), ']', ''), ' ', '')) > 0
    )

    UNION ALL

    -- Selke Trophy (Best Defensive Forward)
    SELECT 
        sa.seasonID,
        sa.selkeWinnerID as playerID,
        np.firstName,
        np.lastName,
        'Selke Trophy' as awardName,
        'Best Defensive Forward' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.selkeWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.selkeWinnerID IS NOT NULL 
    AND sa.selkeWinnerID != ''
    AND FIND_IN_SET(sa.selkeWinnerID, REPLACE(REPLACE(REPLACE(tsr.forwards, '[', ''), ']', ''), ' ', '')) > 0

    UNION ALL

    -- King Clancy Award (Leadership and humanitarian contributions)
    SELECT 
        sa.seasonID,
        sa.kingClancyWinnerID as playerID,
        np.firstName,
        np.lastName,
        'King Clancy Memorial Trophy' as awardName,
        'Leadership & Humanitarian Contributions' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.kingClancyWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.kingClancyWinnerID IS NOT NULL 
    AND sa.kingClancyWinnerID != ''
    AND (
        FIND_IN_SET(sa.kingClancyWinnerID, REPLACE(REPLACE(REPLACE(tsr.forwards, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.kingClancyWinnerID, REPLACE(REPLACE(REPLACE(tsr.defensemen, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.kingClancyWinnerID, REPLACE(REPLACE(REPLACE(tsr.goalies, '[', ''), ']', ''), ' ', '')) > 0
    )

    UNION ALL

    -- Rocket Richard Trophy (Most Goals)
    SELECT 
        sa.seasonID,
        sa.richardTrophyWinnerID as playerID,
        np.firstName,
        np.lastName,
        'Maurice Richard Trophy' as awardName,
        'Most Goals Scored' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.richardTrophyWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.richardTrophyWinnerID IS NOT NULL 
    AND sa.richardTrophyWinnerID != ''
    AND (
        FIND_IN_SET(sa.richardTrophyWinnerID, REPLACE(REPLACE(REPLACE(tsr.forwards, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.richardTrophyWinnerID, REPLACE(REPLACE(REPLACE(tsr.defensemen, '[', ''), ']', ''), ' ', '')) > 0
    )

    UNION ALL

    -- Mark Messier Leadership Award
    SELECT 
        sa.seasonID,
        sa.messierTrophyWinnerID as playerID,
        np.firstName,
        np.lastName,
        'Mark Messier Leadership Award' as awardName,
        'Leadership & Contributions to Game' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.messierTrophyWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.messierTrophyWinnerID IS NOT NULL 
    AND sa.messierTrophyWinnerID != ''
    AND (
        FIND_IN_SET(sa.messierTrophyWinnerID, REPLACE(REPLACE(REPLACE(tsr.forwards, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.messierTrophyWinnerID, REPLACE(REPLACE(REPLACE(tsr.defensemen, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.messierTrophyWinnerID, REPLACE(REPLACE(REPLACE(tsr.goalies, '[', ''), ']', ''), ' ', '')) > 0
    )

    UNION ALL

    -- Masterton Trophy (Perseverance and dedication)
    SELECT 
        sa.seasonID,
        sa.mastertonWinnerID as playerID,
        np.firstName,
        np.lastName,
        'Bill Masterton Memorial Trophy' as awardName,
        'Perseverance & Dedication' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.mastertonWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.mastertonWinnerID IS NOT NULL 
    AND sa.mastertonWinnerID != ''
    AND (
        FIND_IN_SET(sa.mastertonWinnerID, REPLACE(REPLACE(REPLACE(tsr.forwards, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.mastertonWinnerID, REPLACE(REPLACE(REPLACE(tsr.defensemen, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.mastertonWinnerID, REPLACE(REPLACE(REPLACE(tsr.goalies, '[', ''), ']', ''), ' ', '')) > 0
    )

    UNION ALL

    -- Conn Smythe Trophy (Playoff MVP)
    SELECT 
        sa.seasonID,
        sa.connSmytheWinnerID as playerID,
        np.firstName,
        np.lastName,
        'Conn Smythe Trophy' as awardName,
        'Playoff Most Valuable Player' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.connSmytheWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.connSmytheWinnerID IS NOT NULL 
    AND sa.connSmytheWinnerID != ''
    AND (
        FIND_IN_SET(sa.connSmytheWinnerID, REPLACE(REPLACE(REPLACE(tsr.forwards, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.connSmytheWinnerID, REPLACE(REPLACE(REPLACE(tsr.defensemen, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.connSmytheWinnerID, REPLACE(REPLACE(REPLACE(tsr.goalies, '[', ''), ']', ''), ' ', '')) > 0
    )

    UNION ALL

    -- Norris Trophy (Best Defenseman)
    SELECT 
        sa.seasonID,
        sa.norrisWinnerID as playerID,
        np.firstName,
        np.lastName,
        'James Norris Memorial Trophy' as awardName,
        'Best Defenseman' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.norrisWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.norrisWinnerID IS NOT NULL 
    AND sa.norrisWinnerID != ''
    AND FIND_IN_SET(sa.norrisWinnerID, REPLACE(REPLACE(REPLACE(tsr.defensemen, '[', ''), ']', ''), ' ', '')) > 0

    UNION ALL

    -- Art Ross Trophy (Most Points)
    SELECT 
        sa.seasonID,
        sa.artRossWinnerID as playerID,
        np.firstName,
        np.lastName,
        'Art Ross Trophy' as awardName,
        'Most Points in Regular Season' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.artRossWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.artRossWinnerID IS NOT NULL 
    AND sa.artRossWinnerID != ''
    AND (
        FIND_IN_SET(sa.artRossWinnerID, REPLACE(REPLACE(REPLACE(tsr.forwards, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.artRossWinnerID, REPLACE(REPLACE(REPLACE(tsr.defensemen, '[', ''), ']', ''), ' ', '')) > 0
    )

    UNION ALL

    -- Lady Byng Trophy (Sportsmanship)
    SELECT 
        sa.seasonID,
        sa.ladyByngWinnerID as playerID,
        np.firstName,
        np.lastName,
        'Lady Byng Memorial Trophy' as awardName,
        'Sportsmanship & Gentlemanly Conduct' as awardDescription
    FROM season_awards sa
    JOIN team_season_rosters tsr ON sa.seasonID = tsr.season
    JOIN nhl_players np ON sa.ladyByngWinnerID = np.playerId
    WHERE tsr.team_id = $team_id 
    AND sa.ladyByngWinnerID IS NOT NULL 
    AND sa.ladyByngWinnerID != ''
    AND (
        FIND_IN_SET(sa.ladyByngWinnerID, REPLACE(REPLACE(REPLACE(tsr.forwards, '[', ''), ']', ''), ' ', '')) > 0
        OR FIND_IN_SET(sa.ladyByngWinnerID, REPLACE(REPLACE(REPLACE(tsr.defensemen, '[', ''), ']', ''), ' ', '')) > 0
    )

    ORDER BY seasonID DESC, awardName";

$individualAwardsResult = mysqli_query($conn, $individualAwardsSql);
$individualAwards = [];

if ($individualAwardsResult && mysqli_num_rows($individualAwardsResult) > 0) {
    while ($award = mysqli_fetch_assoc($individualAwardsResult)) {
        $individualAwards[] = [
            'season' => $award['seasonID'],
            'playerName' => $award['firstName'] . ' ' . $award['lastName'],
            'playerId' => $award['playerID'],
            'awardName' => $award['awardName'],
            'awardDescription' => $award['awardDescription']
        ];
    }
}

// Group awards by type for display
$awardsByType = [];
foreach ($individualAwards as $award) {
    $awardsByType[$award['awardName']][] = $award;
}
?>

<!-- Team Achievements -->
<div class="my-8">
    <h2 class="text-2xl font-bold mb-4 text-white">Team Achievements</h2>
    <div class='p-4 season-stats-section mx-auto bg-gradient-to-br from-gray-900/95 to-gray-800/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700/50 overflow-hidden'>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Left: Major Championships -->
            <div>
                <!-- Stanley Cup -->
                <h3 class="text-xl mb-2">
                    Stanley Cup Championships: 
                    <span class="font-bold text-2xl text-yellow-400"><?php echo count($stanleyCups); ?></span>
                </h3>
                <div class="mb-4">
                    <?php foreach ($stanleyCups as $season): ?>
                        <span class="inline-block px-3 py-1 mr-2 mb-1 rounded text-sm font-medium text-white" 
                              style="background-color: <?php echo $teamColor1; ?>;">
                            <?php echo formatSeason($season); ?>
                        </span>
                    <?php endforeach; ?>
                </div>

                <!-- Presidents' Trophy -->
                <h3 class="text-lg mb-2">
                    Presidents' Trophies: 
                    <span class="font-bold text-xl text-yellow-400"><?php echo count($presidentsTrophies); ?></span>
                </h3>
                <div class="mb-4">
                    <?php foreach ($presidentsTrophies as $season): ?>
                        <span class="inline-block px-3 py-1 mr-2 mb-1 rounded text-sm font-medium text-white" 
                              style="background-color: <?php echo $teamColor2; ?>;">
                            <?php echo formatSeason($season); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
                        <div class="border-t border-gray-600 pt-4 mt-4">
                <h3 class="text-xl mb-4">Other Team Awards:</h3>
                
                <!-- Team Awards -->
                <!-- Jack Adams Award -->
                <div class="mb-4">
                    <p class="mb-2 font-semibold text-white">
                        Jack Adams Award (Coach of the Year):
                    </p>
                    <?php if (count($jackAdamsSeasons) > 0): ?>
                        <?php for ($i = 0; $i < count($jackAdamsSeasons); $i++): ?>
                            <div class="text-sm text-gray-300 mb-1">
                                <?php echo formatSeason($jackAdamsSeasons[$i]); ?>: 
                                <span class="text-sky-400"><?php echo $jackAdamsCoaches[$i]; ?></span>
                            </div>
                        <?php endfor; ?>
                    <?php else: ?>
                        <div class="text-sm text-gray-500">None</div>
                    <?php endif; ?>
                </div>

                <!-- Jennings Trophy -->
                <div class="mb-4">
                    <p class="mb-2 font-semibold text-white">
                        Jennings Trophy (Best Team Goals Against Average):
                    </p>
                    <?php if (count($jenningsSeasons) > 0): ?>
                        <?php for ($i = 0; $i < count($jenningsSeasons); $i++): ?>
                            <div class="text-sm text-gray-300 mb-1">
                                <?php echo formatSeason($jenningsSeasons[$i]); ?>: 
                                <span class="text-sky-400">
                                    <?php 
                                    // Display player names, handling multiple players
                                    if (isset($jenningsPlayerNames[$i]) && !empty($jenningsPlayerNames[$i])) {
                                        echo implode(' & ', $jenningsPlayerNames[$i]);
                                    } else {
                                        echo "Unknown Player(s)";
                                    }
                                    ?>
                                </span>
                            </div>
                        <?php endfor; ?>
                    <?php else: ?>
                        <div class="text-sm text-gray-500">None</div>
                    <?php endif; ?>
                </div>
            </div>

            </div>

            <!-- Right: All Awards -->
            <div>

                <!-- Individual Player Awards -->
                <?php if (count($individualAwards) > 0): ?>
                    
                        <p class="mb-6 text-xl text-white">Individual Player Awards:</p>
                        
                        <?php foreach ($awardsByType as $awardType => $winners): ?>
                            <div class="mb-3">
                                <p class="mb-1 font-medium text-gray-200">
                                    <?php echo $awardType; ?>:
                                </p>
                                <?php foreach ($winners as $winner): ?>
                                    <div class="text-sm text-gray-300 mb-1 ml-2">
                                        <?php echo formatSeason($winner['season']); ?>: 
                                        <a href="player_details.php?player_id=<?php echo $winner['playerId']; ?>" 
                                           class="text-sky-400 hover:text-sky-300 hover:underline transition-colors">
                                            <?php echo $winner['playerName']; ?>
                                        </a>
                                        <span class="text-gray-500 text-xs">
                                            (<?php echo $winner['awardDescription']; ?>)
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="border-t border-gray-600 pt-4 mt-4">
                        <p class="text-gray-500">No individual player awards</p>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>


   

        <!-- Subtle Divider -->
        <div class="my-6 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
        <br>

<!-- Season Statistics Section -->
<div class="mb-8 flex justify-between max-w-[90%] mx-auto">
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
echo "<div class='season-stats-section max-w-[90%] mx-auto bg-gradient-to-br from-gray-900/95 to-gray-800/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-gray-700/50 overflow-hidden'>"; // Centered container for content

?>
<div class="season-tabs-container bg-gray-800/90 backdrop-blur-sm border-b border-gray-700/50 p-4">
    <div class="season-tab flex flex-wrap justify-between max-w-4xl mx-auto">
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


<div class='season-tab-pane active' id='tab1'>
    <div class="overview-dashboard p-6">
        
        <?php
        // First, let's collect and organize the data for visualization
        mysqli_data_seek($overallStatsResult, 0);
        $seasonData = [];
        $currentSeasonData = null;
        
        while ($row = mysqli_fetch_assoc($overallStatsResult)) {
            $seasonId = $row['season_id'];
            $seasonDisplay = substr($seasonId, 0, 4) . "-" . substr($seasonId, 4, 4);
            
            $data = [
                'season' => $seasonId,
                'seasonDisplay' => $seasonDisplay,
                'gp' => $row['gp'],
                'wins' => $row['win'],
                'losses' => $row['loss'],
                'otLosses' => $row['otLoss'],
                'ties' => $row['ties'] ?? 0,
                'points' => $row['pts'],
                'regWins' => $row['winsInRegulation'],
                'regOtWins' => $row['regulationAndOtWins'],
                'soWins' => $row['winsInShootout'],
                'goalsFor' => $row['goalFor'],
                'goalsAgainst' => $row['goalAgainst'],
                'goalsForPerGame' => $row['goalForPerGame'],
                'goalsAgainstPerGame' => $row['goalAgainstPerGame'],
                'shotsForPerGame' => $row['shotForPerGame'],
                'shotsAgainstPerGame' => $row['shotAgainstPerGame'],
                'faceoffWinPct' => $row['faceoffWinPctg'],
                'powerPlayPct' => $row['powerPlayPctg'],
                'penaltyKillPct' => $row['penaltyKillPctg'],
                'pointPct' => $row['pointPct']
            ];
            
            $seasonData[] = $data;
            
            // Set the most recent season as current (assuming data is ordered)
            if ($currentSeasonData === null) {
                $currentSeasonData = $data;
            }
        }
        
        // If we have current season data, let's create the dashboard
        // If we have current season data, let's create the dashboard
if ($currentSeasonData):
    $otWins = $currentSeasonData['regOtWins'] - $currentSeasonData['regWins'];
    $goalDifferential = $currentSeasonData['goalsFor'] - $currentSeasonData['goalsAgainst'];
?>

<?php
// ADD STEP 1 CODE HERE - League Rankings Calculation
$currentSeasonForRanking = $currentSeasonData['season']; // e.g., "20242025"

// Get all teams' data for the current season for ranking calculations
$rankingSQL = "SELECT 
    team_id,
    powerPlayPct,
    penaltyKillPct,
    pointPct,
    goalsFor,
    goalsAgainst,
    shotsForPerGame,
    shotsAgainstPerGame,
    faceoffWinPct,
    pts
FROM team_overall_stats_by_season
WHERE season_id = '$currentSeasonForRanking'
ORDER BY team_id";

$rankingResult = mysqli_query($conn, $rankingSQL);
$allTeamsData = [];

while ($teamRow = mysqli_fetch_assoc($rankingResult)) {
    $allTeamsData[] = $teamRow;
}

// Calculate rankings for various stats
function calculateRanking($allTeamsData, $statColumn, $currentTeamId, $higherIsBetter = true) {
    // Sort teams by the stat
    usort($allTeamsData, function($a, $b) use ($statColumn, $higherIsBetter) {
        if ($higherIsBetter) {
            return $b[$statColumn] <=> $a[$statColumn]; // Descending for "higher is better"
        } else {
            return $a[$statColumn] <=> $b[$statColumn]; // Ascending for "lower is better"
        }
    });
    
    // Find current team's rank
    $rank = 1;
    foreach ($allTeamsData as $index => $team) {
        if ($team['team_id'] == $currentTeamId) {
            return $index + 1; // Rank starts at 1
        }
    }
    return null;
}

// Calculate all the rankings
$rankings = [
    'powerPlay' => calculateRanking($allTeamsData, 'powerPlayPct', $team_id, true),
    'penaltyKill' => calculateRanking($allTeamsData, 'penaltyKillPct', $team_id, true),
    'points' => calculateRanking($allTeamsData, 'pts', $team_id, true),
    'pointPct' => calculateRanking($allTeamsData, 'pointPct', $team_id, true),
    'goalsFor' => calculateRanking($allTeamsData, 'goalsFor', $team_id, true),
    'goalsAgainst' => calculateRanking($allTeamsData, 'goalsAgainst', $team_id, false), // Lower is better
    'shotsFor' => calculateRanking($allTeamsData, 'shotsForPerGame', $team_id, true),
    'shotsAgainst' => calculateRanking($allTeamsData, 'shotsAgainstPerGame', $team_id, false), // Lower is better
    'faceoffs' => calculateRanking($allTeamsData, 'faceoffWinPct', $team_id, true)
];

// Helper function to format rankings with ordinal suffix and color
function formatRanking($rank, $totalTeams = 32) {
    if ($rank === null) return '';
    
    $suffix = 'th';
    if ($rank % 10 == 1 && $rank % 100 != 11) $suffix = 'st';
    else if ($rank % 10 == 2 && $rank % 100 != 12) $suffix = 'nd';
    else if ($rank % 10 == 3 && $rank % 100 != 13) $suffix = 'rd';
    
    // Color coding based on ranking
    if ($rank <= 5) {
        $color = 'text-green-400'; // Top 5
    } else if ($rank <= 10) {
        $color = 'text-yellow-400'; // Top 10
    } else if ($rank <= 20) {
        $color = 'text-orange-400'; // Middle
    } else {
        $color = 'text-red-400'; // Bottom
    }
    
    return "<span class='$color text-sm'>({$rank}{$suffix})</span>";
}
?>

<!-- Season Selector Info -->
<div class="mb-6 text-center">
    <h3 class="text-2xl font-bold text-white mb-2">Season Overview</h3>
    <p class="text-gray-300">Viewing data for: <span class="font-semibold text-blue-400" id="currentSeasonDisplay"><?php echo $currentSeasonData['seasonDisplay']; ?></span></p>
</div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <!-- Record -->
            <div class="metric-card bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold text-white" id="record">
                        <?php echo $currentSeasonData['wins']; ?>-<?php echo $currentSeasonData['losses']; ?>-<?php echo $currentSeasonData['otLosses']; ?>
                    </div>
                    <div class="text-sm text-gray-400">Record</div>
                </div>
            </div>
            
            <!-- Points -->
            <div class="metric-card bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold" id="points">
                        <?php echo $currentSeasonData['points']; ?>
                        <?php echo formatRanking($rankings['points']); ?>
                    </div>
                    <div class="text-sm text-gray-400">Points</div>
                </div>
            </div>
            
            <!-- Point Percentage -->
            <div class="metric-card bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold" id="pointPct">
                        <?php echo number_format($currentSeasonData['pointPct'], 2); ?>%
                        <?php echo formatRanking($rankings['pointPct']); ?>
                    </div>
                    <div class="text-sm text-gray-400">Point %</div>
                </div>
            </div>
            
            <!-- Goal Differential -->
            <div class="metric-card bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold <?php echo $goalDifferential >= 0 ? 'text-green-400' : 'text-red-400'; ?>" id="goalDiff">
                        <?php echo ($goalDifferential >= 0 ? '+' : '') . $goalDifferential; ?>
                    </div>
                    <div class="text-sm text-gray-400">Goal Diff</div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Win/Loss Breakdown Pie Chart -->
            <div class="chart-container bg-gray-800/90 rounded-lg p-6 border-2" style="border-color: <?php echo $teamColor2; ?>;">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">Game Results (Reg. Season)</h4>
                <div class="relative h-64 flex items-center justify-center">
                    <canvas id="winLossChart" width="250" height="250"></canvas>
                </div>
                <div class="mt-4 flex justify-center space-x-4 text-sm">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded mr-2"></div>
                        <span class="text-gray-300">Wins</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded mr-2"></div>
                        <span class="text-gray-300">Losses</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-orange-500 rounded mr-2"></div>
                        <span class="text-gray-300">OT/SO Loss</span>
                    </div>
                </div>
            </div>

            <!-- Goals For vs Against -->
            <div class="chart-container bg-gray-800/90 rounded-lg p-6 border-2" style="border-color: <?php echo $teamColor2; ?>;">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">Goals For vs Against</h4>
                <div class="relative h-64">
                    <canvas id="goalsChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Special Teams & Advanced Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- Special Teams -->
            <div class="stats-card bg-gray-800/90 rounded-lg p-6 border" style="border-color: <?php echo $teamColor1; ?>;">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">Special Teams</h4>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-300">Power Play</span>
                            <span class="font-semibold text-white" id="ppPct">
                                <?php echo number_format($currentSeasonData['powerPlayPct'], 2); ?>%
                            
                                <?php echo formatRanking($rankings['powerPlay']); ?>
                            </span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-500" 
                                 style="width: <?php echo $currentSeasonData['powerPlayPct']*100; ?>%; background-color: <?php echo $teamColor1; ?>;" 
                                 id="ppBar"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-300">Penalty Kill</span>
                            <span class="font-semibold text-white" id="pkPct">
                                <?php echo number_format($currentSeasonData['penaltyKillPct'], 2); ?>%

                                <?php echo formatRanking($rankings['penaltyKill']); ?>
                            </span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-500" 
                                 style="width: <?php echo $currentSeasonData['penaltyKillPct']*100; ?>%; background-color: <?php echo $teamColor2; ?>;" 
                                 id="pkBar"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shooting Stats -->
            <div class="stats-card bg-gray-800/90 rounded-lg p-6 border" style="border-color: <?php echo $teamColor1; ?>;">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">Shooting</h4>
                <div class="space-y-4">
                    <div class="text-center p-3 bg-gray-700/50 rounded-lg">
                        <div class="text-xl font-bold text-white" id="shotsFor">
                            <?php echo number_format($currentSeasonData['shotsForPerGame'], 1); ?>
                            <?php echo formatRanking($rankings['shotsFor']); ?>
                        </div>
                        <div class="text-sm">Shots For/Game</div>
                    </div>
                    <div class="text-center p-3 bg-gray-700/50 rounded-lg">
                        <div class="text-xl font-bold text-white" id="shotsAgainst">
                            <?php echo number_format($currentSeasonData['shotsAgainstPerGame'], 1); ?>
                            <?php echo formatRanking($rankings['shotsAgainst']); ?>
                        </div>
                        <div class="text-sm">Shots Against/Game</div>
                    </div>
                </div>
            </div>

            <!-- Win Types Breakdown -->
            <div class="stats-card bg-gray-800/90 rounded-lg p-6 border" style="border-color: <?php echo $teamColor1; ?>;">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">Win Types (incl. Post.)</h4>
                <div class="space-y-3">
                    <div class="flex justify-between p-2 bg-gray-700/50 rounded">
                        <span class="text-gray-300">Regulation</span>
                        <span class="font-semibold text-white" id="regWins">
                            <?php echo $currentSeasonData['regWins']; ?>
                        </span>
                    </div>
                    <div class="flex justify-between p-2 bg-gray-700/50 rounded">
                        <span class="text-gray-300">Overtime</span>
                        <span class="font-semibold text-white" id="otWins">
                            <?php echo $otWins; ?>
                        </span>
                    </div>
                    <div class="flex justify-between p-2 bg-gray-700/50 rounded">
                        <span class="text-gray-300">Shootout</span>
                        <span class="font-semibold text-white" id="soWins">
                            <?php echo $currentSeasonData['soWins']; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Season History Chart -->
        <div class="chart-container bg-gray-800/90 rounded-lg p-6 border" style="border-color: <?php echo $teamColor2; ?>;">
            <h4 class="text-lg font-semibold text-white mb-4 text-center">Points History (Last 10 Seasons)</h4>
            <div class="relative h-64">
                <canvas id="historyChart" width="800" height="250"></canvas>
            </div>
        </div>

        <?php endif; ?>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Pass PHP data to JavaScript
        const seasonData = <?php echo json_encode($seasonData); ?>;
        const teamColors = {
            primary: '<?php echo $teamColor1; ?>',
            secondary: '<?php echo $teamColor2; ?>'
        };
        
        // Convert hex to rgba
        function hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }

        // Wait for DOM to load
        document.addEventListener('DOMContentLoaded', function() {
            
            // Get current season data (first in array)
            const currentSeason = seasonData[0];
            
            // Win/Loss Pie Chart
            const winLossCtx = document.getElementById('winLossChart').getContext('2d');
            window.winLossChart = new Chart(winLossCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Wins', 'Losses', 'OT/SO Losses'],
                    datasets: [{
                        data: [currentSeason.wins, currentSeason.losses, currentSeason.otLosses],
                        backgroundColor: [
                            '#10B981', // Green for wins
                            '#EF4444', // Red for losses  
                            '#F59E0B'  // Orange for OT losses
                        ],
                        borderWidth: 2,
                        borderColor: '#374151'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Goals For vs Against Bar Chart
            const goalsCtx = document.getElementById('goalsChart').getContext('2d');
            window.goalsChart = new Chart(goalsCtx, {
                type: 'bar',
                data: {
                    labels: ['Goals For', 'Goals Against'],
                    datasets: [{
                        data: [currentSeason.goalsFor, currentSeason.goalsAgainst],
                        backgroundColor: [
                            hexToRgba(teamColors.primary, 0.8),
                            hexToRgba(teamColors.secondary, 0.8)
                        ],
                        borderColor: [
                            teamColors.primary,
                            teamColors.secondary
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#374151'
                            },
                            ticks: {
                                color: '#9CA3AF'
                            }
                        },
                        x: {
                            grid: {
                                color: '#374151'
                            },
                            ticks: {
                                color: '#9CA3AF'
                            }
                        }
                    }
                }
            });

            // Points History Line Chart (last 10 seasons)
            const last10Seasons = seasonData.slice(0, 10).reverse();
            const historyCtx = document.getElementById('historyChart').getContext('2d');
            window.historyChart = new Chart(historyCtx, {
                type: 'line',
                data: {
                    labels: last10Seasons.map(s => s.seasonDisplay),
                    datasets: [{
                        label: 'Points',
                        data: last10Seasons.map(s => s.points),
                        borderColor: teamColors.primary,
                        backgroundColor: hexToRgba(teamColors.primary, 0.1),
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 150,
                            grid: {
                                color: '#374151'
                            },
                            ticks: {
                                color: '#9CA3AF'
                            }
                        },
                        x: {
                            grid: {
                                color: '#374151'
                            },
                            ticks: {
                                color: '#9CA3AF'
                            }
                        }
                    }
                }
            });
        });

        // Enhanced function to update charts AND rankings when season changes
function updateOverviewCharts(seasonId) {
    const selectedSeason = seasonData.find(s => s.season === seasonId);
    if (!selectedSeason) return;
    
    console.log('Updating overview for season:', seasonId);
    
    // Update metric cards (keeping existing functionality)
    document.getElementById('record').textContent = `${selectedSeason.wins}-${selectedSeason.losses}-${selectedSeason.otLosses}`;
    document.getElementById('points').innerHTML = selectedSeason.points; // Remove rankings for now, will add back with AJAX
    document.getElementById('pointPct').innerHTML = (selectedSeason.pointPct * 100).toFixed(1) + '%';
    
    const goalDiff = selectedSeason.goalsFor - selectedSeason.goalsAgainst;
    const goalDiffEl = document.getElementById('goalDiff');
    goalDiffEl.textContent = (goalDiff >= 0 ? '+' : '') + goalDiff;
    goalDiffEl.className = `text-2xl font-bold ${goalDiff >= 0 ? 'text-green-400' : 'text-red-400'}`;
    
    // Update special teams with proper percentage calculation
    const ppPct = parseFloat(selectedSeason.powerPlayPct) * 100; // Convert decimal to percentage
    const pkPct = parseFloat(selectedSeason.penaltyKillPct) * 100; // Convert decimal to percentage
    
    document.getElementById('ppPct').innerHTML = ppPct.toFixed(2) + '%';
    document.getElementById('pkPct').innerHTML = pkPct.toFixed(2) + '%';
    
    // Fix progress bars
    document.getElementById('ppBar').style.width = ppPct + '%';
    document.getElementById('pkBar').style.width = pkPct + '%';
    
    // Update shooting stats
    document.getElementById('shotsFor').innerHTML = selectedSeason.shotsForPerGame.toFixed(1);
    document.getElementById('shotsAgainst').innerHTML = selectedSeason.shotsAgainstPerGame.toFixed(1);
    
    // Update win types
    document.getElementById('regWins').textContent = selectedSeason.regWins;
    document.getElementById('otWins').textContent = selectedSeason.regOtWins - selectedSeason.regWins;
    document.getElementById('soWins').textContent = selectedSeason.soWins;
    
    // Update season display
    document.getElementById('currentSeasonDisplay').textContent = selectedSeason.seasonDisplay;
    
    // Update charts if they exist (store chart references globally)
    if (window.winLossChart) {
        window.winLossChart.data.datasets[0].data = [selectedSeason.wins, selectedSeason.losses, selectedSeason.otLosses];
        window.winLossChart.update();
    }
    
    if (window.goalsChart) {
        window.goalsChart.data.datasets[0].data = [selectedSeason.goalsFor, selectedSeason.goalsAgainst];
        window.goalsChart.update();
    }
    
    console.log('Overview dashboard updated successfully');
}
    </script>
</div>

               <!-- TAB 2: HOME/ROAD SPLITS -->
<div class='season-tab-pane w-full flex justify-center' id='tab2'>
    <div class="home-road-dashboard p-6 w-full max-w-7xl">
        
        <?php
        // Collect and organize home/road data
        $statsSQL2 = "SELECT * FROM nhl_EOY_team_stats WHERE team_id = $team_id"; 
        $stats2 = mysqli_query($conn, $statsSQL2);
        $homeRoadData = [];
        $currentHomeRoadData = null;
        
        while ($row = mysqli_fetch_assoc($stats2)) {
            $seasonId = $row['season_id'];
            $seasonDisplay = substr($seasonId, 0, 4) . "-" . substr($seasonId, 4, 4);
            
            $data = [
                'season' => $seasonId,
                'seasonDisplay' => $seasonDisplay,
                // Home stats
                'homeGP' => $row['homeGamesPlayed'],
                'homeWins' => $row['homeWins'],
                'homeLosses' => $row['homeLosses'],
                'homeOtLosses' => $row['homeOtLosses'],
                'homeTies' => $row['homeTies'] ?? 0,
                'homePoints' => $row['homePoints'],
                'homeGoalsFor' => $row['homeGoalsFor'],
                'homeGoalsAgainst' => $row['homeGoalsAgainst'],
                'homeGoalDiff' => $row['homeGoalDifferential'],
                'homeRegWins' => $row['homeRegulationWins'],
                'homeRegOtWins' => $row['homeRegulationPlusOtWins'],
                // Road stats
                'roadGP' => $row['roadGamesPlayed'],
                'roadWins' => $row['roadWins'],
                'roadLosses' => $row['roadLosses'],
                'roadOtLosses' => $row['roadOtLosses'],
                'roadTies' => $row['roadTies'] ?? 0,
                'roadPoints' => $row['roadPoints'],
                'roadGoalsFor' => $row['roadGoalsFor'],
                'roadGoalsAgainst' => $row['roadGoalsAgainst'],
                'roadGoalDiff' => $row['roadGoalDifferential'],
                'roadRegWins' => $row['roadRegulationWins'],
                'roadRegOtWins' => $row['roadRegulationPlusOtWins']
            ];
            
            $homeRoadData[] = $data;
            
            // Set the most recent season as current
            if ($currentHomeRoadData === null) {
                $currentHomeRoadData = $data;
            }
        }
        
        if ($currentHomeRoadData):
            // Calculate additional metrics
            $homeWinPct = $currentHomeRoadData['homeGP'] > 0 ? ($currentHomeRoadData['homeWins'] / $currentHomeRoadData['homeGP']) * 100 : 0;
            $roadWinPct = $currentHomeRoadData['roadGP'] > 0 ? ($currentHomeRoadData['roadWins'] / $currentHomeRoadData['roadGP']) * 100 : 0;
            $homeGoalsPerGame = $currentHomeRoadData['homeGP'] > 0 ? $currentHomeRoadData['homeGoalsFor'] / $currentHomeRoadData['homeGP'] : 0;
            $roadGoalsPerGame = $currentHomeRoadData['roadGP'] > 0 ? $currentHomeRoadData['roadGoalsFor'] / $currentHomeRoadData['roadGP'] : 0;
        ?>
        
        <!-- Rest of the dashboard content stays the same -->
        <!-- Header -->
        <div class="mb-6 text-center">
            <h3 class="text-2xl font-bold text-white mb-2">Home vs Road Performance</h3>
            <p class="text-gray-300">Viewing data for: <span class="font-semibold text-blue-400" id="currentHomeRoadSeason"><?php echo $currentHomeRoadData['seasonDisplay']; ?></span></p>
        </div>

        <!-- Home vs Road Comparison Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Home Performance Card -->
            <div class="bg-gray-800/90 rounded-lg p-6 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center mb-4">
                    <h4 class="text-xl font-bold text-white mb-2">🏠 Home Performance</h4>
                    <div class="text-3xl font-bold" id="homeRecord">
                        <?php echo $currentHomeRoadData['homeWins']; ?>-<?php echo $currentHomeRoadData['homeLosses']; ?>-<?php echo $currentHomeRoadData['homeOtLosses']; ?>
                    </div>
                    <div class="text-sm text-gray-400">Record</div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-300">Games Played</span>
                        <span class="font-semibold text-white" id="homeGP"><?php echo $currentHomeRoadData['homeGP']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Points</span>
                        <span class="font-semibold text-white" id="homePoints"><?php echo $currentHomeRoadData['homePoints']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Win %</span>
                        <span class="font-semibold"  id="homeWinPct"><?php echo number_format($homeWinPct, 1); ?>%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Goals For</span>
                        <span class="font-semibold text-white" id="homeGF"><?php echo $currentHomeRoadData['homeGoalsFor']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Goals Against</span>
                        <span class="font-semibold text-white" id="homeGA"><?php echo $currentHomeRoadData['homeGoalsAgainst']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Goal Differential</span>
                        <span class="font-semibold <?php echo $currentHomeRoadData['homeGoalDiff'] >= 0 ? 'text-green-400' : 'text-red-400'; ?>" id="homeGoalDiff">
                            <?php echo ($currentHomeRoadData['homeGoalDiff'] >= 0 ? '+' : '') . $currentHomeRoadData['homeGoalDiff']; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Road Performance Card -->
            <div class="bg-gray-800/90 rounded-lg p-6 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center mb-4">
                    <h4 class="text-xl font-bold text-white mb-2">✈️ Road Performance</h4>
                    <div class="text-3xl font-bold" id="roadRecord">
                        <?php echo $currentHomeRoadData['roadWins']; ?>-<?php echo $currentHomeRoadData['roadLosses']; ?>-<?php echo $currentHomeRoadData['roadOtLosses']; ?>
                    </div>
                    <div class="text-sm text-gray-400">Record</div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-300">Games Played</span>
                        <span class="font-semibold text-white" id="roadGP"><?php echo $currentHomeRoadData['roadGP']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Points</span>
                        <span class="font-semibold text-white" id="roadPoints"><?php echo $currentHomeRoadData['roadPoints']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Win %</span>
                        <span class="font-semibold" id="roadWinPct"><?php echo number_format($roadWinPct, 1); ?>%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Goals For</span>
                        <span class="font-semibold text-white" id="roadGF"><?php echo $currentHomeRoadData['roadGoalsFor']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Goals Against</span>
                        <span class="font-semibold text-white" id="roadGA"><?php echo $currentHomeRoadData['roadGoalsAgainst']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Goal Differential</span>
                        <span class="font-semibold <?php echo $currentHomeRoadData['roadGoalDiff'] >= 0 ? 'text-green-400' : 'text-red-400'; ?>" id="roadGoalDiff">
                            <?php echo ($currentHomeRoadData['roadGoalDiff'] >= 0 ? '+' : '') . $currentHomeRoadData['roadGoalDiff']; ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Win Percentage Comparison -->
            <div class="bg-gray-800/90 rounded-lg p-6 border" style="border-color: <?php echo $teamColor2; ?>;">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">Win Percentage Comparison</h4>
                <div class="relative h-64">
                    <canvas id="winPctChart" width="400" height="250"></canvas>
                </div>
            </div>

            <!-- Goals Comparison -->
            <div class="bg-gray-800/90 rounded-lg p-6 border" style="border-color: <?php echo $teamColor2; ?>;">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">Goals Comparison</h4>
                <div class="relative h-64">
                    <canvas id="goalsComparisonChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Home Advantage Analysis -->
        <div class="bg-gray-800/90 rounded-lg p-6 border" style="border-color: <?php echo $teamColor1; ?>;">
            <h4 class="text-lg font-semibold text-white mb-4 text-center">Home Ice Advantage</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Win Rate Difference -->
                <div class="text-center p-4 bg-gray-700/50 rounded-lg">
                    <div class="text-2xl font-bold <?php echo ($homeWinPct - $roadWinPct) >= 0 ? 'text-green-400' : 'text-red-400'; ?>" id="winPctDiff">
                        <?php echo ($homeWinPct - $roadWinPct >= 0 ? '+' : '') . number_format($homeWinPct - $roadWinPct, 1); ?>%
                    </div>
                    <div class="text-sm text-gray-400">Win % Difference</div>
                </div>
                
                <!-- Goals Per Game Difference -->
                <div class="text-center p-4 bg-gray-700/50 rounded-lg">
                    <div class="text-2xl font-bold <?php echo ($homeGoalsPerGame - $roadGoalsPerGame) >= 0 ? 'text-green-400' : 'text-red-400'; ?>" id="goalsPGDiff">
                        <?php echo ($homeGoalsPerGame - $roadGoalsPerGame >= 0 ? '+' : '') . number_format($homeGoalsPerGame - $roadGoalsPerGame, 2); ?>
                    </div>
                    <div class="text-sm text-gray-400">Goals/Game Difference</div>
                </div>
                
                <!-- Point Difference -->
                <div class="text-center p-4 bg-gray-700/50 rounded-lg">
                    <div class="text-2xl font-bold <?php echo ($currentHomeRoadData['homePoints'] - $currentHomeRoadData['roadPoints']) >= 0 ? 'text-green-400' : 'text-red-400'; ?>" id="pointsDiff">
                        <?php echo ($currentHomeRoadData['homePoints'] - $currentHomeRoadData['roadPoints'] >= 0 ? '+' : '') . ($currentHomeRoadData['homePoints'] - $currentHomeRoadData['roadPoints']); ?>
                    </div>
                    <div class="text-sm text-gray-400">Points Difference</div>
                </div>
            </div>
        </div>
<br>
        <!-- Historical Trends -->
        <div class="bg-gray-800/90 rounded-lg p-6 border" style="border-color: <?php echo $teamColor2; ?>;">
            <h4 class="text-lg font-semibold text-white mb-4 text-center">Home vs Road Trends (Last 10 Seasons)</h4>
            <div class="relative h-64">
                <canvas id="homeRoadTrendsChart" width="800" height="250"></canvas>
            </div>
        </div>

        <?php endif; ?>
    </div>

    <script>
        // Pass PHP data to JavaScript for home/road tab
        const homeRoadData = <?php echo json_encode($homeRoadData); ?>;
        
        // Initialize charts when this tab becomes active
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener for when tab 2 becomes active
            const tab2Button = document.querySelector('[data-tab="tab2"]');
            if (tab2Button) {
                tab2Button.addEventListener('click', function() {
                    // Small delay to ensure tab is visible before initializing charts
                    setTimeout(initializeHomeRoadCharts, 100);
                });
            }
        });
        
        function initializeHomeRoadCharts() {
            // Only initialize if tab2 is active and charts haven't been created yet
            if (!document.getElementById('tab2').classList.contains('active')) return;
            if (document.getElementById('winPctChart').chart) return; // Already initialized
            
            const currentData = homeRoadData[0];
            
            // Win Percentage Comparison Bar Chart
            const winPctCtx = document.getElementById('winPctChart').getContext('2d');
            const homeWinPct = currentData.homeGP > 0 ? (currentData.homeWins / currentData.homeGP) * 100 : 0;
            const roadWinPct = currentData.roadGP > 0 ? (currentData.roadWins / currentData.roadGP) * 100 : 0;
            
            document.getElementById('winPctChart').chart = new Chart(winPctCtx, {
                type: 'bar',
                data: {
                    labels: ['Home', 'Road'],
                    datasets: [{
                        label: 'Win Percentage',
                        data: [homeWinPct, roadWinPct],
                        backgroundColor: [
                            hexToRgba(teamColors.primary, 0.8),
                            hexToRgba(teamColors.secondary, 0.8)
                        ],
                        borderColor: [
                            teamColors.primary,
                            teamColors.secondary
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: '#374151' },
                            ticks: { 
                                color: '#9CA3AF',
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: { color: '#374151' },
                            ticks: { color: '#9CA3AF' }
                        }
                    }
                }
            });

            // Goals Comparison Chart
            const goalsCtx = document.getElementById('goalsComparisonChart').getContext('2d');
            document.getElementById('goalsComparisonChart').chart = new Chart(goalsCtx, {
                type: 'bar',
                data: {
                    labels: ['Goals For', 'Goals Against'],
                    datasets: [{
                        label: 'Home',
                        data: [currentData.homeGoalsFor, currentData.homeGoalsAgainst],
                        backgroundColor: hexToRgba(teamColors.primary, 0.8),
                        borderColor: teamColors.primary,
                        borderWidth: 2
                    }, {
                        label: 'Road',
                        data: [currentData.roadGoalsFor, currentData.roadGoalsAgainst],
                        backgroundColor: hexToRgba(teamColors.secondary, 0.8),
                        borderColor: teamColors.secondary,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: '#9CA3AF' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#374151' },
                            ticks: { color: '#9CA3AF' }
                        },
                        x: {
                            grid: { color: '#374151' },
                            ticks: { color: '#9CA3AF' }
                        }
                    }
                }
            });

            // Home vs Road Trends Line Chart
            const last10Seasons = homeRoadData.slice(0, 10).reverse();
            const trendsCtx = document.getElementById('homeRoadTrendsChart').getContext('2d');
            
            document.getElementById('homeRoadTrendsChart').chart = new Chart(trendsCtx, {
                type: 'line',
                data: {
                    labels: last10Seasons.map(s => s.seasonDisplay),
                    datasets: [{
                        label: 'Home Win %',
                        data: last10Seasons.map(s => s.homeGP > 0 ? (s.homeWins / s.homeGP) * 100 : 0),
                        borderColor: teamColors.primary,
                        backgroundColor: hexToRgba(teamColors.primary, 0.1),
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4
                    }, {
                        label: 'Road Win %',
                        data: last10Seasons.map(s => s.roadGP > 0 ? (s.roadWins / s.roadGP) * 100 : 0),
                        borderColor: teamColors.secondary,
                        backgroundColor: hexToRgba(teamColors.secondary, 0.1),
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: '#9CA3AF' }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: { color: '#374151' },
                            ticks: { 
                                color: '#9CA3AF',
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: { color: '#374151' },
                            ticks: { color: '#9CA3AF' }
                        }
                    }
                }
            });
        }

        // Function to update home/road charts when season changes
        function updateHomeRoadCharts(seasonId) {
            const selectedSeason = homeRoadData.find(s => s.season === seasonId);
            if (!selectedSeason) return;
            
            // Calculate percentages
            const homeWinPct = selectedSeason.homeGP > 0 ? (selectedSeason.homeWins / selectedSeason.homeGP) * 100 : 0;
            const roadWinPct = selectedSeason.roadGP > 0 ? (selectedSeason.roadWins / selectedSeason.roadGP) * 100 : 0;
            const homeGoalsPerGame = selectedSeason.homeGP > 0 ? selectedSeason.homeGoalsFor / selectedSeason.homeGP : 0;
            const roadGoalsPerGame = selectedSeason.roadGP > 0 ? selectedSeason.roadGoalsFor / selectedSeason.roadGP : 0;
            
            // Update home stats
            document.getElementById('homeRecord').textContent = `${selectedSeason.homeWins}-${selectedSeason.homeLosses}-${selectedSeason.homeOtLosses}`;
            document.getElementById('homeGP').textContent = selectedSeason.homeGP;
            document.getElementById('homePoints').textContent = selectedSeason.homePoints;
            document.getElementById('homeWinPct').textContent = homeWinPct.toFixed(1) + '%';
            document.getElementById('homeGF').textContent = selectedSeason.homeGoalsFor;
            document.getElementById('homeGA').textContent = selectedSeason.homeGoalsAgainst;
            
            const homeGoalDiffEl = document.getElementById('homeGoalDiff');
            homeGoalDiffEl.textContent = (selectedSeason.homeGoalDiff >= 0 ? '+' : '') + selectedSeason.homeGoalDiff;
            homeGoalDiffEl.className = `font-semibold ${selectedSeason.homeGoalDiff >= 0 ? 'text-green-400' : 'text-red-400'}`;
            
            // Update road stats
            document.getElementById('roadRecord').textContent = `${selectedSeason.roadWins}-${selectedSeason.roadLosses}-${selectedSeason.roadOtLosses}`;
            document.getElementById('roadGP').textContent = selectedSeason.roadGP;
            document.getElementById('roadPoints').textContent = selectedSeason.roadPoints;
            document.getElementById('roadWinPct').textContent = roadWinPct.toFixed(1) + '%';
            document.getElementById('roadGF').textContent = selectedSeason.roadGoalsFor;
            document.getElementById('roadGA').textContent = selectedSeason.roadGoalsAgainst;
            
            const roadGoalDiffEl = document.getElementById('roadGoalDiff');
            roadGoalDiffEl.textContent = (selectedSeason.roadGoalDiff >= 0 ? '+' : '') + selectedSeason.roadGoalDiff;
            roadGoalDiffEl.className = `font-semibold ${selectedSeason.roadGoalDiff >= 0 ? 'text-green-400' : 'text-red-400'}`;
            
            // Update home advantage metrics
            const winPctDiff = homeWinPct - roadWinPct;
            const goalsPGDiff = homeGoalsPerGame - roadGoalsPerGame;
            const pointsDiff = selectedSeason.homePoints - selectedSeason.roadPoints;
            
            const winPctDiffEl = document.getElementById('winPctDiff');
            winPctDiffEl.textContent = (winPctDiff >= 0 ? '+' : '') + winPctDiff.toFixed(1) + '%';
            winPctDiffEl.className = `text-2xl font-bold ${winPctDiff >= 0 ? 'text-green-400' : 'text-red-400'}`;
            
            const goalsPGDiffEl = document.getElementById('goalsPGDiff');
            goalsPGDiffEl.textContent = (goalsPGDiff >= 0 ? '+' : '') + goalsPGDiff.toFixed(2);
            goalsPGDiffEl.className = `text-2xl font-bold ${goalsPGDiff >= 0 ? 'text-green-400' : 'text-red-400'}`;
            
            const pointsDiffEl = document.getElementById('pointsDiff');
            pointsDiffEl.textContent = (pointsDiff >= 0 ? '+' : '') + pointsDiff;
            pointsDiffEl.className = `text-2xl font-bold ${pointsDiff >= 0 ? 'text-green-400' : 'text-red-400'}`;
            
            // Update season display
            document.getElementById('currentHomeRoadSeason').textContent = selectedSeason.seasonDisplay;
        }
    </script>
</div>
            

<!-- TAB 3: SKATERS COMBINED TABLE -->
<div class='season-tab-pane w-full flex justify-center' id='tab3'>
    <div class="skaters-dashboard w-full max-w-7xl p-6">
        
        <?php
        // Collect and analyze skater data for the CURRENT season only
        mysqli_data_seek($result_skaters_combined, 0);
        $currentSeasonSkaters = [];
        $totalCapHit = 0;
        $validCapHits = 0;
        $totalShootingPct = 0;
        $validShootingPcts = 0;
        $totalFOPct = 0;
        $validFOPcts = 0;
        $totalPoints = 0;
        $totalGoals = 0;
        $totalAssists = 0;
        $positionCounts = ['C' => 0, 'LW' => 0, 'RW' => 0, 'D' => 0];
        $topScorer = null;
        $highestPaid = null;
        $currentSeasonId = null;
        
        // First pass: find the current season (most recent)
        while ($row = mysqli_fetch_assoc($result_skaters_combined)) {
            if ($currentSeasonId === null) {
                $currentSeasonId = $row['seasonWithType'];
            }
            break;
        }
        
        // Second pass: process only current season data
        mysqli_data_seek($result_skaters_combined, 0);
        while ($row = mysqli_fetch_assoc($result_skaters_combined)) {
            $seasonWithType = $row['seasonWithType'];
            
            // Only process current season data
            if ($seasonWithType !== $currentSeasonId) {
                continue;
            }
            
            // Process cap hit
            $capHitRaw = $row['capHit'];
            $capHitNumeric = null;
            if ($capHitRaw && $capHitRaw !== '-') {
                $capHitClean = substr($capHitRaw, 1); // Remove $
                $capHitClean = str_replace(',', '', $capHitClean); // Remove commas
                $capHitNumeric = floatval($capHitClean);
                if ($capHitNumeric > 0) {
                    $totalCapHit += $capHitNumeric;
                    $validCapHits++;
                    
                    if (!$highestPaid || $capHitNumeric > $highestPaid['capHit']) {
                        $highestPaid = [
                            'name' => $row['firstName'] . ' ' . $row['lastName'],
                            'capHit' => $capHitNumeric,
                            'position' => $row['position']
                        ];
                    }
                }
            }
            
            // Process shooting percentage
            if (isset($row['seasonShootingPct']) && $row['seasonShootingPct'] !== null) {
                $totalShootingPct += (float)$row['seasonShootingPct'] * 100;
                $validShootingPcts++;
            }
            
            // Process faceoff percentage
// Process faceoff percentage - ONLY include players with actual faceoff data
if (isset($row['seasonFOWinPct']) && $row['seasonFOWinPct'] !== null && $row['seasonFOWinPct'] > 0) {
    $totalFOPct += (float)$row['seasonFOWinPct'] * 100;
    $validFOPcts++;
}
            
            // Process points and find top scorer (current season only)
            $points = $row['seasonPoints'] ?? 0;
            $goals = $row['seasonGoals'] ?? 0;
            $assists = $row['seasonAssists'] ?? 0;
            
            $totalPoints += $points;
            $totalGoals += $goals;
            $totalAssists += $assists;
            
            if (!$topScorer || $points > $topScorer['points']) {
                $topScorer = [
                    'name' => $row['firstName'] . ' ' . $row['lastName'],
                    'points' => $points,
                    'goals' => $goals,
                    'assists' => $assists,
                    'position' => $row['position']
                ];
            }
            
            // Count positions
            $position = $row['position'];
            if ($position == 'R') {
                $positionCounts['RW']++;
            } else if ($position == 'L') {
                $positionCounts['LW']++;
            } else if ($position == 'C') {
                $positionCounts['C']++;
            } else if ($position == 'D') {
                $positionCounts['D']++;
            }
            
            $currentSeasonSkaters[] = $row;
        }
        
        // Calculate averages
        $avgCapHit = $validCapHits > 0 ? $totalCapHit / $validCapHits : 0;
        $avgShootingPct = $validShootingPcts > 0 ? $totalShootingPct / $validShootingPcts : 0;
        $avgFOPct = $validFOPcts > 0 ? $totalFOPct / $validFOPcts : 0;
        $totalSkaters = count($currentSeasonSkaters);
        $salaryCap = 88000000; // NHL salary cap (adjust as needed)
        $capSpaceUsed = ($totalCapHit / $salaryCap) * 100;
        ?>
        
        <!-- Header Section -->
        <div class="mb-6 text-center">
            <h3 class="text-2xl font-bold text-white mb-2">Team Skaters</h3>
            <p class="text-gray-300">Current Season Overview & Player Details</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            
            <!-- Total Skaters -->
            <div class="bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold text-white"><?php echo $totalSkaters; ?></div>
                    <div class="text-sm text-gray-400">Active Skaters</div>
                </div>
            </div>
            
            <!-- Total Points -->
            <div class="bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold"><?php echo $totalPoints; ?></div>
                    <div class="text-sm text-gray-400">Team Points</div>
                </div>
            </div>
            
            <!-- Total Team Salary -->
            <div class="bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-400">$<?php echo number_format($totalCapHit / 1000000, 1); ?>M</div>
                    <div class="text-sm text-gray-400">Total Salary</div>
                </div>
            </div>
            
            <!-- Average Cap Hit -->
            <div class="bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-400">$<?php echo number_format($avgCapHit / 1000000, 2); ?>M</div>
                    <div class="text-sm text-gray-400">Avg Cap Hit</div>
                </div>
            </div>
            
            <!-- Team Shooting % -->
            <div class="bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-400"><?php echo number_format($avgShootingPct, 1); ?>%</div>
                    <div class="text-sm text-gray-400">Avg Shooting %</div>
                </div>
            </div>
        </div>

        <!-- Salary Cap Usage -->
        <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700 mb-8">
            <h4 class="text-lg font-semibold text-white mb-4 text-center">💰 Salary Cap Usage (Skaters)</h4>
            <div class="flex items-center justify-between mb-2">
                <span class="text-gray-300">Cap Space Used</span>
                <span class="font-semibold text-white">$<?php echo number_format($totalCapHit / 1000000, 1); ?>M / $<?php echo number_format($salaryCap / 1000000, 1); ?>M</span>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-4 mb-2">
                <div class="h-4 rounded-full transition-all duration-500 <?php echo $capSpaceUsed > 95 ? 'bg-red-500' : ($capSpaceUsed > 85 ? 'bg-yellow-500' : 'bg-green-500'); ?>" 
                     style="width: <?php echo min($capSpaceUsed, 100); ?>%;"></div>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-400"><?php echo number_format($capSpaceUsed, 1); ?>% Used</span>
                <span class="text-gray-400">$<?php echo number_format(($salaryCap - $totalCapHit) / 1000000, 1); ?>M Remaining</span>
            </div>
        </div>

        <!-- Team Leaders & Position Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            
            <!-- Top Performer -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">🏆 Leading Scorer</h4>
                <?php if ($topScorer): ?>
                <div class="text-center">
                    <div class="text-xl font-bold"><?php echo $topScorer['name']; ?></div>
                    <div class="text-lg text-white mt-2"><?php echo $topScorer['points']; ?> Points</div>
                    <div class="text-sm text-gray-400"><?php echo $topScorer['goals']; ?>G • <?php echo $topScorer['assists']; ?>A</div>
                </div>
                <?php else: ?>
                <div class="text-center text-gray-400">No data available</div>
                <?php endif; ?>
            </div>

            <!-- Highest Paid -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">💰 Highest Paid</h4>
                <?php if ($highestPaid): ?>
                <div class="text-center">
                    <div class="text-xl font-bold"><?php echo $highestPaid['name']; ?></div>
                    <div class="text-lg text-green-400 mt-2">$<?php echo number_format($highestPaid['capHit'] / 1000000, 2); ?>M</div>
                    <div class="text-sm text-gray-400">Annual Cap Hit</div>
                </div>
                <?php else: ?>
                <div class="text-center text-gray-400">No salary data available</div>
                <?php endif; ?>
            </div>

            <!-- Position Breakdown -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">📊 Position Breakdown</h4>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-300">Centers (C)</span>
                        <span class="font-semibold text-white"><?php echo $positionCounts['C']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Left Wings (LW)</span>
                        <span class="font-semibold text-white"><?php echo $positionCounts['LW']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Right Wings (RW)</span>
                        <span class="font-semibold text-white"><?php echo $positionCounts['RW']; ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Defensemen (D)</span>
                        <span class="font-semibold text-white"><?php echo $positionCounts['D']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advanced Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Team Averages -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">📈 Team Averages</h4>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-300">Shooting Percentage</span>
                            <span class="font-semibold"><?php echo number_format($avgShootingPct, 2); ?>%</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-500" 
                                 style="width: <?php echo min($avgShootingPct, 100); ?>%; background-color: <?php echo $teamColor1; ?>;"></div>
                        </div>
                    </div>
                    
                    <?php if ($avgFOPct > 0): ?>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-300">Faceoff Win %</span>
                            <span class="font-semibold"><?php echo number_format($avgFOPct, 2); ?>%</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3">
                            <div class="h-3 rounded-full transition-all duration-500" 
                                 style="width: <?php echo $avgFOPct; ?>%; background-color: <?php echo $teamColor2; ?>;"></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Goals vs Assists Chart -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">🥅 Goals vs Assists</h4>
                <div class="relative h-48">
                    <canvas id="goalsAssistsChart" width="300" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Salary Distribution Chart -->
        <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700 mb-8">
            <h4 class="text-lg font-semibold text-white mb-4 text-center">💵 Average Salary by Position</h4>
            <div class="relative h-64">
                <canvas id="salaryDistributionChart" width="800" height="250"></canvas>
            </div>
        </div>

        <!-- Player Table -->
        <div class="bg-gray-800/90 rounded-lg p-4 border border-gray-700">
            <h4 class="text-lg font-semibold text-white mb-4 text-center">📋 Detailed Player Statistics</h4>
            <div class="overflow-x-auto">
                <table class='team-stats-table w-full' style='border: 2px solid <?php echo $teamColor2; ?>;'>
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
        </div>
    </div>

    <script>
        // Initialize skaters charts when tab becomes active
        document.addEventListener('DOMContentLoaded', function() {
            const tab3Button = document.querySelector('[data-tab="tab3"]');
            if (tab3Button) {
                tab3Button.addEventListener('click', function() {
                    setTimeout(initializeSkatersCharts, 100);
                });
            }
        });
        
        function initializeSkatersCharts() {
            if (!document.getElementById('tab3').classList.contains('active')) return;
            if (document.getElementById('goalsAssistsChart').chart) return; // Already initialized
            
            // Goals vs Assists Pie Chart
            const goalsAssistsCtx = document.getElementById('goalsAssistsChart').getContext('2d');
            document.getElementById('goalsAssistsChart').chart = new Chart(goalsAssistsCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Goals', 'Assists'],
                    datasets: [{
                        data: [<?php echo $totalGoals; ?>, <?php echo $totalAssists; ?>],
                        backgroundColor: [
                            hexToRgba(teamColors.primary, 0.8),
                            hexToRgba(teamColors.secondary, 0.8)
                        ],
                        borderColor: [
                            teamColors.primary,
                            teamColors.secondary
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: '#9CA3AF' }
                        }
                    }
                }
            });

            // Salary Distribution by Position Chart
            const salaryCtx = document.getElementById('salaryDistributionChart').getContext('2d');
            
            // Calculate average salaries by position
            const positionSalaries = {
                'C': 0, 'LW': 0, 'RW': 0, 'D': 0
            };
            const positionCounts = {
                'C': 0, 'LW': 0, 'RW': 0, 'D': 0
            };
            
            // Process current season salary data by position
            <?php
            echo "const salaryData = [";
            foreach ($currentSeasonSkaters as $skater) {
                $capHitRaw = $skater['capHit'];
                $capHitNumeric = 0;
                if ($capHitRaw && $capHitRaw !== '-') {
                    $capHitClean = substr($capHitRaw, 1);
                    $capHitClean = str_replace(',', '', $capHitClean);
                    $capHitNumeric = floatval($capHitClean) / 1000000; // Convert to millions
                }
                $position = $skater['position'];
                if ($position == 'R') $position = 'RW';
                else if ($position == 'L') $position = 'LW';
                
                echo "{position: '" . $position . "', salary: " . $capHitNumeric . "},";
            }
            echo "];";
            ?>
            
            // Calculate averages
            salaryData.forEach(player => {
                if (player.salary > 0) {
                    positionSalaries[player.position] += player.salary;
                    positionCounts[player.position]++;
                }
            });
            
            Object.keys(positionSalaries).forEach(pos => {
                if (positionCounts[pos] > 0) {
                    positionSalaries[pos] = positionSalaries[pos] / positionCounts[pos];
                }
            });
            
            document.getElementById('salaryDistributionChart').chart = new Chart(salaryCtx, {
                type: 'bar',
                data: {
                    labels: ['Centers', 'Left Wings', 'Right Wings', 'Defensemen'],
                    datasets: [{
                        label: 'Average Salary (M)',
                        data: [
                            positionSalaries['C'],
                            positionSalaries['LW'],
                            positionSalaries['RW'],
                            positionSalaries['D']
                        ],
                        backgroundColor: [
                            hexToRgba(teamColors.primary, 0.8),
                            hexToRgba(teamColors.secondary, 0.8),
                            hexToRgba(teamColors.primary, 0.6),
                            hexToRgba(teamColors.secondary, 0.6)
                        ],
                        borderColor: [
                            teamColors.primary,
                            teamColors.secondary,
                            teamColors.primary,
                            teamColors.secondary
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#374151' },
                            ticks: { 
                                color: '#9CA3AF',
                                callback: function(value) {
                                    return '$' + value.toFixed(1) + 'M';
                                }
                            }
                        },
                        x: {
                            grid: { color: '#374151' },
                            ticks: { color: '#9CA3AF' }
                        }
                    }
                }
            });
        }
    </script>
</div>
                        

       <!-- TAB 4: GOALIES COMBINED TABLE -->
<div class='season-tab-pane w-full flex justify-center' id='tab4'>
    <div class="goalies-dashboard w-full max-w-7xl p-6">
        
        <?php
        // Collect and analyze goalie data for the CURRENT season only
        mysqli_data_seek($result_goalies_combined, 0);
        $currentSeasonGoalies = [];
        $totalGamesPlayed = 0;
        $totalWins = 0;
        $totalLosses = 0;
        $totalOTLosses = 0;
        $totalTies = 0;
        $totalShutouts = 0;
        $totalSaves = 0;
        $totalShotsAgainst = 0;
        $totalGoalsAgainst = 0;
        $totalTOI = 0;
        $validGAA = 0;
        $totalGAA = 0;
        $validSavePct = 0;
        $totalSavePct = 0;
        $topGoalie = null;
        $currentSeasonId = null;
        
        // First pass: find the current season (most recent)
        while ($row = mysqli_fetch_assoc($result_goalies_combined)) {
            if ($currentSeasonId === null) {
                $currentSeasonId = $row['seasonWithType'];
            }
            break;
        }
        
        // Second pass: process only current season data
        mysqli_data_seek($result_goalies_combined, 0);
        while ($row = mysqli_fetch_assoc($result_goalies_combined)) {
            $seasonWithType = $row['seasonWithType'];
            
            // Only process current season data
            if ($seasonWithType !== $currentSeasonId) {
                continue;
            }
            
            // Process goalie stats
            $gamesPlayed = $row['seasonGamesPlayed'] ?? 0;
            $wins = $row['seasonWins'] ?? 0;
            $losses = $row['seasonLosses'] ?? 0;
            $otLosses = $row['seasonOTLosses'] ?? 0;
            $ties = $row['seasonTies'] ?? 0;
            $shutouts = $row['seasonSO'] ?? 0;
            $saves = $row['seasonSaves'] ?? 0;
            $shotsAgainst = $row['seasonSA'] ?? 0;
            $goalsAgainst = $row['seasonGA'] ?? 0;
            $toi = $row['seasonTOI'] ?? 0;
            $gaa = $row['seasonGAA'] ?? 0;
            $savePct = $row['seasonSavePct'] ?? 0;
            
            $totalGamesPlayed += $gamesPlayed;
            $totalWins += $wins;
            $totalLosses += $losses;
            $totalOTLosses += $otLosses;
            $totalTies += $ties;
            $totalShutouts += $shutouts;
            $totalSaves += $saves;
            $totalShotsAgainst += $shotsAgainst;
            $totalGoalsAgainst += $goalsAgainst;
            $totalTOI += $toi;
            
            // Process GAA and Save%
            if ($gaa > 0) {
                $totalGAA += $gaa;
                $validGAA++;
            }
            
            if ($savePct > 0) {
                $totalSavePct += $savePct;
                $validSavePct++;
            }
            
            // Find top goalie (by games played, then by save %)
            if (!$topGoalie || $gamesPlayed > $topGoalie['gamesPlayed'] || 
                ($gamesPlayed == $topGoalie['gamesPlayed'] && $savePct > $topGoalie['savePct'])) {
                $topGoalie = [
                    'name' => $row['firstName'] . ' ' . $row['lastName'],
                    'gamesPlayed' => $gamesPlayed,
                    'wins' => $wins,
                    'losses' => $losses,
                    'otLosses' => $otLosses,
                    'gaa' => $gaa,
                    'savePct' => $savePct,
                    'shutouts' => $shutouts
                ];
            }
            
            $currentSeasonGoalies[] = $row;
        }
        
        // Calculate averages
        $avgGAA = $validGAA > 0 ? $totalGAA / $validGAA : 0;
        $avgSavePct = $validSavePct > 0 ? $totalSavePct / $validSavePct : 0;
        $totalGoalies = count($currentSeasonGoalies);
        $teamSavePct = $totalShotsAgainst > 0 ? ($totalSaves / $totalShotsAgainst) : 0;
        $teamGAA = $totalTOI > 0 ? ($totalGoalsAgainst * 3600) / $totalTOI : 0;
        ?>
        
        <!-- Header Section -->
        <div class="mb-6 text-center">
            <h3 class="text-2xl font-bold text-white mb-2">Team Goalies</h3>
            <p class="text-gray-300">Current Season Goaltending Overview</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            
            <!-- Total Goalies -->
            <div class="bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold text-white"><?php echo $totalGoalies; ?></div>
                    <div class="text-sm text-gray-400">Active Goalies</div>
                </div>
            </div>
            
            <!-- Team Record -->
            <div class="bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor2; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold" style="color: <?php echo $teamColor1; ?>"><?php echo $totalWins; ?>-<?php echo $totalLosses; ?>-<?php echo $totalOTLosses; ?></div>
                    <div class="text-sm text-gray-400">Goalie Record</div>
                </div>
            </div>
            
            <!-- Team GAA -->
            <div class="bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold text-red-400"><?php echo number_format($teamGAA, 2); ?></div>
                    <div class="text-sm text-gray-400">Team GAA</div>
                </div>
            </div>
            
            <!-- Team Save % -->
            <div class="bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor2; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-400"><?php echo number_format($teamSavePct, 3); ?></div>
                    <div class="text-sm text-gray-400">Team Save %</div>
                </div>
            </div>
            
            <!-- Total Shutouts -->
            <div class="bg-gray-800/90 rounded-lg p-4 border-2" style="border-color: <?php echo $teamColor1; ?>;">
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-400"><?php echo $totalShutouts; ?></div>
                    <div class="text-sm text-gray-400">Shutouts</div>
                </div>
            </div>
        </div>

        <!-- Goaltending Performance Meters -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- GAA Performance -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">🥅 Goals Against Average</h4>
                <div class="text-center mb-4">
                    <div class="text-3xl font-bold text-red-400"><?php echo number_format($teamGAA, 2); ?></div>
                    <div class="text-sm text-gray-400">Team GAA</div>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-4">
                    <!-- GAA scale: excellent (0-2.2.25), good (2.25-2.4), average (2.4-2.9), poor (2.9+) -->
                    <?php 
                    $gaaPercentage = min((4.0 - $teamGAA) / 4.0 * 100, 100);
                    $gaaColor = $teamGAA <= 2.25 ? 'bg-green-500' : ($teamGAA <= 2.4 ? 'bg-yellow-500' : ($teamGAA <= 2.9 ? 'bg-orange-500' : 'bg-red-500'));
                    ?>
                    <div class="h-4 rounded-full transition-all duration-500 <?php echo $gaaColor; ?>" 
                         style="width: <?php echo max($gaaPercentage, 10); ?>%;"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-2">
                    <span>Excellent (≤2.25)</span>
                    <span>Poor (≥2.9)</span>
                </div>
            </div>

            <!-- Save % Performance -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">🛡️ Save Percentage</h4>
                <div class="text-center mb-4">
                    <div class="text-3xl font-bold text-blue-400"><?php echo number_format($teamSavePct, 3); ?></div>
                    <div class="text-sm text-gray-400">Team Save %</div>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-4">
                    <!-- Save% scale: 0.900 = 90%, excellent is 92%+ -->
                    <?php 
                    $savePercentage = ($teamSavePct - 0.850) / (0.950 - 0.850) * 100;
                    $savePercentage = max(0, min(100, $savePercentage));
                    $saveColor = $teamSavePct >= 0.920 ? 'bg-green-500' : ($teamSavePct >= 0.910 ? 'bg-yellow-500' : ($teamSavePct >= 0.900 ? 'bg-orange-500' : 'bg-red-500'));
                    ?>
                    <div class="h-4 rounded-full transition-all duration-500 <?php echo $saveColor; ?>" 
                         style="width: <?php echo max($savePercentage, 10); ?>%;"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-2">
                    <span>Poor (≤90%)</span>
                    <span>Excellent (≥92%)</span>
                </div>
            </div>
        </div>

        <!-- Team Leaders & Stats Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            
            <!-- Top Goalie -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">⭐ Starting Goaltender</h4>
                <?php if ($topGoalie): ?>
                <div class="text-center">
                    <div class="text-xl font-bold" style="color: <?php echo $teamColor1; ?>"><?php echo $topGoalie['name']; ?></div>
                    <div class="text-lg text-white mt-2"><?php echo $topGoalie['wins']; ?>-<?php echo $topGoalie['losses']; ?>-<?php echo $topGoalie['otLosses']; ?></div>
                    <div class="text-sm text-gray-400"><?php echo $topGoalie['gamesPlayed']; ?> GP • <?php echo number_format($topGoalie['gaa'], 2); ?> GAA</div>
                    <div class="text-sm text-gray-400"><?php echo number_format($topGoalie['savePct'] * 100, 1); ?>% Save • <?php echo $topGoalie['shutouts']; ?> SO</div>
                </div>
                <?php else: ?>
                <div class="text-center text-gray-400">No data available</div>
                <?php endif; ?>
            </div>

            <!-- Workload Distribution -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">⚡ Workload</h4>
                <div class="space-y-3">
                    <div class="text-center p-3 bg-gray-700/50 rounded-lg">
                        <div class="text-xl font-bold text-white"><?php echo $totalGamesPlayed; ?></div>
                        <div class="text-sm" style="color: <?php echo $teamColor1; ?>">Total Games</div>
                    </div>
                    <div class="text-center p-3 bg-gray-700/50 rounded-lg">
                        <div class="text-xl font-bold text-white"><?php echo number_format($totalShotsAgainst); ?></div>
                        <div class="text-sm" style="color: <?php echo $teamColor2; ?>">Shots Faced</div>
                    </div>
                    <div class="text-center p-3 bg-gray-700/50 rounded-lg">
                        <div class="text-xl font-bold text-white"><?php echo number_format($totalSaves); ?></div>
                        <div class="text-sm" style="color: <?php echo $teamColor1; ?>">Total Saves</div>
                    </div>
                </div>
            </div>

            <!-- Game Results -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">📊 Results Breakdown</h4>
                <div class="space-y-3">
                    <div class="flex justify-between p-2 bg-green-900/30 rounded">
                        <span class="text-gray-300">Wins</span>
                        <span class="font-semibold text-green-400"><?php echo $totalWins; ?></span>
                    </div>
                    <div class="flex justify-between p-2 bg-red-900/30 rounded">
                        <span class="text-gray-300">Losses</span>
                        <span class="font-semibold text-red-400"><?php echo $totalLosses; ?></span>
                    </div>
                    <div class="flex justify-between p-2 bg-orange-900/30 rounded">
                        <span class="text-gray-300">OT/SO Losses</span>
                        <span class="font-semibold text-orange-400"><?php echo $totalOTLosses; ?></span>
                    </div>
                    <?php if ($totalTies > 0): ?>
                    <div class="flex justify-between p-2 bg-gray-700/50 rounded">
                        <span class="text-gray-300">Ties</span>
                        <span class="font-semibold text-gray-400"><?php echo $totalTies; ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <!-- Wins vs Losses Chart -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">🏆 Win/Loss Distribution</h4>
                <div class="relative h-64">
                    <canvas id="goalieWinLossChart" width="400" height="250"></canvas>
                </div>
            </div>

            <!-- Saves vs Goals Against -->
            <div class="bg-gray-800/90 rounded-lg p-6 border border-gray-700">
                <h4 class="text-lg font-semibold text-white mb-4 text-center">🎯 Saves vs Goals Against</h4>
                <div class="relative h-64">
                    <canvas id="saveVsGoalsChart" width="400" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Goalie Table -->
        <div class="bg-gray-800/90 rounded-lg p-4 border border-gray-700">
            <h4 class="text-lg font-semibold text-white mb-4 text-center">📋 Detailed Goaltending Statistics</h4>
            <div class="overflow-x-auto">
                <table class='team-stats-table w-full' style='border: 2px solid <?php echo $teamColor2; ?>;'>
                    <colgroup>
                        <col class='goalies-combined-season'>
                        <col class='goalies-combined-name'>
                        <col class='goalies-combined-caphit'>
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
                            <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Cap Hit</th>
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
                            
                            // Format cap hit
                            $capHitRaw = $row['capHit'];
                            echo "<td class='border' style='border-color: $teamColor2'>" . $capHitRaw . "</td>";

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
        </div>
    </div>

    <script>
        // Initialize goalie charts when tab becomes active
        document.addEventListener('DOMContentLoaded', function() {
            const tab4Button = document.querySelector('[data-tab="tab4"]');
            if (tab4Button) {
                tab4Button.addEventListener('click', function() {
                    setTimeout(initializeGoalieCharts, 100);
                });
            }
        });
        
        function initializeGoalieCharts() {
            if (!document.getElementById('tab4').classList.contains('active')) return;
            if (document.getElementById('goalieWinLossChart').chart) return; // Already initialized
            
            // Win/Loss Pie Chart
            const winLossCtx = document.getElementById('goalieWinLossChart').getContext('2d');
            document.getElementById('goalieWinLossChart').chart = new Chart(winLossCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Wins', 'Losses', 'OT/SO Losses'],
                    datasets: [{
                        data: [<?php echo $totalWins; ?>, <?php echo $totalLosses; ?>, <?php echo $totalOTLosses; ?>],
                        backgroundColor: [
                            '#10B981', // Green for wins
                            '#EF4444', // Red for losses  
                            '#F59E0B'  // Orange for OT losses
                        ],
                        borderColor: '#374151',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: '#9CA3AF' }
                        }
                    }
                }
            });

            // Saves vs Goals Against Bar Chart
            const savesCtx = document.getElementById('saveVsGoalsChart').getContext('2d');
            document.getElementById('saveVsGoalsChart').chart = new Chart(savesCtx, {
                type: 'bar',
                data: {
                    labels: ['Saves', 'Goals Against'],
                    datasets: [{
                        data: [<?php echo $totalSaves; ?>, <?php echo $totalGoalsAgainst; ?>],
                        backgroundColor: [
                            hexToRgba(teamColors.primary, 0.8),
                            hexToRgba('#EF4444', 0.8)
                        ],
                        borderColor: [
                            teamColors.primary,
                            '#EF4444'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#374151' },
                            ticks: { color: '#9CA3AF' }
                        },
                        x: {
                            grid: { color: '#374151' },
                            ticks: { color: '#9CA3AF' }
                        }
                    }
                }
            });
        }
    </script>
</div>

<?php
echo "<div>";
$draftSQL = "SELECT * FROM draft_history WHERE teamID = $team_id";
$draftResult = mysqli_query($conn, $draftSQL);
?>


 <!-- TAB 5: DRAFT PICKS -->
<div class='season-tab-pane w-full flex justify-center' id='tab5'>
    <table class='team-stats-table w-full md:w-[98%]' style='border: 2px solid <?php echo $teamColor2; ?>;'>
        <colgroup>
            <col class='draft-year'>
            <col class='draft-round'>
            <col class='draft-pick-in-round'>
            <col class='draft-overall-pick'>
            <col class='draft-player-name'>
            <col class='draft-player-position'>
            <col class='draft-player-country'>
            <col class='draft-player-id'>
        </colgroup>
        <thead>
            <tr style="background: linear-gradient(90deg, <?php echo $teamColor1.'50'; ?> 0%, <?php echo $teamColor2.'50'; ?> 100%);">
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Year</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Round</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Pick In Round</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Overall Pick</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Player Name</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Position</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Country</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>ID</th>
            </tr>
        </thead>
        <tbody id='draftHistoryTable'>
            <?php
            $draftSQL = "SELECT * FROM draft_history WHERE teamID = $team_id";
            $draftResult = mysqli_query($conn, $draftSQL);
            
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
                echo "<td class='border' style='border-color: $teamColor2'>" . $draftRound . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $draftPickInRound . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $draftPickOvr . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $draftPlayerName . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . $draftPlayerPosition . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'>" . getFlagSVG($draftPlayerCountry) . "</td>";
                echo "<td class='border' style='border-color: $teamColor2'><a style='color:rgb(15, 63, 152)' href='player_details.php?player_id=" . $draftPlayerID . "'>" . $draftPlayerID . "</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- TAB 6: CURRENT PROSPECTS -->
<div class='season-tab-pane w-full flex justify-center' id='tab6'>
    <table class='team-stats-table w-full md:w-[98%]' style='border: 2px solid <?php echo $teamColor2; ?>;'>
        <colgroup>
            <col class='prospect-id'>
            <col class='prospect-name'>
            <col class='prospect-position'>
            <col class='prospect-age'>
            <col class='prospect-height'>
            <col class='prospect-weight'>
            <col class='prospect-country'>
        </colgroup>
        <thead>
            <tr style="background: linear-gradient(90deg, <?php echo $teamColor1.'50'; ?> 0%, <?php echo $teamColor2.'50'; ?> 100%);">
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Prospect ID</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Name</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Number</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Position</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Age</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Height</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Weight</th>
                <th class='border' style='border-color: <?php echo $teamColor2; ?>'>Country</th>
            </tr>
        </thead>
        <tbody id='prospectTable'>
            <?php
            $prospectSQL = "SELECT team_prospects.*, nhl_players.sweaterNumber, nhl_players.firstName, nhl_players.lastName, nhl_players.birthCountry,
            nhl_players.position, nhl_players.heightInInches, nhl_players.heightInCentimeters, nhl_players.weightInPounds, nhl_players.weightInKilograms,
            nhl_players.birthDate
            FROM team_prospects LEFT JOIN nhl_players ON team_prospects.prospect_id=nhl_players.playerId WHERE team_id = $team_id";
            $prospectResult = mysqli_query($conn, $prospectSQL);
            
            while ($row = mysqli_fetch_assoc($prospectResult)) {
                $prospectID = $row['prospect_id'];
                if ($prospectID == null || $prospectID == '') {
                    $prospectID = "-";
                }
                $firstName = $row['firstName'];
                $lastName = $row['lastName'];
                $number = $row['sweaterNumber'];
                if ($number == null || $number == '') {
                    $number = "-";
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
            ?>
        </tbody>
    </table>
</div>

<?php
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
            </script>

            <script>
                // Make season data available to JavaScript FIRST
                window.seasonData = <?php echo json_encode($seasonData); ?>;
                window.homeRoadData = <?php echo json_encode($homeRoadData); ?>;

                // Global function - define OUTSIDE of DOMContentLoaded so it's accessible
                function filterTableBySeason(seasonID) {
                    console.log("Filtering by season:", seasonID);

                    const baseSeasonID = seasonID.split('-')[0]; // "20242025-2" becomes "20242025"

                    // Get all table rows
                    const skaterRows = document.querySelectorAll('#skaterStatsTable tr');
                    const goalieRows = document.querySelectorAll('#goalieStatsTable tr');
                    const rosterRows = document.querySelectorAll('#seasonRosterTable tr');
                    const overallRows = document.querySelectorAll('#overallStatsTable tr');
                    const draftRows = document.querySelectorAll('#draftHistoryTable tr');
                    const standingsRows = document.querySelectorAll('#standingsTable tr');
                    const statsRows = document.querySelectorAll('#statsTable tr');

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

                    // Filter other table rows (same logic)
                    rosterRows.forEach(row => {
                        if (row.dataset.season === seasonID || row.dataset.season === baseSeasonID) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    overallRows.forEach(row => {
                        if (row.dataset.season === seasonID || row.dataset.season === baseSeasonID) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    draftRows.forEach(row => {
                        const draftYear = row.dataset.season;
                        const selectedYear = seasonID.substring(0, 4);
                        if (draftYear === selectedYear) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    standingsRows.forEach(row => {
                        if (row.dataset.season === seasonID) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    statsRows.forEach(row => {
                        if (row.dataset.season === seasonID) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // UPDATE DASHBOARDS - Add this here!
                    updateOverviewDashboard(seasonID);
                    
                    // Update home/road dashboard if function exists
                    if (typeof updateHomeRoadCharts === 'function') {
                        updateHomeRoadCharts(seasonID);
                    }

                    updateOverviewCharts(seasonID);
                }

                // Dashboard update function
                function updateOverviewDashboard(seasonId) {
                    console.log('Updating overview dashboard for season:', seasonId);
                    
                    if (!window.seasonData) {
                        console.log('No season data available');
                        return;
                    }
                    
                    const selectedSeason = window.seasonData.find(s => s.season === seasonId);
                    if (!selectedSeason) {
                        console.log('Season not found:', seasonId);
                        return;
                    }
                    
                    console.log('Found season data:', selectedSeason);
                    
                    // Update overview dashboard elements
                    const updates = [
                        { id: 'currentSeasonDisplay', value: selectedSeason.seasonDisplay },
                        { id: 'record', value: `${selectedSeason.wins}-${selectedSeason.losses}-${selectedSeason.otLosses}` },
                        { id: 'points', value: selectedSeason.points },
                        { id: 'pointPct', value: (selectedSeason.pointPct * 100).toFixed(1) + '%' },
                        { id: 'ppPct', value: selectedSeason.powerPlayPct.toFixed(2) + '%' },
                        { id: 'pkPct', value: selectedSeason.penaltyKillPct.toFixed(2) + '%' },
                        { id: 'regWins', value: selectedSeason.regWins },
                        { id: 'otWins', value: selectedSeason.regOtWins - selectedSeason.regWins },
                        { id: 'soWins', value: selectedSeason.soWins }
                    ];
                    
                    // Apply updates
                    updates.forEach(update => {
                        const element = document.getElementById(update.id);
                        if (element) {
                            element.textContent = update.value;
                            console.log(`Updated ${update.id} to ${update.value}`);
                        } else {
                            console.log(`Element not found: ${update.id}`);
                        }
                    });
                    
                    // Update goal differential with color
                    const goalDiff = selectedSeason.goalsFor - selectedSeason.goalsAgainst;
                    const goalDiffElement = document.getElementById('goalDiff');
                    if (goalDiffElement) {
                        goalDiffElement.textContent = (goalDiff >= 0 ? '+' : '') + goalDiff;
                        goalDiffElement.className = `text-2xl font-bold ${goalDiff >= 0 ? 'text-green-400' : 'text-red-400'}`;
                    }
                    
                    // Update progress bars
                    const ppBar = document.getElementById('ppBar');
                    if (ppBar) {
                        ppBar.style.width = selectedSeason.powerPlayPct + '%';
                    }
                    
                    const pkBar = document.getElementById('pkBar');
                    if (pkBar) {
                        pkBar.style.width = selectedSeason.penaltyKillPct + '%';
                    }
                }

                document.addEventListener('DOMContentLoaded', function () {
                    const dropdown = document.getElementById('seasonDropdown');

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

                    // Handle season selection change for skaters header
                    document.getElementById('seasonDropdown').addEventListener('change', function () {
                        const selectedSeason = this.value;
                        const seasonYear1 = selectedSeason.substring(0, 4);
                        const seasonYear2 = selectedSeason.substring(4, 8);

                        // Update the Skaters table header
                        const skatersHeader = document.getElementById('skatersHeader');
                        if (skatersHeader) {
                            skatersHeader.textContent = `Skaters ${seasonYear1}-${seasonYear2}`;
                        }
                    });

                    // Tab functionality
                    const tabButtons = document.querySelectorAll('.season-tab-button');
                    
                    tabButtons.forEach(button => {
                        button.addEventListener('click', function() {
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