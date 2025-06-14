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
    <style>
      /* Custom connecting lines */
      .connecting-lines {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
      }
      
      .line {
        position: absolute;
        background: linear-gradient(90deg, #64748b, #94a3b8, #64748b);
        border-radius: 1px;
      }
      
      .line-horizontal {
        height: 2px;
      }
      
      .line-vertical {
        width: 2px;
      }
    </style>
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
  </head>

  <body class="flex flex-col bg-gray-900">
    
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
          $season_id = '20232024';
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

      <!-- Page Header Section - matching homepage style -->
       <br>
        <div class="bg-gray-900/80 p-6 mb-10 rounded-lg border border-gray-700 max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-white tracking-tight mb-2">
                    Playoff Results
                    </h1>
                    <p class="text-gray-300 text-lg">
                        View historical NHL playoff brackets and series details
                    </p>
                </div>
                
                <!-- Season navigation with improved styling -->
        <div class="flex justify-center">
          <div class="bg-slate-800 rounded-lg p-6 shadow-xl border border-slate-600">
            <label for="season-select" class="block text-sm font-medium text-slate-300 mb-3">Change Season</label>
            <div class="relative">
              <select id="season-select" class="bg-slate-700 text-white border border-slate-500 rounded-lg px-4 py-3 pr-10 cursor-pointer hover:bg-slate-600 transition-colors w-64 appearance-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php
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
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </div>
            </div>
          </div>
        </div>
            </div>
        </div>

      <div class="container mx-auto px-4 py-8">

        <!-- Bracket Content with improved container -->
        <div class="bracket-container relative overflow-x-auto bg-gradient-to-br from-slate-800/50 to-slate-900/50 rounded-2xl p-8 shadow-2xl border border-slate-600/50 backdrop-blur-sm min-w-[1200px] mx-auto">
          
          <?php
          // Improved SQL query
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

          // Process results
          $rounds = [];
          $processedSeries = [];
          
          while ($row = mysqli_fetch_assoc($result)) {
              $seriesKey = $row['roundNums'] . '-' . $row['seriesLetters'];
              
              if (!isset($processedSeries[$seriesKey])) {
                  $rounds[$row['roundNums']][] = $row;
                  $processedSeries[$seriesKey] = true;
              }
          }
    
          $westDivisions = ['Pacific', 'Central', 'Northwest', 'Western', 'Smythe', 'Norris'];
          $eastDivisions = ['Atlantic', 'Metropolitan', 'Northeast', 'Southeast', 'Adams', 'Patrick'];
    
          if (empty($rounds)) {
              echo "<div class='text-center text-xl py-20 text-slate-300'>No playoff data available for the selected season.</div>";
          } else {
              // Start playoff grid with improved styling
              echo "<div class='relative min-w-[1200px] mx-auto'>";
              echo "<div class='playoff-grid grid grid-cols-7 gap-8 items-center justify-items-center relative'>";
              
              // Add connecting lines container
              echo "<div class='connecting-lines'></div>";

              // Conference labels with improved styling
              echo "<div class='col-span-3 text-center mb-8'>";
              echo "<h2 class='text-2xl font-bold text-blue-400 mb-2 drop-shadow-lg'>Western Conference</h2>";
              echo "<div class='h-1 bg-gradient-to-r from-transparent via-blue-400 to-transparent rounded-full'></div>";
              echo "</div>";

              echo "<div class='col-span-1 text-2xl'>" . $seasonYear1 . "-" . $seasonYear2 . "</div>"; // Center spacer

              echo "<div class='col-span-3 text-center mb-8'>";
              echo "<h2 class='text-2xl font-bold text-red-400 mb-2 drop-shadow-lg'>Eastern Conference</h2>";
              echo "<div class='h-1 bg-gradient-to-r from-transparent via-red-400 to-transparent rounded-full'></div>";
              echo "</div>";

              // Track series by position
              $westR1 = $eastR1 = $westR2 = $eastR2 = $westR3 = $eastR3 = $cupFinal = [];
              
              // Sort series into proper buckets
              foreach ($rounds as $round => $series) {
                  foreach ($series as $match) {
                      $topDiv = $match['topSeedTeamDivision'] ?? '';
                      $botDiv = $match['bottomSeedTeamDivision'] ?? '';
                      
                      $isWest = (in_array($topDiv, $westDivisions) || in_array($botDiv, $westDivisions));
                      $isEast = (in_array($topDiv, $eastDivisions) || in_array($botDiv, $eastDivisions));
                      
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
              
              // Round 1 West (Column 1)
              echo "<div class='space-y-6'>";
              echo "<h3 class='text-lg font-semibold text-center text-slate-300 mb-4 border-b border-slate-600 pb-2'>Round 1</h3>";
              foreach ($westR1 as $i => $match) {
                  if ($i < 4) {
                      outputSeriesBox($match, 'hover:scale-105 transition-transform duration-300');
                  }
              }
              echo "</div>";
              
              // Round 2 West (Column 2)
              echo "<div class='space-y-12'>";
              echo "<h3 class='text-lg font-semibold text-center text-slate-300 mb-4 border-b border-slate-600 pb-2'>Round 2</h3>";
              foreach ($westR2 as $i => $match) {
                  if ($i < 2) {
                      outputSeriesBox($match, 'hover:scale-105 transition-transform duration-300');
                  }
              }
              echo "</div>";
              
              // Conference Final West (Column 3)
              echo "<div class='flex flex-col items-center justify-center'>";
              echo "<h3 class='text-lg font-semibold text-center text-slate-300 mb-6 border-b border-slate-600 pb-2'>Conference Final</h3>";
              if (!empty($westR3)) {
                  outputSeriesBox($westR3[0], 'hover:scale-105 transition-transform duration-300 shadow-xl');
              }
              echo "</div>";
              
              // Stanley Cup Final (Column 4 - Center)
              echo "<div class='flex flex-col items-center justify-center py-8'>";
              echo "<h3 class='text-2xl font-bold text-center text-yellow-400 mb-6 border-b-2 border-yellow-400 pb-2 drop-shadow-lg'>Stanley Cup Final</h3>";
              if (!empty($cupFinal)) {
                  $match = $cupFinal[0];
                  outputSeriesBox($match, 'hover:scale-110 transition-transform duration-300 shadow-2xl border-2 border-yellow-400/50 rounded-xl bg-gradient-to-br from-yellow-900/20 to-yellow-800/20');

                  // Display champion
                  $bottomWins = (int)$match['bottomSeedWins'];
                  $topWins = (int)$match['topSeedWins'];
                  
                  if ($bottomWins == 4 || $topWins == 4) {
                      $winnerName = $bottomWins > $topWins ? $match['bottomSeedTeamTriCode'] : $match['topSeedTeamTriCode'];
                      $winnerLogo = $bottomWins > $topWins ? $match['bottomSeedTeamLogo'] : $match['topSeedTeamLogo'];
                      
                      echo "<div class='mt-8 text-center bg-gradient-to-r from-yellow-600/20 to-yellow-500/20 rounded-xl p-6 border border-yellow-400/30'>";
                      echo "<img src='../resources/images/stanley_cup.png' alt='Stanley Cup' class='w-20 h-auto mx-auto mb-4 drop-shadow-lg hover:scale-110 transition-transform duration-300'>";
                      echo "<div class='text-xl font-bold text-yellow-400 mb-2 drop-shadow-md'>Champion</div>";
                      echo "<div class='flex items-center justify-center space-x-3'>";
                      echo "<img src='$winnerLogo' alt='$winnerName' class='w-8 h-8'>";
                      echo "<span class='text-2xl font-bold text-white'>$winnerName</span>";
                      echo "</div>";
                      echo "</div>";
                  }
              }
              echo "</div>";
              
              // Conference Final East (Column 5)
              echo "<div class='flex flex-col items-center justify-center'>";
              echo "<h3 class='text-lg font-semibold text-center text-slate-300 mb-6 border-b border-slate-600 pb-2'>Conference Final</h3>";
              if (!empty($eastR3)) {
                  outputSeriesBox($eastR3[0], 'hover:scale-105 transition-transform duration-300 shadow-xl');
              }
              echo "</div>";
              
              // Round 2 East (Column 6)
              echo "<div class='space-y-12'>";
              echo "<h3 class='text-lg font-semibold text-center text-slate-300 mb-4 border-b border-slate-600 pb-2'>Round 2</h3>";
              foreach ($eastR2 as $i => $match) {
                  if ($i < 2) {
                      outputSeriesBox($match, 'hover:scale-105 transition-transform duration-300');
                  }
              }
              echo "</div>";
              
              // Round 1 East (Column 7)
              echo "<div class='space-y-6'>";
              echo "<h3 class='text-lg font-semibold text-center text-slate-300 mb-4 border-b border-slate-600 pb-2'>Round 1</h3>";
              foreach ($eastR1 as $i => $match) {
                  if ($i < 4) {
                      outputSeriesBox($match, 'hover:scale-105 transition-transform duration-300');
                  }
              }
              echo "</div>";

              echo "</div>"; // End grid
              echo "</div>"; // End container
          }
          
          // Enhanced series box function
          function outputSeriesBox($match, $additionalClasses = '') {
              $bottomWins = (int)$match['bottomSeedWins'];
              $topWins = (int)$match['topSeedWins'];
              $bottomBold = $bottomWins > $topWins ? 'font-bold text-green-400' : 'text-slate-300';
              $topBold = $topWins > $bottomWins ? 'font-bold text-green-400' : 'text-slate-300';
              $seriesId = $match['seasonID'] . $match['seriesLetters'];
              
              echo "<a href='series_details.php?series_id={$seriesId}' class='block no-underline $additionalClasses'>";
              echo "<div class='bg-slate-800/80 backdrop-blur-sm border border-slate-600/50 rounded-xl p-4 w-48 shadow-lg hover:shadow-xl hover:border-slate-500 transition-all duration-300'>";
              
              // Top team
              echo "<div class='flex items-center justify-between mb-3'>";
              echo "<div class='flex items-center space-x-3 flex-1'>";
              echo "<img class='w-8 h-8 object-contain filter drop-shadow-sm' src='" . $match['bottomSeedTeamLogo'] . "' alt='" . $match['bottomSeedTeamTriCode'] . "'>";
              echo "<div class='flex flex-col'>";
              echo "<span class='$bottomBold text-sm font-medium'>" . $match['bottomSeedTeamTriCode'] . "</span>";
              echo "<span class='text-xs text-slate-400'>(" . $match['bottomSeedRankAbbrevs'] . ")</span>";
              echo "</div>";
              echo "</div>";
              echo "<div class='text-xl font-bold $bottomBold min-w-[24px] text-center'>{$bottomWins}</div>";
              echo "</div>";
              
              // Divider with gradient
              echo "<div class='h-px bg-gradient-to-r from-transparent via-slate-500 to-transparent my-3'></div>";
              
              // Bottom team
              echo "<div class='flex items-center justify-between'>";
              echo "<div class='flex items-center space-x-3 flex-1'>";
              echo "<img class='w-8 h-8 object-contain filter drop-shadow-sm' src='" . $match['topSeedTeamLogo'] . "' alt='" . $match['topSeedTeamTriCode'] . "'>";
              echo "<div class='flex flex-col'>";
              echo "<span class='$topBold text-sm font-medium'>" . $match['topSeedTeamTriCode'] . "</span>";
              echo "<span class='text-xs text-slate-400'>(" . $match['topSeedRankAbbrevs'] . ")</span>";
              echo "</div>";
              echo "</div>";
              echo "<div class='text-xl font-bold $topBold min-w-[24px] text-center'>{$topWins}</div>";
              echo "</div>";
              
              echo "</div>"; // Close series box
              echo "</a>";
          }
          ?>
        </div>
      </div>
      <p class='text-center text-sm text-nhl-muted mt-2 mb-8'>Click any series to view detailed results and game logs.</p>
    </main>

    <!-- Enhanced JavaScript -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Season selector
        const seasonSelect = document.getElementById('season-select');
        if (seasonSelect) {
          seasonSelect.addEventListener('change', function() {
            if (this.value) {
              window.location.href = 'playoff_results.php?season_id=' + this.value;
            }
          });
        }
        
        // Add connecting lines between rounds
        function drawConnectingLines() {
          const grid = document.querySelector('.playoff-grid');
          if (!grid) return;
          
          const linesContainer = document.querySelector('.connecting-lines');
          if (!linesContainer) return;
          
          // Clear existing lines
          linesContainer.innerHTML = '';
          
          // Get all series boxes
          const seriesBoxes = grid.querySelectorAll('a[href*="series_details"]');
          
          // Create connecting lines between rounds
          // This is a simplified version - you'd need more complex logic for a full bracket
          seriesBoxes.forEach((box, index) => {
            if (index < seriesBoxes.length - 1) {
              const line = document.createElement('div');
              line.className = 'absolute bg-slate-500 opacity-30';
              line.style.height = '2px';
              line.style.width = '20px';
              // Position would need to be calculated based on box positions
              linesContainer.appendChild(line);
            }
          });
        }
        
        // Draw lines after content loads
        setTimeout(drawConnectingLines, 100);
        
        // Redraw on window resize
        window.addEventListener('resize', drawConnectingLines);
      });
    </script>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
  </body>
</html>