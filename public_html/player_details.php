<?php include('db_connection.php'); ?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Player Details</title>

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

    <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />



  </head>
    <body class="bg-nhl-dark text-nhl-text font-sans antialiased">
  <!-- Header -->
  <?php include 'header.php'; ?>

    <div class="py-8 px-4 animate-fade-in">
    <div class="max-w-[1770px] mx-auto">
    <div class="text-white">
      <div style='margin-left: 10px; margin-right: 10px'>
        <div class='flex justify-between'>
        <div>
            <h1 class="text-4xl font-bold text-white mt-4 tracking-tight">
                Player Details
            </h1>
        </div>
        
        <!-- Search Form Section (like Season Selector in draft) -->
        <div class="max-w-xs mb-8">
            <form id="nhl-search" method="GET" action="nhl_players.php" class="relative">
                <input type="hidden" name="search_column" value="player">
                <input 
                    id="search-term" 
                    name="search_term" 
                    type="text" 
                    placeholder="Enter player name" 
                    value="<?= htmlspecialchars($search_term) ?>"
                    class="block w-full rounded-lg border-0 py-3 pl-4 pr-10 bg-nhl-medium text-white shadow-sm ring-1 ring-inset ring-nhl-accent/30 focus:ring-2 focus:ring-nhl-accent text-lg font-medium"
                >
                <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-3 text-nhl-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </button>
                
                <!-- Keep any existing filter parameters when searching -->
                <?php if (!empty($filter_name)): ?>
                    <input type="hidden" name="filter_name" value="<?= htmlspecialchars($filter_name) ?>">
                <?php endif; ?>
                <?php if (!empty($filter_team)): ?>
                    <input type="hidden" name="filter_team" value="<?= htmlspecialchars($filter_team) ?>">
                <?php endif; ?>
                <?php if (!empty($filter_hand)): ?>
                    <input type="hidden" name="filter_hand" value="<?= htmlspecialchars($filter_hand) ?>">
                <?php endif; ?>
                <?php if (!empty($filter_country)): ?>
                    <input type="hidden" name="filter_country" value="<?= htmlspecialchars($filter_country) ?>">
                <?php endif; ?>
                <?php if (!empty($filter_status)): ?>
                    <input type="hidden" name="filter_status" value="<?= htmlspecialchars($filter_status) ?>">
                <?php endif; ?>
                <?php if (!empty($filter_number)): ?>
                    <input type="hidden" name="filter_number" value="<?= htmlspecialchars($filter_number) ?>">
                <?php endif; ?>
                <?php if (!empty($filter_weight_min)): ?>
                    <input type="hidden" name="filter_weight_min" value="<?= htmlspecialchars($filter_weight_min) ?>">
                <?php endif; ?>
                <?php if (!empty($filter_weight_max)): ?>
                    <input type="hidden" name="filter_weight_max" value="<?= htmlspecialchars($filter_weight_max) ?>">
                <?php endif; ?>
            </form>
            <div class="text-right mt-1 mr-1">
    <a href="nhl_games.php" class="text-sm text-nhl-accent hover:text-nhl-accent/80">Search games instead</a>
</div>

        </div>
        </div> <!-- End flex justify-between -->
                </div>
                </div>
                </div>
        <?php

          ini_set('display_errors', 1);
          ini_set('display_startup_errors', 1);
          error_reporting(E_ALL);


                                                              ########## GET ALL PLAYER DATA TO USE FOR THE PAGE ##########

          // Check if the 'game_id' is passed in the URL
          if (isset($_GET['player_id'])) {
              $player_id = $_GET['player_id'];

              $sql = "SELECT nhl_players.*, nhl_teams.teamLogo, nhl_teams.fullName AS fullTeamName,
                      nhl_contracts.*
                      FROM nhl_players
                      LEFT JOIN nhl_teams ON nhl_players.currentTeamId = nhl_teams.id
                      LEFT JOIN nhl_contracts ON nhl_players.playerID = nhl_contracts.playerId
                      WHERE nhl_players.playerID=$player_id";
              $playerInfo = mysqli_query($conn, $sql);

            ##### Iterate through all player results and assign data to variables #####
            while ($row = mysqli_fetch_assoc($playerInfo)) {
              ### Basic Info ###
                $name = $row['firstName'] . ' ' . $row['lastName'];
                $sweaterNumber = $row['sweaterNumber'];
                $position = $row['position'];
                $headshot = $row['headshot'];
                $isActiveRaw = strtolower(trim($row['isActive']));
                if ($isActiveRaw === 'false') {
                    $active = "No";
                } else {
                    $active = "Yes";
                }

                if ($active == "Yes") {
                  $teamName = $row['fullTeamName'];
                  $teamLogo = $row['teamLogo'];
                  $teamID = $row['currentTeamId'];
                } else {
                  $teamName = 'N/A';
                  $teamLogo = 'N/A';
                  $teamID = 'N/A';
                } 
                
                $badgesLogos = $row['badgesLogos'];
                $badgesNames = $row['badgesNames'];
                $heroImage = $row['heroImage'];

                if ($row['heightInInches']) {
                  $heightIn = $row['heightInInches'];
                  $heightFt = floor($heightIn/12);
                  $heightInches = ($heightIn % 12);
                  $heightIn = $heightFt . "' " . $heightInches . '"';
                } else {
                  $heightIn = '?';
                }
                if ($row['heightInCentimeters']) {
                  $heightCm = $row['heightInCentimeters'];
                } else {
                  $heightCm = intval((int)$heightIn*2.54);
                }
                if ($row['weightInPounds']) {
                  $weightLb = $row['weightInPounds'];
                } else {
                  $weightLb = '?';
                }
                if ($row['weightInKilograms']) {
                  $weightKg = $row['weightInKilograms'];
                } else {
                  $weightKg = '?';
                }
                
                $birthDate = date('F j, Y',strtotime($row['birthDate']));
                $birthCity = $row['birthCity'];
                $birthStateProvince = $row['birthStateProvince'];
                $birthCountry = $row['birthCountry'];
                $shootsCatches = $row['shootsCatches'];
                if ($row['draftYear'] == '') {
                  $draftYear = 'N/A';
                  $draftTeam = 'N/A';
                  $draftRound = 'N/A';
                  $draftPickInRound = 'N/A';
                  $draftOverall = 'N/A';
                } else {
                  $draftYear = $row['draftYear'];
                  $draftTeam = $row['draftTeam'];
                  $draftRound = $row['draftRound'];
                  $draftPickInRound = $row['draftPickInRound'];
                  $draftOverall = $row['draftOverall'];
                }

                $contractSignedDate = $row['Signed Date'];
                $contractStartSeason = $row['Start Season'];
                $contractEndSeason = $row['End Season'];
                $contractLength = $row['Years'];
                $contractValue = $row['Total Value'];
                $capHit = $row['capHit'];
                $signingBonus = $row['Signing Bonus'];
                $baseSalary = $row['Base Salary'];
                $performanceBonus = $row['Performance Bonus'];
                $contractTerms = $row['Terms'];


                # checks if a value (i.e. # of assists) is an empty string and changes to 0 if so
                if (!function_exists('fillEmptyStats')) {
                  function fillEmptyStats($value) {
                      return ($value === null || $value === '') ? '-' : $value;
                  }
              }

              ### Featured Season Stats ###
                $featuredSeason = $row['featuredSeason'];
                $featuredSeasonGP = fillEmptyStats($row['featuredSeasonGP']);
                $featuredSeasonAssists = fillEmptyStats($row['featuredSeasonAssists']);
                $featuredSeasonGWG = fillEmptyStats($row['featuredSeasonGWG']);
                $featuredSeasonGoals = fillEmptyStats($row['featuredSeasonGoals']);
                $featuredSeasonOTGoals = fillEmptyStats($row['featuredSeasonOTGoals']);
                $featuredSeasonPIM = fillEmptyStats($row['featuredSeasonPIM']);
                $featuredSeasonPlusMinus = fillEmptyStats($row['featuredSeasonPlusMinus']);
                $featuredSeasonPts = fillEmptyStats($row['featuredSeasonPts']);
                $featuredSeasonPPG = fillEmptyStats($row['featuredSeasonPPG']);
                $featuredSeasonPPPoints = fillEmptyStats($row['featuredSeasonPPPoints']);
                $featuredSeasonShootingPct = fillEmptyStats($row['featuredSeasonShootingPct']);
                $featuredSeasonSHG = fillEmptyStats($row['featuredSeasonSHG']);
                $featuredSeasonSHPts = fillEmptyStats($row['featuredSeasonSHPts']);
                $featuredSeasonShots = fillEmptyStats($row['featuredSeasonShots']);
                  # Goalie
                $featuredSeasonGAA = fillEmptyStats($row['featuredSeasonGAA']);
                $featuredSeasonLosses = fillEmptyStats($row['featuredSeasonLosses']);
                $featuredSeasonSO = fillEmptyStats($row['featuredSeasonSO']);
                $featuredSeasonTies = fillEmptyStats($row['featuredSeasonTies']);
                $featuredSeasonWins = fillEmptyStats($row['featuredSeasonWins']);
                $featuredSeasonGS = fillEmptyStats($row['featuredSeasonGS']);
                $featuredSeasonGA = fillEmptyStats($row['featuredSeasonGA']);
                $featuredSeasonSavePct = fillEmptyStats($row['featuredSeasonSavePct']);
                $featuredSeasonOTLosses = fillEmptyStats($row['featuredSeasonOTLosses']);
                $featuredSeasonShotsAgainst = fillEmptyStats($row['featuredSeasonShotsAgainst']);
                
              ### Regular Season Stats ###
                $regSeasonCareerGP = fillEmptyStats($row['regSeasonCareerGP']);
                $regSeasonCareerAssists = fillEmptyStats($row['regSeasonCareerAssists']);
                $regSeasonCareerGWG = fillEmptyStats($row['regSeasonCareerGWG']);
                $regSeasonCareerGoals = fillEmptyStats($row['regSeasonCareerGoals']);
                $regSeasonCareerOTGoals = fillEmptyStats($row['regSeasonCareerOTGoals']);
                $regSeasonCareerPIM = fillEmptyStats($row['regSeasonCareerPIM']);
                $regSeasonCareerPlusMinus = fillEmptyStats($row['regSeasonCareerPlusMinus']);
                $regSeasonCareerPts = fillEmptyStats($row['regSeasonCareerPts']);
                $regSeasonCareerPPG = fillEmptyStats($row['regSeasonCareerPPG']);
                $regSeasonCareerPPPoints = fillEmptyStats($row['regSeasonCareerPPPoints']);
                $regSeasonCareerShootingPct = fillEmptyStats($row['regSeasonCareerShootingPct']);
                $regSeasonCareerSHG = fillEmptyStats($row['regSeasonCareerSHG']);
                $regSeasonCareerSHPts = fillEmptyStats($row['regSeasonCareerSHPts']);
                $regSeasonCareerShots = fillEmptyStats($row['regSeasonCareerShots']);
                  # Goalie
                $regSeasonCareerGAA = fillEmptyStats($row['regSeasonCareerGAA']);
                $regSeasonCareerLosses = fillEmptyStats($row['regSeasonCareerLosses']);
                $regSeasonCareerSO = fillEmptyStats($row['regSeasonCareerSO']);
                $regSeasonCareerTies = fillEmptyStats($row['regSeasonCareerTies']);
                $regSeasonCareerWins = fillEmptyStats($row['regSeasonCareerWins']);
                $regSeasonCareerGS = fillEmptyStats($row['regSeasonCareerGS']);
                $regSeasonCareerGA = fillEmptyStats($row['regSeasonCareerGA']);
                $regSeasonCareerSavePct = fillEmptyStats($row['regSeasonCareerSavePct']);
                $regSeasonCareerOTLosses = fillEmptyStats($row['regSeasonCareerOTLosses']);
                $regSeasonCareerShotsAgainst = fillEmptyStats($row['regSeasonCareerShotsAgainst']);

              ### Playoff Stats ###
                $playoffsCareerAssists = fillEmptyStats($row['playoffsCareerAssists']);
                $playoffsCareerGP = fillEmptyStats($row['playoffsCareerGP']);
                $playoffsCareerAssists = fillEmptyStats($row['playoffsCareerAssists']);
                $playoffsCareerGWG = fillEmptyStats($row['playoffsCareerGWG']);
                $playoffsCareerGoals = fillEmptyStats($row['playoffsCareerGoals']);
                $playoffsCareerOTGoals = fillEmptyStats($row['playoffsCareerOTGoals']);
                $playoffsCareerPIM = fillEmptyStats($row['playoffsCareerPIM']);
                $playoffsCareerPlusMinus = fillEmptyStats($row['playoffsCareerPlusMinus']);
                $playoffsCareerPts = fillEmptyStats($row['playoffsCareerPts']);
                $playoffsCareerPPG = fillEmptyStats($row['playoffsCareerPPG']);
                $playoffsCareerPPPoints = fillEmptyStats($row['playoffsCareerPPPoints']);
                $playoffsCareerShootingPct = fillEmptyStats($row['playoffsCareerShootingPct']);
                $playoffsCareerSHG = fillEmptyStats($row['playoffsCareerSHG']);
                $playoffsCareerSHPts = fillEmptyStats($row['playoffsCareerSHPts']);
                $playoffsCareerShots = fillEmptyStats($row['playoffsCareerShots']);
                  # Goalie
                $playoffsCareerGAA = fillEmptyStats($row['playoffsCareerGAA']);
                $playoffsCareerLosses = fillEmptyStats($row['playoffsCareerLosses']);
                $playoffsCareerSO = fillEmptyStats($row['playoffsCareerSO']);
                $playoffsCareerTies = fillEmptyStats($row['playoffsCareerTies']);
                $playoffsCareerWins = fillEmptyStats($row['playoffsCareerWins']);
                $playoffsCareerGS = fillEmptyStats($row['playoffsCareerGS']);
                $playoffsCareerGA = fillEmptyStats($row['playoffsCareerGA']);
                $playoffsCareerSavePct = fillEmptyStats($row['playoffsCareerSavePct']);
                $playoffsCareerOTLosses = fillEmptyStats($row['playoffsCareerOTLosses']);
                $playoffsCareerShotsAgainst = fillEmptyStats($row['playoffsCareerShotsAgainst']);
                

                // $last5Games = $row['last5Games'];
                // $seasonTotals = $row['seasonTotals'];
                $awardNames = $row['awardNames'];
                $awardSeasons = $row['awardSeasons'];
                $currentTeamRoster = $row['currentTeamRoster'];
          }

            $last5GameSQL = "SELECT 
                  player_last_5_games.*,
                  skaters_gamebygame_stats.goals AS skater_goals,
                  skaters_gamebygame_stats.assists AS skater_assists,
                  skaters_gamebygame_stats.points AS skater_points,
                  skaters_gamebygame_stats.plusMinus AS skater_plusMinus,
                  skaters_gamebygame_stats.pim AS skater_pim,
                  skaters_gamebygame_stats.hits AS skater_hits,
                  skaters_gamebygame_stats.sog AS skater_sog,
                  skaters_gamebygame_stats.powerPlayGoals AS skater_ppg,
                  skaters_gamebygame_stats.faceoffWinningPctg AS skater_faceoffWinningPctg,
                  skaters_gamebygame_stats.toi AS skater_toi,
                  skaters_gamebygame_stats.blockedShots AS skater_blockedShots,
                  skaters_gamebygame_stats.shifts AS skater_shifts,
                  skaters_gamebygame_stats.giveaways AS skater_giveaways,
                  skaters_gamebygame_stats.takeaways AS skater_takeaways,
                  goalies_gamebygame_stats.pim AS goalie_pim,
                  goalies_gamebygame_stats.evenStrengthShotsAgainst AS evenStrengthSA,
                  goalies_gamebygame_stats.powerPlayShotsAgainst AS powerPlaySA,
                  goalies_gamebygame_stats.shorthandedShotsAgainst AS shorthandedSA,
                  goalies_gamebygame_stats.saveShotsAgainst AS saveSA,
                  goalies_gamebygame_stats.savePctg AS savePctg,
                  goalies_gamebygame_stats.evenStrengthGoalsAgainst AS evenStrengthGA,
                  goalies_gamebygame_stats.powerPlayGoalsAgainst AS powerPlayGA,
                  goalies_gamebygame_stats.shorthandedGoalsAgainst AS shorthandedGA,
                  goalies_gamebygame_stats.goalsAgainst AS goalsAgainst,
                  goalies_gamebygame_stats.starter AS starter,
                  goalies_gamebygame_stats.shotsAgainst AS shotsAgainst,
                  goalies_gamebygame_stats.saves AS saves

              FROM player_last_5_games
              LEFT JOIN skaters_gamebygame_stats 
                  ON player_last_5_games.playerId = skaters_gamebygame_stats.playerId
                  AND player_last_5_games.game_id = skaters_gamebygame_stats.gameID
              LEFT JOIN goalies_gamebygame_stats 
                  ON player_last_5_games.playerId = goalies_gamebygame_stats.playerId
              WHERE player_last_5_games.playerId = '$player_id' AND game_id != 0";

            $last5GameInfo = mysqli_query($conn, $last5GameSQL);

            
            // echo $last5GameSQL;
          echo "<br>";
        ?>


                                                                       <!-- ########## DISPLAY ALL RESULTS ########## -->


                  <!-- Player Header Section -->
          <div class="player-header-container max-w-7xl">
            <!-- Left side: Name and status -->
            <div class="player-header-info">
              <?php if ($sweaterNumber != '') {
                echo "<h1 class='player-name'>" . $name . " #" . $sweaterNumber . "</h1>";
              } else {
                echo "<h1 class='player-name'>" . $name . "</h1>";
              } ?>
              
              
              <?php if ($active == 'Yes') { ?>
                <p class="text-[1.25rem] font-medium text-emerald-500 mt-2 bg-emerald-500/15 px-4 py-1 rounded border border-emerald-500/30">Active - <?php echo $player_id; ?></p>
              <?php } else { ?>
                <p class="text-[1.25rem] font-medium text-red-500 mt-2 bg-red-500/15 px-4 py-1 rounded border border-red-500/30">Not Active - <?php echo $player_id; ?></p>
              <?php } ?>
            </div>
            
            <!-- Right side: Images -->
            <div class="player-images">
              <?php if ($badgesLogos != 'false' && $badgesLogos != '') { ?>
                <img src="<?php echo htmlspecialchars($badgesLogos); ?>" alt="badge logo" style="height: 110px;">
              <?php } ?>
              
              <?php if ($headshot != 'false' && $headshot != '' && $headshot != 'N/A') { ?>
                <img src="<?php echo htmlspecialchars($headshot); ?>" alt="headshot" style="height: 120px; border-radius: 8px; box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);">
              <?php } ?>
              
              <?php if ($teamLogo != 'false' && $teamLogo != '' && $teamLogo != 'N/A') { ?>
                <div class='transition-transform duration-300 ease-in-out hover:scale-105'>
                <a href="https://connoryoung.com/team_details.php?team_id=<?php echo htmlspecialchars($teamID); ?>">
                  <img src="<?php echo htmlspecialchars($teamLogo); ?>" alt="team logo" style="height: 120px;">
                </a>
                </div>
              <?php } ?>
            </div>
          </div>
                <br>
          <?php
              // echo "<p>Current Team: " . $teamName . " <img src='" . htmlspecialchars($teamLogo) . "' alt='N/A' style='height: 65px;'></p>";
          
// Begin improved layout container
echo "<div class='player-profile-container mx-auto max-w-7xl'>";

// Top section with hero image and contract in a side-by-side grid
echo "<div class='grid grid-cols-1 lg:grid-cols-5 gap-6 mb-8'>";

// Hero image takes up 3/5 of the space on larger screens
echo "<div class='lg:col-span-3 flex items-center justify-center'>";
echo "<img src='" . htmlspecialchars($heroImage) . "' alt='heroImage' class='w-full max-h-[500px] object-cover object-center rounded-lg shadow-lg border border-[#45A29E]'>";
echo "</div>";

// Contract card takes up 2/5 of the space on larger screens
echo "<div class='lg:col-span-2'>";
if ($contractSignedDate != '') {
    echo '<div class="contract-card w-full h-full rounded-lg overflow-hidden shadow-xl border border-[#45A29E] flex flex-col">';

    $century = substr($contractSignedDate,-4,2);
    $contractStartSeasonStart = substr($contractStartSeason,0,2);
    $contractStartSeasonEnd = substr($contractStartSeason,3,2);
    $contractStartSeason = $century . $contractStartSeasonStart . "-" . $century . $contractStartSeasonEnd;
    $endSeasonEnd = (int)$contractEndSeason;
    $endSeasonStart = $endSeasonEnd - 1;
    $contractEndSeason = $endSeasonStart . "-" . $endSeasonEnd;

    function tooltipSpan($label, $description) {
        return "<span class='relative group cursor-help'>
          <span>$label</span>
          <span class='absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-max max-w-xs bg-gray-800 text-white text-sm rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10 whitespace-nowrap'>
            $description
          </span>
        </span>";
    }

    // Contract Card Header
    echo '<div class="contract-header bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white p-4 flex justify-between items-center">';
    echo '<h3 class="text-xl font-bold">Current Contract</h3>';
    echo '<span class="text-sm bg-[#343a40] py-1 px-3 rounded-full">' . $contractStartSeason . '  -  ' . $contractEndSeason . '</span>';
    echo '</div>';
    
    // Contract Summary Section
    echo '<div class="bg-[#202428] p-5 shadow-inner border-b border-gray-700">';
    echo '<div class="grid grid-cols-3 gap-4 text-center">';
    
    // Cap Hit Display
    echo '<div class="bg-[#2c3237] p-3 rounded-lg shadow-sm hover:bg-[#343a40]">';
    echo '<div class="text-sm text-gray-400 font-medium">' . tooltipspan("Cap Hit", "Amount of team salary cap used this season") . '</div>';
    echo '<div class="text-xl font-bold text-white">' . $capHit . '</div>';
    echo '</div>';
    
    // Contract Length Display
    echo '<div class="bg-[#2c3237] p-3 rounded-lg shadow-sm hover:bg-[#343a40]">';
    echo '<div class="text-sm text-gray-400 font-medium">Term Length</div>';
    echo '<div class="text-xl font-bold text-white">' . $contractLength . ' Years</div>';
    echo '</div>';
    
    // Total Value Display
    echo '<div class="bg-[#2c3237] p-3 rounded-lg shadow-sm hover:bg-[#343a40]">';
    echo '<div class="text-sm text-gray-400 font-medium">Total Value</div>';
    echo '<div class="text-xl font-bold text-white">' . $contractValue . '</div>';
    echo '</div>';
    
    echo '</div>'; // Close grid
    echo '</div>'; // Close summary section
    
    // Contract Details Section - with flex-grow to fill the space
    echo '<div class="bg-[#292e33] p-5 shadow-md text-white flex-grow overflow-y-auto">';
    
    // Two column layout for details
    echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
    
    // Left Column - Dates and Signing Info
    echo '<div>';
    echo '<h4 class="text-lg font-semibold text-white mb-3 border-b border-gray-700 pb-2">Contract Timeline</h4>';
    
    echo '<div class="flex justify-between mb-2">';
    echo '<span class="text-gray-400">Signed Date:</span>';
    echo '<span class="font-medium text-gray-200">' . $contractSignedDate . '</span>';
    echo '</div>';
    
    echo '<div class="flex justify-between mb-2">';
    echo '<span class="text-gray-400">Start Season:</span>';
    echo '<span class="font-medium text-gray-200">' . $contractStartSeason . '</span>';
    echo '</div>';
    
    echo '<div class="flex justify-between mb-2">';
    echo '<span class="text-gray-400">End Season:</span>';
    echo '<span class="font-medium text-gray-200">' . $contractEndSeason . '</span>';
    echo '</div>';
    
    echo '</div>'; // Close left column
    
    // Right Column - Financial Breakdown
    echo '<div>';
    echo '<h4 class="text-lg font-semibold text-white mb-3 border-b border-gray-700 pb-2">Financial Breakdown</h4>';
    
    echo '<div class="flex justify-between mb-2">';
    echo '<span class="text-gray-400">Base Salary:</span>';
    echo '<span class="font-medium text-gray-200">' . $baseSalary . '</span>';
    echo '</div>';
    
    if ($signingBonus != '' && $signingBonus != '0') {
        echo '<div class="flex justify-between mb-2">';
        echo '<span class="text-gray-400">Signing Bonus:</span>';
        echo '<span class="font-medium text-gray-200">' . $signingBonus . '</span>';
        echo '</div>';
    } else {
        echo '<div class="flex justify-between mb-2">';
        echo '<span class="text-gray-400">Signing Bonus:</span>';
        echo '<span class="font-medium text-gray-200">N/A</span>';
        echo '</div>';
    }
    
    if ($performanceBonus != '' && $performanceBonus != '0') {
        echo '<div class="flex justify-between mb-2">';
        echo '<span class="text-gray-400">Performance Bonus:</span>';
        echo '<span class="font-medium text-gray-200">' . $performanceBonus . '</span>';
        echo '</div>';
    } else {
        echo '<div class="flex justify-between mb-2">';
        echo '<span class="text-gray-400">Performance Bonus:</span>';
        echo '<span class="font-medium text-gray-200">N/A</span>';
        echo '</div>';
    }
    
    echo '</div>'; // Close right column
    
    echo '</div>'; // Close grid
    
    // Contract Terms (if available)
    if ($contractTerms != '') {
        echo '<div class="mt-5 pt-4 border-t border-gray-700">';
        echo '<h4 class="text-lg font-semibold text-white mb-2">Contract Terms</h4>';
        echo '<p class="text-gray-300">' . $contractTerms . '</p>';
        echo '</div>';
    } else {
        echo '<div class="mt-5 pt-4 border-t border-gray-700">';
        echo '<h4 class="text-lg font-semibold text-white mb-2">Contract Terms</h4>';
        echo '<p class="text-gray-300">No special terms or conditions.</p>';
        echo '</div>';
    }
    
    echo '</div>'; // Close details section
    
    echo '</div>'; // Close contract card
} else {
    // No contract information available
    echo '<div class="bg-[#292e33] rounded-lg p-6 text-center mx-auto border border-[#45A29E] h-full flex flex-col justify-center">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
    echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />';
    echo '</svg>';
    echo '<h3 class="text-xl font-semibold text-gray-200">No Active Contract</h3>';
    echo '<p class="text-gray-400 mt-2">This player does not currently have an active contract on record.</p>';
    echo '</div>';
}
echo "</div>"; // close contract container column
echo "</div>"; // close top grid section

// Bio box section (full width)
echo "<div class='bio-box w-full rounded-lg overflow-hidden shadow-xl border border-[#45A29E] mb-8'>";
echo "<div class='bio-header bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white px-4 py-3 flex justify-between items-center'>";
echo "<span class='text-lg font-bold'>Player Bio</span>";
if ($position == 'G') {
    $position = 'Goalie';
} else if ($position == 'D') {
    $position = 'Defense';
} else if ($position == 'C') {
    $position = 'Center';
} else if ($position == 'R') {
    $position = 'Right Wing';
} else if ($position == 'L') {
    $position = 'Left Wing';
}
echo "<span class='player-position bg-[#343a40] py-1 px-3 rounded-full text-sm'>" . $position . "</span>";
echo "</div>";

// Highlight section styled as cards in a 4-column grid
echo "<div class='bio-highlights bg-[#202428] grid grid-cols-2 sm:grid-cols-4 gap-3 p-4'>";

// Age calculation
$birthDateObj = new DateTime($birthDate);
$today = new DateTime();
$age = $today->diff($birthDateObj)->y;

echo "<div class='bio-highlight-item bg-[#2c3237] p-3 rounded-lg text-center'>";
echo "<div class='bio-highlight-label text-sm text-gray-400 font-medium'>Age</div>";
echo "<div class='bio-highlight-value text-xl font-bold text-white'>" . $age . "</div>";
echo "</div>";

echo "<div class='bio-highlight-item bg-[#2c3237] p-3 rounded-lg text-center'>";
echo "<div class='bio-highlight-label text-sm text-gray-400 font-medium'>Height</div>";
echo "<div class='bio-highlight-value text-xl font-bold text-white'>" . $heightIn . "</div>";
echo "</div>";

echo "<div class='bio-highlight-item bg-[#2c3237] p-3 rounded-lg text-center'>";
echo "<div class='bio-highlight-label text-sm text-gray-400 font-medium'>Weight</div>";
echo "<div class='bio-highlight-value text-xl font-bold text-white'>" . $weightLb . " lbs</div>";
echo "</div>";

echo "<div class='bio-highlight-item bg-[#2c3237] p-3 rounded-lg text-center'>";
echo "<div class='bio-highlight-label text-sm text-gray-400 font-medium'>Shoots/Catches</div>";
if ($shootsCatches == 'L') {
    $shootsCatches = 'Left';
} else if ($shootsCatches == 'R') {
    $shootsCatches = 'Right';
} else {
    $shootsCatches = 'Unknown';
}
echo "<div class='bio-highlight-value text-xl font-bold text-white'>" . $shootsCatches . "</div>";
echo "</div>";

echo "</div>"; // close bio-highlights

// Bio body with 3-column grid on larger screens
echo "<div class='bio-body bg-[#292e33] p-5 text-white grid grid-cols-1 md:grid-cols-3 gap-8'>";

// Personal Info Section
echo "<div class='bio-section'>";
echo "<h5 class='bio-section-title text-lg font-semibold mb-2 border-b border-gray-700 pb-1'>Personal Information</h5>";
echo "<div class='space-y-2'>";
echo "<p class='flex justify-between'><span class='font-semibold text-gray-400'>Birthdate:</span> <span class='text-gray-200'>" . $birthDate . "</span></p>";

if ($birthStateProvince == '') {
    echo "<p class='flex justify-between'><span class='font-semibold text-gray-400'>Birthplace:</span> <span class='text-gray-200'>" . $birthCity . " (" . $birthCountry . ")</span></p>";
} else {
    echo "<p class='flex justify-between'><span class='font-semibold text-gray-400'>Birthplace:</span> <span class='text-gray-200'>" . $birthCity . ", " . $birthStateProvince . " (" . $birthCountry . ")</span></p>";
}
echo "</div>";
echo "</div>";

// Draft Info Section
echo "<div class='bio-section'>";
echo "<h5 class='bio-section-title text-lg font-semibold mb-2 border-b border-gray-700 pb-1'>Draft Information</h5>";
echo "<div class='space-y-2'>";
if ($draftYear == 'N/A') {
    echo "<p class='flex justify-between'><span class='font-semibold text-gray-400'>Status:</span> <span class='text-gray-200'>Undrafted</span></p>";
} else {
    echo "<p class='flex justify-between'><span class='font-semibold text-gray-400'>Year:</span> <span class='text-gray-200'>" . $draftYear . "</span></p>";
    echo "<p class='flex justify-between'><span class='font-semibold text-gray-400'>Team:</span> <span class='text-gray-200'>" . $draftTeam . "</span></p>";
    echo "<p class='flex justify-between'><span class='font-semibold text-gray-400'>Round/Pick:</span> <span class='text-gray-200'>Round " . $draftRound . ", Pick " . $draftPickInRound . " (#" . $draftOverall . " Overall)</span></p>";
}
echo "</div>";
echo "</div>";

// Awards Section
if (!empty($awardNames)) {
    $awardNamesArray = json_decode(str_replace("'", '"', $awardNames), true);
    $awardSeasonsArray = json_decode(str_replace("'", '"', $awardSeasons), true);

    if (is_array($awardNamesArray) && is_array($awardSeasonsArray) && count($awardNamesArray) > 0) {
        echo "<div class='bio-section'>";
        echo "<h5 class='bio-section-title text-lg font-semibold mb-2 border-b border-gray-700 pb-1'>Awards & Achievements</h5>";
        echo "<div class='awards space-y-2'>";
        for ($i = 0; $i < count($awardNamesArray); $i++) {
            $award = $awardNamesArray[$i];
            $seasonsRaw = $awardSeasonsArray[$i];

            $formattedSeasons = array_map(function($s) {
                return substr($s, 0, 4) . "-" . substr($s, 4, 2);
            }, $seasonsRaw);

            $seasonString = implode(", ", $formattedSeasons);
            echo "<p class='flex'><span class='text-[#45A29E] mr-2 flex-shrink-0'>•</span> <span class='text-gray-200'>" . $award . " <span class='text-gray-400'>(" . $seasonString . ")</span></span></p>";
        }
        echo "</div>";
        echo "</div>";
    }
} else {
    // If no awards, add an empty third column to maintain grid layout
    echo "<div class='bio-section'>";
    echo "<h5 class='bio-section-title text-lg font-semibold mb-2 border-b border-gray-700 pb-1'>Awards & Achievements</h5>";
    echo "<p class='text-gray-400'>No awards or achievements recorded.</p>";
    echo "</div>";
}

echo "</div>"; // close bio-body
echo "</div>"; // close bio-box

echo "</div>"; // close overall player-profile-container

      ########################################################################## Stat Tables ##################################################################


// Main container
echo "<div class='w-full max-w-7xl mx-auto mb-8'>";

// Stats wrapper with bio-style container
echo "<div class='rounded-lg overflow-hidden shadow-xl border border-[#45A29E]'>";

// Header matching bio box style
echo "<div class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white px-4 py-3 flex justify-between items-center'>";
echo "<span class='text-lg font-bold'>Player Statistics</span>";
echo "</div>";

// Tabs container with bio-style background
echo "<div class='bg-[#202428] px-4 py-3 border-b border-gray-700'>";
echo "<div class='flex flex-wrap gap-2'>";
echo "<button class='bg-[#45A29E] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 hockey-tab-button active' data-tab='tab1'>Last 5 Games</button>";
echo "<button class='bg-[#2c3237] hover:bg-[#343a40] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 hockey-tab-button' data-tab='tab2'>Featured Season</button>";
echo "<button class='bg-[#2c3237] hover:bg-[#343a40] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 hockey-tab-button' data-tab='tab3'>Career Regular Season</button>";
echo "<button class='bg-[#2c3237] hover:bg-[#343a40] text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 hockey-tab-button' data-tab='tab4'>Career Playoffs</button>";
echo "</div>";
echo "</div>";

// Tab content with bio-style background
echo "<div class='bg-[#292e33] p-5 text-center'>";

// Hidden tabs CSS
echo "<style>
.hockey-tab-pane {
    display: none;
}
.hockey-tab-pane.active {
    display: block;
}
.hockey-tab-button.active {
    background-color: #45A29E !important;
}
</style>";

// TAB 1: Last 5 Games Statistics
echo "<div class='hockey-tab-pane active' id='tab1'>";
if (strtolower($position) == 'g') {
    if (mysqli_num_rows($last5GameInfo) == 0) {
        echo "<div class='overflow-x-auto'>";
        echo "<table class='w-full border-collapse border-2 border-[#45A29E] rounded-lg overflow-hidden'>";
        echo "<thead>";
        echo "<tr class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white'>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>ID</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Team</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Opp.</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>H/A</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SA</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GA</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Saves</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Save %</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Starter</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        echo "<tr class='bg-[#2c3237] text-white'>";
        echo "<td colspan='9' class='text-center border border-gray-600 px-3 py-3 text-gray-300'>No data available.</td>";
        echo "</tr>";
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "<div class='overflow-x-auto'>";
        echo "<table class='w-full border-collapse border-2 border-[#45A29E] rounded-lg overflow-hidden'>";
        echo "<thead>";
        echo "<tr class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white'>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>ID</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Team</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Opp.</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>H/A</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SA</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GA</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Saves</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Save %</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Starter</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($row = mysqli_fetch_assoc($last5GameInfo)) {
            $last5_games_id = isset($row['game_id']) ? $row['game_id'] : null;
            $last5_games_team = isset($row['team']) ? $row['team'] : null;
            $last5_games_opponent = isset($row['opponent']) ? $row['opponent'] : null;
            $last5_games_homeRoad = isset($row['homeRoad']) ? $row['homeRoad'] : null;  
            $last5_games_shotsAgainst = isset($row['goalie_shotsAgainst']) ? $row['goalie_shotsAgainst'] : null;
            $last5_games_goalsAgainst = isset($row['goalie_goalsAgainst']) ? $row['goalie_goalsAgainst'] : null;
            $last5_games_saves = isset($row['goalie_saves']) ? $row['goalie_saves'] : null;
            $last5_games_savePctg = isset($row['goalie_savePctg']) ? $row['goalie_savePctg'] : null;
            $last5_games_starter = isset($row['goalie_starter']) ? $row['goalie_starter'] : null;
            
            echo "<tr class='bg-[#2c3237] hover:bg-[#343a40] text-white transition-colors duration-200'>";
            echo "<td class='border border-gray-600 px-3 py-2'><a href='https://connoryoung.com/game_details.php?game_id=" . $last5_games_id . "' class='text-[#45A29E] hover:text-white transition-colors duration-200'>$last5_games_id</a></td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $last5_games_team . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $last5_games_opponent . "</td>";
            
            if ($last5_games_homeRoad =='H') {
                $last5_games_homeRoad = 'Home';
            } else if ($last5_games_homeRoad == 'A') {
                $last5_games_homeRoad = 'Away';
            } else {
                $last5_games_homeRoad = 'N/A';
            }
            
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $last5_games_homeRoad . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($last5_games_shotsAgainst)) . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($last5_games_goalsAgainst)) . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(number_format($last5_games_saves, 2)) . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(number_format($last5_games_savePctg, 3)) . "</td>";
            
            if ($last5_games_starter == 1) {
                echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>Yes</td>";
            } else {
                echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>No</td>";
            }
            
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }
} else {
    // Skater Last 5 Games
    if (mysqli_num_rows($last5GameInfo) == 0) {
        echo "<div class='overflow-x-auto'>";
        echo "<table class='w-full border-collapse border-2 border-[#45A29E] rounded-lg overflow-hidden'>";
        echo "<thead>";
        echo "<tr class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white'>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>ID</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Team</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Opp.</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>H/A</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>G</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>A</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>P</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>+/-</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PIM</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Hits</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PPG</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SOG</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>FO %</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>TOI</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        echo "<tr class='bg-[#2c3237] text-white'>";
        echo "<td colspan='14' class='text-center border border-gray-600 px-3 py-3 text-gray-300'>No data available.</td>";
        echo "</tr>";
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "<div class='overflow-x-auto'>";
        echo "<table class='w-full border-collapse border-2 border-[#45A29E] rounded-lg overflow-hidden'>";
        echo "<thead>";
        echo "<tr class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white'>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>ID</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Team</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Opp.</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>H/A</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>G</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>A</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>P</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>+/-</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PIM</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Hits</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PPG</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SOG</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>FO %</th>";
            echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>TOI</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        while ($row = mysqli_fetch_assoc($last5GameInfo)) {
            $last5_games_id = isset($row['game_id']) ? $row['game_id'] : null;
            $last5_games_team = isset($row['team']) ? $row['team'] : null;
            $last5_games_opponent = isset($row['opponent']) ? $row['opponent'] : null;
            $last5_games_homeRoad = isset($row['homeRoad']) ? $row['homeRoad'] : null;  
            $last5_games_goals = isset($row['skater_goals']) ? $row['skater_goals'] : null;
            $last5_games_assists = isset($row['assists']) ? $row['assists'] : null;
            $last5_games_points = isset($row['points']) ? $row['points'] : null;
            $last5_games_plusMinus = isset($row['plusMinus']) ? $row['plusMinus'] : null;
            $last5_games_pim = isset($row['pim']) ? $row['pim'] : null;
            $last5_games_hits = isset($row['hits']) ? $row['hits'] : null;
            $last5_games_ppg = isset($row['powerPlayGoals']) ? $row['powerPlayGoals'] : null;
            $last5_games_sog = isset($row['sog']) ? $row['sog'] : null;
            $last5_games_faceoffWinningPctg = isset($row['faceoffWinningPctg']) ? $row['faceoffWinningPctg'] : null;
            $last5_games_toi = isset($row['toi']) ? $row['toi'] : null;
            
            echo "<tr class='bg-[#2c3237] hover:bg-[#343a40] text-white transition-colors duration-200'>";
            echo "<td class='border border-gray-600 px-3 py-2'><a href='https://connoryoung.com/game_details.php?game_id=" . $last5_games_id . "' class='text-[#45A29E] hover:text-white transition-colors duration-200'>$last5_games_id</a></td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $last5_games_team . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $last5_games_opponent . "</td>";
            
            if ($last5_games_homeRoad =='H') {
                $last5_games_homeRoad = 'Home';
            } else if ($last5_games_homeRoad == 'A') {
                $last5_games_homeRoad = 'Away';
            } else {
                $last5_games_homeRoad = 'N/A';
            }
            
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $last5_games_homeRoad . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $last5_games_goals . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($last5_games_assists)) . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($last5_games_points)) . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($last5_games_plusMinus)) . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($last5_games_pim)) . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($last5_games_hits)) . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($last5_games_ppg)) . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($last5_games_sog)) . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($last5_games_faceoffWinningPctg)) . "</td>";
            echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($last5_games_toi)) . "</td>";
            
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }
}
echo "</div>"; // End TAB 1

// TAB 2: Featured Season Statistics
echo "<div class='hockey-tab-pane' id='tab2'>";
$formatted_featuredSeason_1 = substr($featuredSeason, 0, 4);
$formatted_featuredSeason_2 = substr($featuredSeason, 4);
echo "<h3 class='text-xl font-semibold mb-4 text-white text-center border-b border-gray-700 pb-2'>" . $formatted_featuredSeason_1 . "-" . $formatted_featuredSeason_2 . "</h3>";

if (strtolower($position) == 'g') {
    // GOALIE STATS BLOCK
    echo "<div class='overflow-x-auto'>";
    echo "<table class='w-full border-collapse border-2 border-[#45A29E] rounded-lg overflow-hidden'>";
    echo "<thead>";
    echo "<tr class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white'>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GP</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>W</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>L</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GAA</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Save %</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SO</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>T</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GS</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GA</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>OT L</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SA</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    echo "<tr class='bg-[#2c3237] hover:bg-[#343a40] text-white transition-colors duration-200'>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$featuredSeasonGP</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$featuredSeasonWins</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$featuredSeasonLosses</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . number_format($featuredSeasonGAA,2) . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . number_format($featuredSeasonSavePct,3) . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$featuredSeasonSO</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$featuredSeasonTies</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$featuredSeasonGS</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$featuredSeasonGA</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$featuredSeasonOTLosses</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$featuredSeasonShotsAgainst</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    // SKATER STATS BLOCK
    echo "<div class='overflow-x-auto'>";
    echo "<table class='w-full border-collapse border-2 border-[#45A29E] rounded-lg overflow-hidden'>";
    echo "<thead>";
    echo "<tr class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white'>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GP</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>G</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>A</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Pts</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>+/-</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PIM</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Shots</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Shot %</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PPG</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PP Pts</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SHG</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SH Pts</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GWG</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>OTG</th>"; 
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    echo "<tr class='bg-[#2c3237] hover:bg-[#343a40] text-white transition-colors duration-200'>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonGP . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonGoals . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonAssists . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonPts . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonPlusMinus . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonPIM . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonShots . "</td>";
        $formatted_featuredSeasonShootingPct = (float)$featuredSeasonShootingPct * 100;
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . round(number_format($formatted_featuredSeasonShootingPct, 2), 1) . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonPPG . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonPPPoints . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonSHG . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonSHPts . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonGWG . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $featuredSeasonOTGoals . "</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}
echo "</div>"; // End TAB 2

// TAB 3: Career Regular Season Statistics
echo "<div class='hockey-tab-pane' id='tab3'>";
echo "<h3 class='text-xl font-semibold mb-4 text-white text-center border-b border-gray-700 pb-2'>Career Regular Season</h3>";

if (strtolower($position) == 'g') {
    // GOALIE STATS BLOCK
    echo "<div class='overflow-x-auto'>";
    echo "<table class='w-full border-collapse border-2 border-[#45A29E] rounded-lg overflow-hidden'>";
    echo "<thead>";
    echo "<tr class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white'>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GP</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>W</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>L</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GAA</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Save %</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SO</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>T</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GS</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GA</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>OT L</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SA</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    echo "<tr class='bg-[#2c3237] hover:bg-[#343a40] text-white transition-colors duration-200'>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$regSeasonCareerGP</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$regSeasonCareerWins</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$regSeasonCareerLosses</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . number_format($regSeasonCareerGAA,2) . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . number_format($regSeasonCareerSavePct,3) . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$regSeasonCareerSO</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$regSeasonCareerTies</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$regSeasonCareerGS</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$regSeasonCareerGA</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$regSeasonCareerOTLosses</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$regSeasonCareerShotsAgainst</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    // SKATER STATS BLOCK
    echo "<div class='overflow-x-auto'>";
    echo "<table class='w-full border-collapse border-2 border-[#45A29E] rounded-lg overflow-hidden'>";
    echo "<thead>";
    echo "<tr class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white'>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GP</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>G</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>A</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Pts</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>+/-</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PIM</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Shots</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Shot %</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PPG</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PP Pts</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SHG</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SH Pts</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GWG</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>OTG</th>"; 
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    echo "<tr class='bg-[#2c3237] hover:bg-[#343a40] text-white transition-colors duration-200'>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerGP . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerGoals . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerAssists . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerPts . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerPlusMinus . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerPIM . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerShots . "</td>";
        $formatted_regSeasonCareerShootingPct = round((float)$regSeasonCareerShootingPct * 100, 1);
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $formatted_regSeasonCareerShootingPct . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerPPG . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerPPPoints . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerSHG . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerSHPts . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerGWG . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $regSeasonCareerOTGoals . "</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}
echo "</div>"; // End TAB 3

// TAB 4: Career Playoff Statistics
echo "<div class='hockey-tab-pane' id='tab4'>";
echo "<h3 class='text-xl font-semibold mb-4 text-white text-center border-b border-gray-700 pb-2'>Career Playoffs</h3>";

if (strtolower($position) == 'g') {
    // GOALIE STATS BLOCK
    echo "<div class='overflow-x-auto'>";
    echo "<table class='w-full border-collapse border-2 border-[#45A29E] rounded-lg overflow-hidden'>";
    echo "<thead>";
    echo "<tr class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white'>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GP</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>W</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>L</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GAA</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Save %</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SO</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>T</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GS</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GA</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>OT L</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SA</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    echo "<tr class='bg-[#2c3237] hover:bg-[#343a40] text-white transition-colors duration-200'>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$playoffsCareerGP</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$playoffsCareerWins</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$playoffsCareerLosses</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . number_format((float)$playoffsCareerGAA,2) . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . number_format((float)$playoffsCareerSavePct,3) . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$playoffsCareerSO</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$playoffsCareerTies</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$playoffsCareerGS</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$playoffsCareerGA</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$playoffsCareerOTLosses</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>$playoffsCareerShotsAgainst</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
} else {
    // SKATER STATS BLOCK
    echo "<div class='overflow-x-auto'>";
    echo "<table class='w-full border-collapse border-2 border-[#45A29E] rounded-lg overflow-hidden'>";
    echo "<thead>";
    echo "<tr class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white'>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GP</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>G</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>A</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Pts</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>+/-</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PIM</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Shots</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Shot %</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PPG</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PP Pts</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SHG</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>SH Pts</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GWG</th>";
        echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>OTG</th>"; 
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    echo "<tr class='bg-[#2c3237] hover:bg-[#343a40] text-white transition-colors duration-200'>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerGP . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerGoals . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerAssists . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerPts . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerPlusMinus . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerPIM . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerShots . "</td>";
        $formatted_playoffsCareerShootingPct = round((float)$playoffsCareerShootingPct * 100, 1);
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $formatted_playoffsCareerShootingPct . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerPPG . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerPPPoints . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerSHG . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerSHPts . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerGWG . "</td>";
        echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . $playoffsCareerOTGoals . "</td>";
    echo "</tr>";
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}
echo "</div>"; // End TAB 4

echo "</div>"; // End tab content container
echo "</div>"; // End stats wrapper
echo "</div>"; // End main container

// Season-by-Season Stats with bio box styling
echo "<div class='w-full max-w-7xl mx-auto mb-8'>";

// Stats wrapper with bio-style container
echo "<div class='rounded-lg overflow-hidden shadow-xl border border-[#45A29E]'>";

// Header matching bio box style
echo "<div class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white px-4 py-3'>";
echo "<span class='text-lg font-bold'>Season-by-Season Statistics</span>";
echo "</div>";

// Table content with bio-style background
echo "<div class='bg-[#292e33] p-5'>";
echo "<div class='overflow-x-auto'>";

$seasonStatsSQL = "SELECT *
                   FROM player_season_stats 
                   WHERE playerID=$player_id 
                   ORDER BY seasonSeason ASC";
$seasonStats = mysqli_query($conn, $seasonStatsSQL);

echo "<table class='w-full border-collapse border-2 border-[#45A29E] rounded-lg overflow-hidden'>";
echo "<thead>";
echo "<tr class='bg-gradient-to-r from-[#1F2833] to-[#2C3E50] text-white'>";

if (strtolower($position) == 'g') {
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Season</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>League</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Team Name</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Season Type</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GP</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>W</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>L</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GAA</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Sv %</th>";
} else {
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Season</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>League</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Team Name</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Season Type</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>GP</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>G</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>A</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>Pts</th>";
  echo "<th class='border border-gray-600 px-3 py-2 text-sm font-semibold'>PIM</th>";
}
echo "</tr>";
echo "</thead>";
echo "<tbody>";

# initializing variables to store career totals
$totalGP = $totalG = $totalA = $totalPts = $totalPIM = $totalW = $totalL = $totalGAA = $totalSavePct = $count = 0;

while ($row = mysqli_fetch_assoc($seasonStats)) {
    $count+=1;
    echo "<tr class='bg-[#2c3237] hover:bg-[#343a40] text-white transition-colors duration-200'>";
    $formatted_season_1 = substr($row['seasonSeason'], 0, 4);
    $formatted_season_2 = substr($row['seasonSeason'], 4);
    echo "<td class='border border-gray-600 px-3 py-2 text-center text-gray-200'>".htmlspecialchars($formatted_season_1)."-".htmlspecialchars($formatted_season_2)."</td>";
    echo "<td class='border border-gray-600 px-3 py-2 text-center text-gray-200'>" . htmlspecialchars($row['seasonLeagueAbbrev']) . "</td>";
    echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars($row['seasonTeamName']) . "</td>";
    
    $gameType_num = $row['seasonGameTypeId'];
    if ($gameType_num == 1) {
        $gameType_text = "Pre.";
    } elseif ($gameType_num == 2) {
        $gameType_text = "Reg.";
    } elseif ($gameType_num == 3) {
        $gameType_text = "Post.";
    } else {
        $gameType_text = "Unknown";
    }
    echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>".$gameType_text."</td>";

    echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($row['seasonGamesPlayed'])) . "</td>";

    if (strtolower($position) == 'g') {
      echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($row['seasonWins'])) . "</td>";
      echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($row['seasonLosses'])) . "</td>";
      echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(number_format($row['seasonGAA'], 2)) . "</td>";
      echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(number_format($row['seasonSavePct'], 3)) . "</td>";

      // Tally up goalie stats
      $totalGP       += (int) $row['seasonGamesPlayed'];
      $totalW        += (int) $row['seasonWins'];
      $totalL        += (int) $row['seasonLosses'];
      $totalGAA      += (float) $row['seasonGAA'];
      $totalSavePct  += (float) $row['seasonSavePct'];
  } else {
      echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($row['seasonGoals'])) . "</td>";
      echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($row['seasonAssists'])) . "</td>";
      echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($row['seasonPoints'])) . "</td>";
      echo "<td class='border border-gray-600 px-3 py-2 text-gray-200'>" . htmlspecialchars(fillEmptyStats($row['seasonPIM'])) . "</td>";

      // Tally up skater stats
      $totalGP   += (int) $row['seasonGamesPlayed'];
      $totalG    += (int) $row['seasonGoals'];
      $totalA    += (int) $row['seasonAssists'];
      $totalPts  += (int) $row['seasonPoints'];
      $totalPIM  += (int) $row['seasonPIM'];
  }

  echo "</tr>";
}

// Career totals section with bio box styling
if (strtolower($position) == 'g') {
  $avgGAA = $count > 0 ? $totalGAA / $count : 0;
  $avgSavePct = $count > 0 ? $totalSavePct / $count : 0;

  // Career totals header row
  echo "<tr class='bg-[#1F2833] text-white border-t-2 border-[#45A29E]'>";
  echo "<td colspan='4' rowspan='2' class='border border-gray-600 px-3 py-3 text-center font-bold text-lg'>Career Totals</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center font-semibold'>GP</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center font-semibold'>W</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center font-semibold'>L</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center font-semibold'>GAA</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center font-semibold'>Sv %</td>";
  echo "</tr>";

  // Career totals values row
  echo "<tr class='bg-[#2c3237] text-white font-bold'>";
//   echo "<td colspan='4' class='border border-gray-600 px-3 py-3'></td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center'>$totalGP</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center'>$totalW</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center'>$totalL</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center'>" . number_format($avgGAA, 2) . "</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center'>" . number_format($avgSavePct, 3) . "</td>";
  echo "</tr>";
} else {
  // Career totals header row for skaters
  echo "<tr class='bg-[#1F2833] text-white border-t-2 border-[#45A29E]'>";
  echo "<td colspan='4' rowspan='2' class='border border-gray-600 px-3 py-3 text-center font-bold text-lg'>Career Totals</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center font-semibold'>GP</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center font-semibold'>G</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center font-semibold'>A</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center font-semibold'>Pts</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center font-semibold'>PIM</td>";
  echo "</tr>";

  // Career totals values row for skaters
  echo "<tr class='bg-[#2c3237] text-white font-bold'>";
//   echo "<td colspan='4' class='border border-gray-600 px-3 py-3'></td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center'>$totalGP</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center'>$totalG</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center'>$totalA</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center'>$totalPts</td>";
  echo "<td class='border border-gray-600 px-3 py-2 text-center'>$totalPIM</td>";
  echo "</tr>";
}

echo "</tbody>";
echo "</table>";
echo "</div>"; // End overflow container
echo "</div>"; // End content container
echo "</div>"; // End stats wrapper
echo "</div>"; // End main container

} // End if(isset($_GET['player_id']))
?>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
    // Get all tab buttons
    const tabButtons = document.querySelectorAll('.hockey-tab-button');
    
    // Add click event to each tab button
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get the tab ID from data-tab attribute
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and tab panes
            document.querySelectorAll('.hockey-tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.querySelectorAll('.hockey-tab-pane').forEach(pane => {
                pane.classList.remove('active');
            });
            
            // Add active class to clicked button and corresponding tab pane
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
});
    </script>
    
</div>

<?php include 'footer.php'; ?>
  </body>
  
</html>