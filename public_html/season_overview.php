<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NHL Season Statistics and Leaders">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Season Overview</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="../resources/css/default_v3.css" rel="stylesheet" type="text/css" />
    
    
    <!-- Updated tabs functionality -->
    <script>
document.addEventListener('DOMContentLoaded', function() {
  // Keep track of active player tab for each main tab
  let activePlayerTabs = {
    "regular-season": "#skaters-content",
    "playoffs": "#skaters-playoffs"
  };

  // Tab navigation setup
  function setupTabs(tabSelector, contentSelector, isMainTab = false) {
    const tabs = document.querySelectorAll(tabSelector);
    const contents = document.querySelectorAll(contentSelector);
    
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const target = document.querySelector(tab.dataset.target);
        const targetId = tab.dataset.target.substring(1); // Remove the # from id
        
        // Hide all contents
        contents.forEach(content => {
          content.classList.add('hidden');
        });
        
        // Deactivate all tabs
        tabs.forEach(t => {
          t.classList.remove('active');
        });
        
        // Activate selected tab and show content
        tab.classList.add('active');
        target.classList.remove('hidden');
        
        // If this is a main tab switch, remember which section we're in
        if (isMainTab) {
          const currentSection = targetId === "regular-season" ? "regular" : "playoffs";
          
          // Apply the correct player tab (skaters/goalies) based on what was active before
          if (currentSection === "regular") {
            // Find the corresponding skaters/goalies tab in regular season section
            const playerTabTarget = activePlayerTabs["regular-season"];
            const playerTab = document.querySelector(`.player-tab[data-target="${playerTabTarget}"]`);
            if (playerTab) playerTab.click();
          } else {
            // Find the corresponding skaters/goalies tab in playoffs section
            const playerTabTarget = activePlayerTabs["playoffs"];
            const playerTab = document.querySelector(`.player-tab[data-target="${playerTabTarget}"]`);
            if (playerTab) playerTab.click();
          }
        } else {
          // This is a player tab (skaters/goalies), remember the selection for this main section
          const currentSection = target.id.includes("playoffs") ? "playoffs" : "regular-season";
          activePlayerTabs[currentSection] = tab.dataset.target;
        }
      });
    });
  }
  
  // Initialize tabs
  setupTabs('.main-tab', '.main-content', true);
  setupTabs('.player-tab', '.player-content');
  
  // Set default tabs
  document.querySelector('.main-tab[data-target="#regular-season"]').click();
  
  // Set up season selector dropdown
  const seasonSelect = document.getElementById('season-select');
  if (seasonSelect) {
    seasonSelect.addEventListener('change', function() {
      if (this.value) {
        window.location.href = 'season_overview.php?season_id=' + this.value;
      }
    });
  }
});
    </script>
  </head>

  <body class="flex flex-col min-h-screen">
    
    <!-- Header -->
    <?php include 'header.php'; ?>
    
    <!-- Main Content -->
    <main class="flex-grow text-white" style='background-color: #343a40'>
      <?php
        include('db_connection.php');

        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        if (isset($_GET['season_id'])) {
          $season_id = $_GET['season_id'];
          
          // Format season for display (e.g. 20212022 -> 2021-2022)
          $formatted_season = substr($season_id, 0, 4) . '-' . substr($season_id, 4);
      ?>
          <div class="page-container">
            <h1 class="page-title text-3xl font-bold text-center mt-4 mb-6"><?php echo $formatted_season ?> Season Overview</h1>

            <!-- Season navigation with dropdown -->
            <div class="flex justify-center mb-8">
                <div class="season-selector w-full max-w-xs">
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
            
                <?php
                $sql = "SELECT season_awards.*, 
                               stanleyCupTeams.fullName as stanleyCupWinner, stanleyCupTeams.teamLogo as stanleyCupLogo,
                               presTrophyTeams.fullName as presTrophyWinner, presTrophyTeams.teamLogo as presTrophyLogo,
                               messierWinners.firstName as messierWinnerFirstName, messierWinners.lastName as messierWinnerLastName, messierWinners.playerId as messierWinnerPlayerId,
                               richardWinners.firstName as richardWinnerFirstName, richardWinners.lastName as richardWinnerLastName, richardWinners.playerId as richardWinnerPlayerId,
                               hartWinners.firstName as hartWinnerFirstName, hartWinners.lastName as hartWinnerLastName, hartWinners.playerId as hartTrophyWinnerID,
                               vezinaWinners.firstName as vezinaWinnerFirstName, vezinaWinners.lastName as vezinaWinnerLastName, vezinaWinners.playerId as vezinaWinnerPlayerId,
                               kingClancyWinners.firstName as kingClancyWinnerFirstName, kingClancyWinners.lastName as kingClancyWinnerLastName, kingClancyWinners.playerId as kingClancyWinnerPlayerId,
                               selkeWinners.firstName as selkeWinnerFirstName, selkeWinners.lastName as selkeWinnerLastName, selkeWinners.playerId as selkeWinnerPlayerId,
                               lindsayWinners.firstName as lindsayWinnerFirstName, lindsayWinners.lastName as lindsayWinnerLastName, lindsayWinners.playerId as lindsayWinnerPlayerId,
                               mastertonWinners.firstName as mastertonWinnerFirstName, mastertonWinners.lastName as mastertonWinnerLastName, mastertonWinners.playerId as mastertonWinnerPlayerId,
                               connSmytheWinners.firstName as connSmytheWinnerFirstName, connSmytheWinners.lastName as connSmytheWinnerLastName, connSmytheWinners.playerId as connSmytheWinnerPlayerId,
                               norrisWinners.firstName as norrisWinnerFirstName, norrisWinners.lastName as norrisWinnerLastName, norrisWinners.playerId as norrisWinnerPlayerId,
                               artRossWinners.firstName as artRossWinnerFirstName, artRossWinners.lastName as artRossWinnerLastName, artRossWinners.playerId as artRossWinnerPlayerId,
                               calderWinners.firstName as calderWinnerFirstName, calderWinners.lastName as calderWinnerLastName, calderWinners.playerId as calderWinnerPlayerId
                        FROM season_awards
                        LEFT JOIN nhl_teams as stanleyCupTeams ON season_awards.stanleyCupWinnerID=stanleyCupTeams.id
                        LEFT JOIN nhl_teams as presTrophyTeams ON season_awards.presidentsTrophyWinnerID=presTrophyTeams.id
                        LEFT JOIN nhl_players as messierWinners ON season_awards.messierTrophyWinnerID=messierWinners.playerId
                        LEFT JOIN nhl_players as richardWinners ON season_awards.richardTrophyWinnerID=richardWinners.playerId
                        LEFT JOIN nhl_players as hartWinners ON season_awards.hartTrophyWinnerID=hartWinners.playerId
                        LEFT JOIN nhl_players as vezinaWinners ON season_awards.vezinaTrophyWinnerID=vezinaWinners.playerId
                        LEFT JOIN nhl_players as kingClancyWinners ON season_awards.kingClancyWinnerID=kingClancyWinners.playerId
                        LEFT JOIN nhl_players as selkeWinners ON season_awards.selkeWinnerID=selkeWinners.playerId
                        LEFT JOIN nhl_players as lindsayWinners ON season_awards.lindsayWinnerID=lindsayWinners.playerId
                        LEFT JOIN nhl_players as mastertonWinners ON season_awards.mastertonWinnerID=mastertonWinners.playerId
                        LEFT JOIN nhl_players as connSmytheWinners ON season_awards.connSmytheWinnerID=connSmytheWinners.playerId
                        LEFT JOIN nhl_players as norrisWinners ON season_awards.norrisWinnerID=norrisWinners.playerId
                        LEFT JOIN nhl_players as artRossWinners ON season_awards.artRossWinnerID=artRossWinners.playerId
                        LEFT JOIN nhl_players as calderWinners ON season_awards.calderWinnerID=calderWinners.playerId
                        WHERE seasonID = $season_id";

                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                $stanleyCupChamp = $row['stanleyCupWinner'];
                $stanleyCupLogo = $row['stanleyCupLogo'];
                $presTrophyWinner = $row['presTrophyWinner'];
                $presTrophyLogo = $row['presTrophyLogo'];
                $messierWinner = $row['messierWinnerFirstName'] . " " . $row['messierWinnerLastName'];
                $richardWinner = $row['richardWinnerFirstName'] . " " . $row['richardWinnerLastName'];
                $hartWinner = $row['hartWinnerFirstName'] . " " . $row['hartWinnerLastName'];
                $vezinaWinner = $row['vezinaWinnerFirstName'] . " " . $row['vezinaWinnerLastName'];
                $kingClancyWinner = $row['kingClancyWinnerFirstName'] . " " . $row['kingClancyWinnerLastName'];
                $selkeWinner = $row['selkeWinnerFirstName'] . " " . $row['selkeWinnerLastName'];
                $lindsayWinner = $row['lindsayWinnerFirstName'] . " " . $row['lindsayWinnerLastName'];
                $mastertonWinner = $row['mastertonWinnerFirstName'] . " " . $row['mastertonWinnerLastName'];
                $connSmytheWinner = $row['connSmytheWinnerFirstName'] . " " . $row['connSmytheWinnerLastName'];
                $norrisWinner = $row['norrisWinnerFirstName'] . " " . $row['norrisWinnerLastName'];
                $artRossWinner = $row['artRossWinnerFirstName'] . " " . $row['artRossWinnerLastName'];
                $calderWinner = $row['calderWinnerFirstName'] . " " . $row['calderWinnerLastName'];
                ?>


<div class="season-summary mt-8 mb-10">
  <h2 class="text-2xl font-bold mb-4">Season Summary</h2>
  <div class="summary-content grid grid-cols-1 md:grid-cols-2 gap-6">
    
    <!-- Left: Champion and Playoffs -->
    <div>
      <h3 class="text-xl mb-2">
        Stanley Cup Champion: 
        <span class="font-bold text-2xl text-yellow-400"><?php echo $stanleyCupChamp ?></span>
        <span><a href='<?php echo $teamLogo ?>'></a></span>
      </h3>
      <br>
      <?php
      echo "<p>Presidents' Trophy: 
        <a href='team_details.php?team_id=" . $row['presidentsTrophyWinnerID'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $presTrophyWinner . "</a></p>";
      echo "<br>";
      echo "<a class='text-sky-400 hover:text-sky-300 hover:underline transition-colors' href='https://connoryoung.com/playoff_results.php?season_id=" . $season_id . "'>View Playoff Results</a>";
      ?>
    </div>

    <!-- Right: Awards in Two Columns -->
    <div>
      <h3 class="text-xl mb-4">Individual Awards</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-2">
        <?php
        function tooltipSpan($label, $description) {
          return "<span class='relative group cursor-help'>
            <span>$label</span>
            <span class='absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-max max-w-xs bg-gray-800 text-white text-sm rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10 whitespace-nowrap'>
              $description
            </span>
          </span>";
        }

        echo "<p>" . tooltipSpan("Hart Trophy:<br>", "Regular season most valuable player") . "
        <a href='player_details.php?player_id=" . $row['hartTrophyWinnerID'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $hartWinner . "</a></p>";

        echo "<p>" . tooltipSpan("Vezina Trophy:<br>", "Regular season best goaltender") . "
        <a href='player_details.php?player_id=" . $row['vezinaWinnerPlayerId'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $vezinaWinner . "</a></p>";

        echo "<p>" . tooltipSpan("Calder Trophy:<br>", "Regular season best rookie") . "
        <a href='player_details.php?player_id=" . $row['calderWinnerPlayerId'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $calderWinner . "</a></p>";

        echo "<p>" . tooltipSpan("Ted Lindsay Award:<br>", "Most outstanding player as voted by the players") . "
        <a href='player_details.php?player_id=" . $row['lindsayWinnerPlayerId'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $lindsayWinner . "</a></p>";

        echo "<p>" . tooltipSpan("Selke Trophy:<br>", "Regular season best defensive forward") . "
        <a href='player_details.php?player_id=" . $row['selkeWinnerPlayerId'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $selkeWinner . "</a></p>";

        echo "<p>" . tooltipSpan("King Clancy Award:<br>", "Leadership and humanitarian contributions") . "
        <a href='player_details.php?player_id=" . $row['kingClancyWinnerPlayerId'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $kingClancyWinner . "</a></p>";

        echo "<p>" . tooltipSpan("Rocket Richard Trophy:<br>", "Regular season most goals scored") . "
        <a href='player_details.php?player_id=" . $row['richardWinnerPlayerId'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $richardWinner . "</a></p>";

        echo "<p>" . tooltipSpan("Mark Messier Leadership Award:<br>", "Leadership and contributions to the game") . "
        <a href='player_details.php?player_id=" . $row['messierWinnerPlayerId'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $messierWinner . "</a></p>";

        echo "<p>" . tooltipSpan("Masterton Trophy:<br>", "Perseverance and dedication to hockey") . "
        <a href='player_details.php?player_id=" . $row['mastertonWinnerPlayerId'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $mastertonWinner . "</a></p>";

        echo "<p>" . tooltipSpan("Conn Smythe Trophy:<br>", "Most valuable player in the playoffs") . "
        <a href='player_details.php?player_id=" . $row['connSmytheWinnerPlayerId'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $connSmytheWinner . "</a></p>";

        echo "<p>" . tooltipSpan("Norris Trophy:<br>", "Regular season best defenseman") . "
        <a href='player_details.php?player_id=" . $row['norrisWinnerPlayerId'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $norrisWinner . "</a></p>";

        echo "<p>" . tooltipSpan("Art Ross Trophy:<br>", "Most points in the regular season") . "
        <a href='player_details.php?player_id=" . $row['artRossWinnerPlayerId'] . "' class='text-sky-400 hover:text-sky-300 hover:underline transition-colors'>" . $artRossWinner . "</a></p>";
        ?>
      </div>
    </div>
  </div>
</div>

            
            <!-- Integrated Tab Navigation -->
            <div class="tab-container">
              <div class="tabs-outer">
                <div class="tabs-inner">
                  <!-- Main Season Type Tabs -->
                  <button class="tab-button main-tab" data-target="#regular-season">Regular Season</button>
                  <button class="tab-button main-tab" data-target="#playoffs">Playoffs</button>
                </div>
              </div>
              
              <!-- Regular Season Content -->
              <div id="regular-season" class="main-content hidden">
                <!-- Nested Player Type Tabs -->
                <div class="nested-tab-container">
                  <button class="nested-tab-button player-tab active" data-target="#skaters-content">Skaters</button>
                  <button class="nested-tab-button player-tab" data-target="#goalies-content">Goalies</button>
                </div>
                
                <!-- Content for Regular Season Skaters -->
                <div id="skaters-content" class="player-content">
                  <div class="stat-grid">
                    <?php
                    $statCategories = [
                      ['Points', 'Points', 'Points'], 
                      ['Goals', 'Goals', 'Goals'],
                      ['Assists', 'Assists', 'Assists'],
                      ['goalsPp', 'PPG', 'Power Play Goals'],
                      ['goalsSh', 'SHG', 'Shorthanded Goals'],
                      ['penaltyMins', 'PIM', 'Penalty Minutes'],
                      ['toi', 'TOI', 'Time On Ice (mins)'],
                      ['faceoffLeaders', 'FO%', 'Faceoff Percentage']
                    ];
                    
                    foreach ($statCategories as $stat) {
                      $stat_sql = "SELECT skater_past_season_leaders.*, nhl_players.firstName, nhl_players.lastName 
                                  FROM skater_past_season_leaders 
                                  JOIN nhl_players ON skater_past_season_leaders.playerID=nhl_players.playerId 
                                  WHERE seasonID = $season_id AND statCategory = '{$stat[0]}' AND seasonType = 2 
                                  ORDER BY statValue DESC LIMIT 5";
                      $result = mysqli_query($conn, $stat_sql);
                      
                      if ($result && mysqli_num_rows($result) > 0) {
                        echo "<div class='stat-card'>";
                        echo "<div class='stat-card-header'>";
                        echo "<h3 class='text-xl font-bold text-center text-white'>{$stat[2]}</h3>";
                        echo "</div>";
                        echo "<div class='stat-card-body'>";
                        echo "<table class='w-full'>";
                        echo "<thead class='border-b border-gray-600'>";
                        echo "<tr>";
                        echo "<th class='text-left pb-2'>Player</th>";
                        echo "<th class='text-right pb-2'>{$stat[1]}</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        
                        $first = true;
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<tr class='border-b border-gray-700'>";
                          echo "<td class='py-2'><a href='player_details.php?player_id={$row['playerID']}' class='hover:text-blue-300'>{$row['firstName']} {$row['lastName']}</a></td>";
                          
                          // Highlight top value
                          $highlightClass = $first ? 'highlight-value' : '';
                          $first = false;
                          
                          // Format value based on stat type
                          if ($stat[0] == 'toi') {
                            echo "<td class='text-right py-2 $highlightClass'>" . number_format($row['statValue'], 1) . "</td>";
                          } elseif ($stat[0] == 'faceoffLeaders') {
                            echo "<td class='text-right py-2 $highlightClass'>" . number_format($row['statValue']*100, 1) . "%</td>";
                          } else {
                            echo "<td class='text-right py-2 $highlightClass'>" . $row['statValue'] . "</td>";
                          }
                          
                          echo "</tr>";
                        }
                        
                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";
                        echo "</div>";
                      }
                    }
                    ?>
                  </div>
                </div>
                
                <!-- Content for Regular Season Goalies -->
                <div id="goalies-content" class="player-content hidden">
                  <div class="stat-grid">
                    <?php
                    $goalieStatCategories = [
                      ['wins', 'Wins', 'Wins'],
                      ['goalsAgainstAverage', 'GAA', 'Goals Against Average'],
                      ['savePctg', 'SV%', 'Save Percentage'],
                      ['shutouts', 'SO', 'Shutouts']
                    ];
                    
                    foreach ($goalieStatCategories as $stat) {
                      // Determine sort order - lower is better for GAA, higher is better for everything else
                      $sortDirection = ($stat[0] == 'goalsAgainstAverage') ? 'ASC' : 'DESC';
                      
                      $stat_sql = "SELECT goalie_past_season_leaders.*, nhl_players.firstName, nhl_players.lastName 
                                  FROM goalie_past_season_leaders 
                                  JOIN nhl_players ON goalie_past_season_leaders.playerID=nhl_players.playerId 
                                  WHERE seasonID = $season_id AND statCategory = '{$stat[0]}' AND seasonType = 2 
                                  ORDER BY statValue $sortDirection LIMIT 5";
                      $result = mysqli_query($conn, $stat_sql);
                      
                      if ($result && mysqli_num_rows($result) > 0) {
                        echo "<div class='stat-card'>";
                        echo "<div class='stat-card-header'>";
                        echo "<h3 class='text-xl font-bold text-center text-white'>{$stat[2]}</h3>";
                        echo "</div>";
                        echo "<div class='stat-card-body'>";
                        echo "<table class='w-full'>";
                        echo "<thead class='border-b border-gray-600'>";
                        echo "<tr>";
                        echo "<th class='text-left pb-2'>Player</th>";
                        echo "<th class='text-right pb-2'>{$stat[1]}</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        
                        $first = true;
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<tr class='border-b border-gray-700'>";
                          echo "<td class='py-2'><a href='player_details.php?player_id={$row['playerID']}' class='hover:text-blue-300'>{$row['firstName']} {$row['lastName']}</a></td>";
                          
                          // Highlight top value
                          $highlightClass = $first ? 'highlight-value' : '';
                          $first = false;
                          
                          // Format value based on stat type
                          if ($stat[0] == 'savePctg') {
                            echo "<td class='text-right py-2 $highlightClass'>" . number_format($row['statValue'], 3) . "</td>";
                          } elseif ($stat[0] == 'goalsAgainstAverage') {
                            echo "<td class='text-right py-2 $highlightClass'>" . number_format($row['statValue'], 2) . "</td>";
                          } else {
                            echo "<td class='text-right py-2 $highlightClass'>" . $row['statValue'] . "</td>";
                          }
                          
                          echo "</tr>";
                        }
                        
                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";
                        echo "</div>";
                      }
                    }
                    ?>
                  </div>
                </div>
              </div>
              
              <!-- Playoffs Content -->
              <div id="playoffs" class="main-content hidden">
                <!-- Nested Player Type Tabs -->
                <div class="nested-tab-container">
                  <button class="nested-tab-button player-tab active" data-target="#skaters-playoffs">Skaters</button>
                  <button class="nested-tab-button player-tab" data-target="#goalies-playoffs">Goalies</button>
                </div>
                
                <!-- Content for Playoffs Skaters -->
                <div id="skaters-playoffs" class="player-content">
                  <div class="stat-grid">
                    <?php
                    // Same stats for playoffs (seasonType = 3)
                    foreach ($statCategories as $stat) {
                      $stat_sql = "SELECT skater_past_season_leaders.*, nhl_players.firstName, nhl_players.lastName 
                                  FROM skater_past_season_leaders 
                                  JOIN nhl_players ON skater_past_season_leaders.playerID=nhl_players.playerId 
                                  WHERE seasonID = $season_id AND statCategory = '{$stat[0]}' AND seasonType = 3 
                                  ORDER BY statValue DESC LIMIT 5";
                      $result = mysqli_query($conn, $stat_sql);
                      
                      if ($result && mysqli_num_rows($result) > 0) {
                        echo "<div class='stat-card'>";
                        echo "<div class='stat-card-header'>";
                        echo "<h3 class='text-xl font-bold text-center text-white'>{$stat[2]}</h3>";
                        echo "</div>";
                        echo "<div class='stat-card-body'>";
                        echo "<table class='w-full'>";
                        echo "<thead class='border-b border-gray-600'>";
                        echo "<tr>";
                        echo "<th class='text-left pb-2'>Player</th>";
                        echo "<th class='text-right pb-2'>{$stat[1]}</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        
                        $first = true;
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<tr class='border-b border-gray-700'>";
                          echo "<td class='py-2'><a href='player_details.php?player_id={$row['playerID']}' class='hover:text-blue-300'>{$row['firstName']} {$row['lastName']}</a></td>";
                          
                          // Highlight top value
                          $highlightClass = $first ? 'highlight-value' : '';
                          $first = false;
                          
                          // Format value based on stat type
                          if ($stat[0] == 'toi') {
                            echo "<td class='text-right py-2 $highlightClass'>" . number_format($row['statValue'], 1) . "</td>";
                          } elseif ($stat[0] == 'faceoffLeaders') {
                            echo "<td class='text-right py-2 $highlightClass'>" . number_format($row['statValue']*100, 1) . "%</td>";
                          } else {
                            echo "<td class='text-right py-2 $highlightClass'>" . $row['statValue'] . "</td>";
                          }
                          
                          echo "</tr>";
                        }
                        
                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";
                        echo "</div>";
                      } else {
                        // Show empty card for stats with no playoff data
                        echo "<div class='stat-card'>";
                        echo "<div class='stat-card-header'>";
                        echo "<h3 class='text-xl font-bold text-center text-white'>{$stat[2]}</h3>";
                        echo "</div>";
                        echo "<div class='stat-card-body text-center py-6'>";
                        echo "<p class='empty-stats'>No playoff data available</p>";
                        echo "</div>";
                        echo "</div>";
                      }
                    }
                    ?>
                  </div>
                </div>
                
                <!-- Content for Playoffs Goalies -->
                <div id="goalies-playoffs" class="player-content hidden">
                  <div class="stat-grid">
                    <?php
                    // Same goalie stats for playoffs (seasonType = 3)
                    foreach ($goalieStatCategories as $stat) {
                      // Determine sort order - lower is better for GAA, higher is better for everything else
                      $sortDirection = ($stat[0] == 'goalsAgainstAverage') ? 'ASC' : 'DESC';
                      
                      $stat_sql = "SELECT goalie_past_season_leaders.*, nhl_players.firstName, nhl_players.lastName 
                                  FROM goalie_past_season_leaders 
                                  JOIN nhl_players ON goalie_past_season_leaders.playerID=nhl_players.playerId 
                                  WHERE seasonID = $season_id AND statCategory = '{$stat[0]}' AND seasonType = 3 
                                  ORDER BY statValue $sortDirection LIMIT 5";
                      $result = mysqli_query($conn, $stat_sql);
                      
                      if ($result && mysqli_num_rows($result) > 0) {
                        echo "<div class='stat-card'>";
                        echo "<div class='stat-card-header'>";
                        echo "<h3 class='text-xl font-bold text-center text-white'>{$stat[2]}</h3>";
                        echo "</div>";
                        echo "<div class='stat-card-body'>";
                        echo "<table class='w-full'>";
                        echo "<thead class='border-b border-gray-600'>";
                        echo "<tr>";
                        echo "<th class='text-left pb-2'>Player</th>";
                        echo "<th class='text-right pb-2'>{$stat[1]}</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        
                        $first = true;
                        while ($row = mysqli_fetch_assoc($result)) {
                          echo "<tr class='border-b border-gray-700'>";
                          echo "<td class='py-2'><a href='player_details.php?player_id={$row['playerID']}' class='hover:text-blue-300'>{$row['firstName']} {$row['lastName']}</a></td>";
                          
                          // Highlight top value
                          $highlightClass = $first ? 'highlight-value' : '';
                          $first = false;
                          
                          // Format value based on stat type
                          if ($stat[0] == 'savePctg') {
                            echo "<td class='text-right py-2 $highlightClass'>" . number_format($row['statValue'], 3) . "</td>";
                          } elseif ($stat[0] == 'goalsAgainstAverage') {
                            echo "<td class='text-right py-2 $highlightClass'>" . number_format($row['statValue'], 2) . "</td>";
                          } else {
                            echo "<td class='text-right py-2 $highlightClass'>" . $row['statValue'] . "</td>";
                          }
                          
                          echo "</tr>";
                        }
                        
                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";
                        echo "</div>";
                      } else {
                        // Show empty card for stats with no playoff data
                        echo "<div class='stat-card'>";
                        echo "<div class='stat-card-header'>";
                        echo "<h3 class='text-xl font-bold text-center text-white'>{$stat[2]}</h3>";
                        echo "</div>";
                        echo "<div class='stat-card-body text-center py-6'>";
                        echo "<p class='empty-stats'>No playoff data available</p>";
                        echo "</div>";
                        echo "</div>";
                      }
                    }
                    ?>
                  </div>
                </div>


              </div>
            </div>

            <!-- TEAM STANDINGS -->

<?php
  // Query standings by conference
  $eastSQL = "SELECT * FROM nhl_EOY_standings WHERE seasonID = $season_id AND conferenceName = 'Eastern' ORDER BY points DESC";
  $westSQL = "SELECT * FROM nhl_EOY_standings WHERE seasonID = $season_id AND conferenceName = 'Western' ORDER BY points DESC";

  $eastResult = mysqli_query($conn, $eastSQL);
  $westResult = mysqli_query($conn, $westSQL);
?>

<h2 class="section-title text-2xl font-bold text-center mb-6 text-white">End of Season Standings</h2>

<div class="flex flex-col md:flex-row gap-4 overflow-x-auto">
  
  <!-- Western Conference -->
  <div class="flex-1">
    <h3 class="text-xl font-semibold mb-2 text-white text-center">Western Conference</h3>
    <table class="default-zebra-table text-white w-full">
      <thead>
        <tr>
          <th>Clinched</th>
          <th>Team</th>
          <th>Points</th>
          <th>GP</th>
          <th>Wins</th>
          <th>Losses</th>
          <th>OTL</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($westResult)) {
          echo "<tr>";
          echo "<td class='text-center'>" . $row['clinchIndicator'] . "</td>";
          echo "<td class='text-center'><a href='team_details.php?team_id=" . $row['teamID'] . "' class='hover:text-blue-300'>" . $row['teamName'] . "</a></td>";
          echo "<td class='text-center'>" . $row['points'] . "</td>";
          echo "<td class='text-center'>" . $row['gp'] . "</td>";
          echo "<td class='text-center'>" . $row['win'] . "</td>";
          echo "<td class='text-center'>" . $row['loss'] . "</td>";
          echo "<td class='text-center'>" . $row['otLoss'] . "</td>";
          echo "</tr>";
        } ?>
      </tbody>
    </table>
  </div>

  <!-- Eastern Conference -->
  <div class="flex-1">
    <h3 class="text-xl font-semibold mb-2 text-white text-center">Eastern Conference</h3>
    <table class="default-zebra-table text-white w-full">
      <thead>
        <tr>
          <th>Clinched</th>
          <th>Team</th>
          <th>Points</th>
          <th>GP</th>
          <th>Wins</th>
          <th>Losses</th>
          <th>OTL</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($eastResult)) {
          echo "<tr>";
          echo "<td class='text-center'>" . $row['clinchIndicator'] . "</td>";
          echo "<td class='text-center'><a href='team_details.php?team_id=" . $row['teamID'] . "' class='hover:text-blue-300'>" . $row['teamName'] . "</a></td>";
          echo "<td class='text-center'>" . $row['points'] . "</td>";
          echo "<td class='text-center'>" . $row['gp'] . "</td>";
          echo "<td class='text-center'>" . $row['win'] . "</td>";
          echo "<td class='text-center'>" . $row['loss'] . "</td>";
          echo "<td class='text-center'>" . $row['otLoss'] . "</td>";
          echo "</tr>";
        } ?>
      </tbody>
    </table>
  </div>
</div> <!-- End of flex container for standing tables -->

  <div class="play-by-play-key max-w-3xl mx-auto p-6 mb-8">
          <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-y-3 gap-x-5">
            <p><strong class="text-blue-300">x</strong> – clinched playoff spot</p>
            <p><strong class="text-blue-300">y</strong> – clinched division</p>
            <p><strong class="text-blue-300">z</strong> – clinched conference</p>
            <p><strong class="text-blue-300">p</strong> – Presidents' Trophy</p>
            <p><strong class="text-blue-300">e</strong> – eliminated from playoffs</p>
          </div>
        </div>






<!-- END STANDINGS -->
          </div>
           
          



      <?php
        } else {
          // If no season ID provided, show a list of available seasons
      ?>
          <div class="container mx-auto px-6 py-10 text-center">
            <h1 class="page-title text-3xl font-bold mb-6">NHL Season Statistics</h1>
            <p class="mb-8 text-lg text-gray-300">Select a season to view statistics:</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-5 max-w-5xl mx-auto mb-10 p-4">
              <?php
              // Generate links for last 25 seasons with alternating blue and slate colors
              $current_year = date("Y");
              for ($i = 0; $i < 25; $i++) {
                $year = $current_year - $i;
                $season_id = ($year - 1) . $year;
                $display = ($year - 1) . "-" . $year;
                
                // Alternate between blue and slate gradients
                if ($i % 2 === 0) {
                  echo "<a href='season_overview.php?season_id=$season_id' class='p-3 rounded font-medium transition-all transform hover:scale-105' style='background: linear-gradient(135deg, #2563eb, #1d4ed8); border: 1px solid rgba(59, 130, 246, 0.5); box-shadow: 0 3px 8px rgba(37, 99, 235, 0.3);'>";
                } else {
                  echo "<a href='season_overview.php?season_id=$season_id' class='p-3 rounded font-medium transition-all transform hover:scale-105' style='background: linear-gradient(135deg, #475569, #334155); border: 1px solid rgba(71, 85, 105, 0.5); box-shadow: 0 3px 8px rgba(51, 65, 85, 0.3);'>";
                }
                echo $display;
                echo "</a>";
              }
              ?>
            </div>

          </div>
      <?php
        }
      ?>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
  </body>
</html>