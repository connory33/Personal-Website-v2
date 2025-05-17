<!doctype html>
<html lang="en" class="min-h-screen">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="NHL Playoff History and Brackets">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Playoff History</title>

    <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  <body class="flex flex-col" style='background-color: #343a40'>
    
    <!-- Header -->
    <?php include 'header.php'; ?>
    
    <!-- Main Content -->
    <main class="flex-grow text-white">
      <?php
      include('db_connection.php');

      ini_set('display_errors', 1);
      ini_set('display_startup_errors', 1);
      error_reporting(E_ALL);

      if (isset($_GET['season_id'])) {
          $season_id = $_GET['season_id'];
      } else {
          $season_id = '20232024'; // Default to most recent season if none specified
      }
      
      $currentSeason = $season_id;
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

      $seasons = array_reverse($seasons);
      ?>

      <div class="container mx-auto px-4 py-6">
        <h1 class="text-4xl font-bold text-center mt-4 mb-8 text-white drop-shadow-md">NHL Playoff Bracket</h1>
        
        <!-- Season navigation with dropdown -->
  <div class="season-selector w-full max-w-xs mx-auto">
    <label for="season-select" class="block text-sm font-medium">Change Season</label>
    <div class="relative">
      <select id="season-select" class="rounded cursor-pointer transition-colors w-full appearance-none pr-8">
        <?php
        // Generate options for last 25 seasons
        $current_year = date("Y");
        for ($i = 0; $i < 108; $i++) {
          $year = $current_year - $i;
          $option_season_id = ($year - 1) . $year;
          $option_display = ($year - 1) . "-" . $year;
          $selected = ($option_season_id == $season_id) ? 'selected' : '';
          echo "<option value='$option_season_id' $selected>$option_display</option>";
        }
        ?>
      </select>
      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="white" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
        </svg>
      </div>
    </div>
  </div>
</div>

        <hr class="border-2 border-slate-600 w-[90%] mb-8 mx-auto opacity-50">

        <!-- Bracket Content -->
        <div class="bracket-container">
          
          <?php
          // Improved SQL query - remove the GROUP BY to get all series
          $sql = "SELECT DISTINCT playoff_results.*, 
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
                  WHERE playoff_results.seasonID = '$season_id'";

          $result = mysqli_query($conn, $sql);
          if (!$result) {
              die("Query failed: " . mysqli_error($conn));
          }

          // Process results into rounds and deduplicate
          $rounds = [];
          $processedSeries = [];
          
          while ($row = mysqli_fetch_assoc($result)) {
              $seriesKey = $row['roundNums'] . '-' . $row['seriesLetters'];
              
              // Only add if we haven't seen this series before
              if (!isset($processedSeries[$seriesKey])) {
                  $rounds[$row['roundNums']][] = $row;
                  $processedSeries[$seriesKey] = true;
              }
          }
    
          // Define divisions by conference - include historical divisions
          $westDivisions = ['Pacific', 'Central', 'Northwest', 'Western', 'Smythe', 'Norris'];
          $eastDivisions = ['Atlantic', 'Metropolitan', 'Northeast', 'Southeast', 'Adams', 'Patrick'];
    
          // Check if we have data
          if (empty($rounds)) {
              echo "<div class='text-center text-lg py-10'>No playoff data available for the selected season.</div>";
          } else {
              // Start playoff grid container
              echo "<div class='playoff-grid-container'>";

              // Add conference labels 
              echo "<div class='conference-label west-label'>Western Conference</div>";
              echo "<div class='conference-label east-label'>Eastern Conference</div>";

              // Add round headers
              echo "<div class='round-header r1-west'>Round 1</div>";
              echo "<div class='round-header r2-west'>Round 2</div>";
              echo "<div class='round-header r3-west'>Conference Final</div>";
              echo "<div class='round-header r3-east'>Conference Final</div>";
              echo "<div class='round-header r2-east'>Round 2</div>";
              echo "<div class='round-header r1-east'>Round 1</div>";
              
              // Add Stanley Cup Final header
              echo "<div class='stanley-cup-header'>Stanley Cup Final</div>";

              // Process series by round
              
              // Track series by position for proper organization
              $westR1 = $eastR1 = $westR2 = $eastR2 = $westR3 = $eastR3 = $cupFinal = [];
              
              // First, sort all series into their proper buckets
              foreach ($rounds as $round => $series) {
                  foreach ($series as $match) {
                      // Get divisions
                      $topDiv = $match['topSeedTeamDivision'] ?? '';
                      $botDiv = $match['bottomSeedTeamDivision'] ?? '';
                      
                      // Determine conference
                      $isWest = (in_array($topDiv, $westDivisions) || in_array($botDiv, $westDivisions));
                      $isEast = (in_array($topDiv, $eastDivisions) || in_array($botDiv, $eastDivisions));
                      
                      // If we can't determine, go by count
                      if (!$isWest && !$isEast) {
                          if ($round == 1) {
                              $isWest = (count($westR1) < 4);
                              $isEast = !$isWest;
                          } elseif ($round == 2) {
                              $isWest = (count($westR2) < 2);
                              $isEast = !$isWest;
                          } elseif ($round == 3) {
                              $isWest = (count($westR3) < 1);
                              $isEast = !$isWest;
                          }
                      }
                      
                      // Sort into proper array
                      if ($round == 1) {
                          if ($isWest) $westR1[] = $match;
                          else if ($isEast) $eastR1[] = $match;
                      } else if ($round == 2) {
                          if ($isWest) $westR2[] = $match;
                          else if ($isEast) $eastR2[] = $match;
                      } else if ($round == 3) {
                          if ($isWest) $westR3[] = $match;
                          else if ($isEast) $eastR3[] = $match;
                      } else if ($round == 4) {
                          $cupFinal[] = $match;
                      }
                  }
              }
              
              // Now output series in the right order
              
              // Round 1 West
              $seriesLetters = ['A', 'B', 'C', 'D'];
              foreach ($westR1 as $i => $match) {
                  if ($i < 4) { // Limit to 4 matchups
                      outputSeriesBox($match, 'series' . $seriesLetters[$i]);
                  }
              }
              
              // Round 1 East
              $seriesLetters = ['E', 'F', 'G', 'H'];
              foreach ($eastR1 as $i => $match) {
                  if ($i < 4) { // Limit to 4 matchups
                      outputSeriesBox($match, 'series' . $seriesLetters[$i]);
                  }
              }
              
              // Round 2 West
              $seriesLetters = ['I', 'J'];
              foreach ($westR2 as $i => $match) {
                  if ($i < 2) { // Limit to 2 matchups
                      outputSeriesBox($match, 'series' . $seriesLetters[$i]);
                  }
              }
              
              // Round 2 East
              $seriesLetters = ['K', 'L'];
              foreach ($eastR2 as $i => $match) {
                  if ($i < 2) { // Limit to 2 matchups
                      outputSeriesBox($match, 'series' . $seriesLetters[$i]);
                  }
              }
              
              // Round 3 (Conference Finals)
              if (!empty($westR3)) {
                  outputSeriesBox($westR3[0], 'seriesM');
              }
              if (!empty($eastR3)) {
                  outputSeriesBox($eastR3[0], 'seriesN');
              }
              
              // Stanley Cup Final
              if (!empty($cupFinal)) {
                  $match = $cupFinal[0];
                  outputSeriesBox($match, 'seriesO');
                  
                  // Display champion if series is complete
                  $bottomWins = (int)$match['bottomSeedWins'];
                  $topWins = (int)$match['topSeedWins'];
                  
                  if ($bottomWins == 4 || $topWins == 4) {
                      $winnerName = $bottomWins > $topWins ? $match['bottomSeedTeamTriCode'] : $match['topSeedTeamTriCode'];
                      
                      echo "<div class='champion-container'>";
                      echo "<img src='../resources/images/stanley_cup.png' alt='Stanley Cup' class='champion-trophy'>";
                      echo "<div class='text-xl font-bold text-yellow-400 mt-2'>Champion: $winnerName</div>";
                      echo "</div>";
                  }
              }

              echo "</div>"; // End playoff grid container
          }
          
          // Helper function to output series box
          function outputSeriesBox($match, $gridClass) {
              $bottomWins = (int)$match['bottomSeedWins'];
              $topWins = (int)$match['topSeedWins'];
              $bottomBold = $bottomWins > $topWins ? 'font-bold text-green-500' : '';
              $topBold = $topWins > $bottomWins ? 'font-bold text-green-500' : '';
              $seriesId = $match['seasonID'] . $match['seriesLetters'];
              
              echo "<a href='series_details.php?series_id={$seriesId}' class='no-underline $gridClass'>";
              echo "<div class='series-box'>";
              
              // Top team
              echo "<div class='team'>";
              echo "<img class='team-logo' src='" . $match['bottomSeedTeamLogo'] . "' alt='" . $match['bottomSeedTeamTriCode'] . "'>";
              echo "<div class='team-info $bottomBold'>" . $match['bottomSeedTeamTriCode'] . " (" . $match['bottomSeedRankAbbrevs'] . ")</div>";
              echo "<div class='series-score'><span class='$bottomBold'>{$bottomWins}</span></div>";
              echo "</div>";
              
              // Center line
              echo "<hr class='border-slate-500 w-[90%] mx-auto my-2'>";
              
              // Bottom team
              echo "<div class='team'>";
              echo "<img class='team-logo' src='" . $match['topSeedTeamLogo'] . "' alt='" . $match['topSeedTeamTriCode'] . "'>";
              echo "<div class='team-info $topBold'>" . $match['topSeedTeamTriCode'] . " (" . $match['topSeedRankAbbrevs'] . ")</div>";
              echo "<div class='series-score'><span class='$topBold'>{$topWins}</span></div>";
              echo "</div>";
              
              echo "</div>"; // Close series box
              echo "</a>";
          }
          ?>
        </div>
      </div>
    </main>

    <!-- JavaScript for season selection -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Set up season selector dropdown
        const seasonSelect = document.getElementById('season-select');
        if (seasonSelect) {
          seasonSelect.addEventListener('change', function() {
            if (this.value) {
              window.location.href = 'playoff_results.php?season_id=' + this.value;
            }
          });
        }
      });
    </script>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
  </body>
</html>