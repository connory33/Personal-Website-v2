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
    
    <!-- Updated tabs functionality to default to Skaters tab -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Set up tab switching for Regular Season/Playoffs
        const tabs = document.querySelectorAll('[data-tab-target]');
        const tabContents = document.querySelectorAll('[data-tab-content]');
        
        tabs.forEach(tab => {
          tab.addEventListener('click', () => {
            const target = document.querySelector(tab.dataset.tabTarget);
            
            // Hide all tab contents
            tabContents.forEach(content => {
              content.classList.add('hidden');
            });
            
            // Deactivate all tabs
            tabs.forEach(t => {
              t.classList.remove('bg-blue-600');
              t.classList.add('bg-gray-700');
            });
            
            // Show the selected tab content
            target.classList.remove('hidden');
            
            // Activate the selected tab
            tab.classList.remove('bg-gray-700');
            tab.classList.add('bg-blue-600');
            
            // Auto-select the skaters tab when changing between regular season and playoffs
            if (tab.dataset.tabTarget === '#regular-season') {
              document.querySelector('[data-category-tab-target="#skaters-content"]').click();
            } else if (tab.dataset.tabTarget === '#playoffs') {
              document.querySelector('[data-category-tab-target="#skaters-playoffs"]').click();
            }
          });
        });

        // Set up category tab switching (Skaters/Goalies)
        const categoryTabs = document.querySelectorAll('[data-category-tab-target]');
        const categoryContents = document.querySelectorAll('[data-category-content]');
        
        categoryTabs.forEach(tab => {
          tab.addEventListener('click', () => {
            const target = document.querySelector(tab.dataset.categoryTabTarget);
            
            // Hide all category contents
            categoryContents.forEach(content => {
              content.classList.add('hidden');
            });
            
            // Deactivate all category tabs
            categoryTabs.forEach(t => {
              t.classList.remove('bg-blue-600');
              t.classList.add('bg-gray-700');
            });
            
            // Show the selected category content
            target.classList.remove('hidden');
            
            // Activate the selected tab
            tab.classList.remove('bg-gray-700');
            tab.classList.add('bg-blue-600');
          });
        });
        
        // Initialize with regular season tab active and skaters tab active
        document.querySelector('[data-tab-target="#regular-season"]').click();
        
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

  <body class="flex flex-col min-h-screen" style='background-color: #343a40'>
    
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
          
          // Format season for display (e.g. 20212022 -> 2021-2022)
          $formatted_season = substr($season_id, 0, 4) . '-' . substr($season_id, 4);
      ?>
          <div class="container mx-auto px-4 py-6">
            <h1 class="text-3xl font-bold text-center mt-4 mb-6"><?php echo $formatted_season ?> Season Overview</h1>

            <!-- Season navigation with dropdown -->
            <div class="flex justify-center mb-6">
              <div class="w-full max-w-xs">
                <label for="season-select" class="block text-sm font-medium mb-1">Change Season</label>
                <select id="season-select" class="bg-gray-700 text-white py-2 px-4 rounded w-full cursor-pointer hover:bg-gray-600 transition-colors">
                  <option value="">Select Season</option>
                  <?php
                  // Generate options for last 25 seasons
                  $current_year = date("Y");
                  for ($i = 0; $i < 25; $i++) {
                    $year = $current_year - $i;
                    $option_season_id = ($year - 1) . $year;
                    $option_display = ($year - 1) . "-" . $year;
                    $selected = ($option_season_id == $season_id) ? 'selected' : '';
                    echo "<option value='$option_season_id' $selected>$option_display</option>";
                  }
                  ?>
                </select>
              </div>
            </div>
            
                <?php
                $sql = "SELECT team_awards.*, nhl_teams.fullName FROM team_awards LEFT JOIN nhl_teams ON team_awards.stanleyCupChampsID=nhl_teams.id WHERE seasonID = $season_id";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                $stanleyCupChamp = $row['fullName'];

                ?>


            <!-- Additional season information -->
            <div class="mt-8 bg-gray-800 rounded-lg p-4 mb-10">
              <h2 class="text-2xl font-bold mb-4">Season Summary</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <h3 class="text-xl mb-2">Stanley Cup Champion: <?php echo $stanleyCupChamp ?> </h3>
                  <?php
                  // You would need to add this data to your database and query it
                  echo "<a class='text-blue-500 hover:underline' href='https://connoryoung.com/playoff_results.php?season_id=" . $season_id . "'>View Playoff Results</a>";
                  ?>
                </div>
                <div>
                  <h3 class="text-xl mb-2">Awards</h3>
                  <?php
                  // You would need to add this data to your database and query it
                  echo "<p>League awards information would go here</p>";
                  ?>
                </div>
              </div>
            </div>
            
            <!-- Tab navigation for Regular Season/Playoffs -->
            <div class="flex justify-center mb-4 border-b border-gray-600">
              <button data-tab-target="#regular-season" class="px-6 py-3 font-medium text-lg bg-gray-700 rounded-t">Regular Season</button>
              <button data-tab-target="#playoffs" class="px-6 py-3 font-medium text-lg bg-gray-700 rounded-t">Playoffs</button>
            </div>
            
            <!-- Regular Season Content -->
            <div id="regular-season" data-tab-content class="hidden">
              <!-- Category tabs for Skaters/Goalies -->
              <div class="flex justify-center mb-4 border-b border-gray-600">
                <button data-category-tab-target="#skaters-content" class="px-6 py-2 font-medium bg-gray-700 rounded-t">Skaters</button>
                <button data-category-tab-target="#goalies-content" class="px-6 py-2 font-medium bg-gray-700 rounded-t">Goalies</button>
              </div>
              
              <!-- Skaters Content -->
              <div id="skaters-content" data-category-content class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
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
                      echo "<div class='bg-gray-800 rounded-lg overflow-hidden shadow-lg'>";
                      echo "<div class='bg-gray-700 px-4 py-2'>";
                      echo "<h3 class='text-xl font-bold text-center'>{$stat[2]}</h3>";
                      echo "</div>";
                      echo "<div class='p-4'>";
                      echo "<table class='w-full'>";
                      echo "<thead class='border-b border-gray-600'>";
                      echo "<tr>";
                      echo "<th class='text-left pb-2'>Player</th>";
                      echo "<th class='text-right pb-2'>{$stat[1]}</th>";
                      echo "</tr>";
                      echo "</thead>";
                      echo "<tbody>";
                      
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='border-b border-gray-700'>";
                        echo "<td class='py-2'><a href='player_details.php?player_id={$row['playerID']}' class='hover:text-blue-300'>{$row['firstName']} {$row['lastName']}</a></td>";
                        
                        // Format value based on stat type
                        if ($stat[0] == 'toi') {
                          echo "<td class='text-right py-2'>" . number_format($row['statValue'], 1) . "</td>";
                        } elseif ($stat[0] == 'faceoffLeaders') {
                          echo "<td class='text-right py-2'>" . number_format($row['statValue']*100, 1) . "%</td>";
                        } else {
                          echo "<td class='text-right py-2'>" . $row['statValue'] . "</td>";
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
              
              <!-- Goalies Content -->
              <div id="goalies-content" data-category-content class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
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
                      echo "<div class='bg-gray-800 rounded-lg overflow-hidden shadow-lg'>";
                      echo "<div class='bg-gray-700 px-4 py-2'>";
                      echo "<h3 class='text-xl font-bold text-center'>{$stat[2]}</h3>";
                      echo "</div>";
                      echo "<div class='p-4'>";
                      echo "<table class='w-full'>";
                      echo "<thead class='border-b border-gray-600'>";
                      echo "<tr>";
                      echo "<th class='text-left pb-2'>Player</th>";
                      echo "<th class='text-right pb-2'>{$stat[1]}</th>";
                      echo "</tr>";
                      echo "</thead>";
                      echo "<tbody>";
                      
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='border-b border-gray-700'>";
                        echo "<td class='py-2'><a href='player_details.php?player_id={$row['playerID']}' class='hover:text-blue-300'>{$row['firstName']} {$row['lastName']}</a></td>";
                        
                        // Format value based on stat type
                        if ($stat[0] == 'savePctg') {
                          echo "<td class='text-right py-2'>" . number_format($row['statValue'], 3) . "</td>";
                        } elseif ($stat[0] == 'goalsAgainstAverage') {
                          echo "<td class='text-right py-2'>" . number_format($row['statValue'], 2) . "</td>";
                        } else {
                          echo "<td class='text-right py-2'>" . $row['statValue'] . "</td>";
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
            <div id="playoffs" data-tab-content class="hidden">
              <!-- Category tabs for Skaters/Goalies -->
              <div class="flex justify-center mb-4 border-b border-gray-600">
                <button data-category-tab-target="#skaters-playoffs" class="px-6 py-2 font-medium bg-gray-700 rounded-t">Skaters</button>
                <button data-category-tab-target="#goalies-playoffs" class="px-6 py-2 font-medium bg-gray-700 rounded-t">Goalies</button>
              </div>
              
              <!-- Skaters Playoffs Content -->
              <div id="skaters-playoffs" data-category-content class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
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
                      echo "<div class='bg-gray-800 rounded-lg overflow-hidden shadow-lg'>";
                      echo "<div class='bg-gray-700 px-4 py-2'>";
                      echo "<h3 class='text-xl font-bold text-center'>{$stat[2]}</h3>";
                      echo "</div>";
                      echo "<div class='p-4'>";
                      echo "<table class='w-full'>";
                      echo "<thead class='border-b border-gray-600'>";
                      echo "<tr>";
                      echo "<th class='text-left pb-2'>Player</th>";
                      echo "<th class='text-right pb-2'>{$stat[1]}</th>";
                      echo "</tr>";
                      echo "</thead>";
                      echo "<tbody>";
                      
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='border-b border-gray-700'>";
                        echo "<td class='py-2'><a href='player_details.php?player_id={$row['playerID']}' class='hover:text-blue-300'>{$row['firstName']} {$row['lastName']}</a></td>";
                        
                        // Format value based on stat type
                        if ($stat[0] == 'toi') {
                          echo "<td class='text-right py-2'>" . number_format($row['statValue'], 1) . "</td>";
                        } elseif ($stat[0] == 'faceoffLeaders') {
                          echo "<td class='text-right py-2'>" . number_format($row['statValue']*100, 1) . "%</td>";
                        } else {
                          echo "<td class='text-right py-2'>" . $row['statValue'] . "</td>";
                        }
                        
                        echo "</tr>";
                      }
                      
                      echo "</tbody>";
                      echo "</table>";
                      echo "</div>";
                      echo "</div>";
                    } else {
                      // Show empty card for stats with no playoff data
                      echo "<div class='bg-gray-800 rounded-lg overflow-hidden shadow-lg'>";
                      echo "<div class='bg-gray-700 px-4 py-2'>";
                      echo "<h3 class='text-xl font-bold text-center'>{$stat[2]}</h3>";
                      echo "</div>";
                      echo "<div class='p-4 text-center text-gray-400'>";
                      echo "No playoff data available";
                      echo "</div>";
                      echo "</div>";
                    }
                  }
                  ?>
                </div>
              </div>
              
              <!-- Goalies Playoffs Content -->
              <div id="goalies-playoffs" data-category-content class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
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
                      echo "<div class='bg-gray-800 rounded-lg overflow-hidden shadow-lg'>";
                      echo "<div class='bg-gray-700 px-4 py-2'>";
                      echo "<h3 class='text-xl font-bold text-center'>{$stat[2]}</h3>";
                      echo "</div>";
                      echo "<div class='p-4'>";
                      echo "<table class='w-full'>";
                      echo "<thead class='border-b border-gray-600'>";
                      echo "<tr>";
                      echo "<th class='text-left pb-2'>Player</th>";
                      echo "<th class='text-right pb-2'>{$stat[1]}</th>";
                      echo "</tr>";
                      echo "</thead>";
                      echo "<tbody>";
                      
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr class='border-b border-gray-700'>";
                        echo "<td class='py-2'><a href='player_details.php?player_id={$row['playerID']}' class='hover:text-blue-300'>{$row['firstName']} {$row['lastName']}</a></td>";
                        
                        // Format value based on stat type
                        if ($stat[0] == 'savePctg') {
                          echo "<td class='text-right py-2'>" . number_format($row['statValue'], 3) . "</td>";
                        } elseif ($stat[0] == 'goalsAgainstAverage') {
                          echo "<td class='text-right py-2'>" . number_format($row['statValue'], 2) . "</td>";
                        } else {
                          echo "<td class='text-right py-2'>" . $row['statValue'] . "</td>";
                        }
                        
                        echo "</tr>";
                      }
                      
                      echo "</tbody>";
                      echo "</table>";
                      echo "</div>";
                      echo "</div>";
                    } else {
                      // Show empty card for stats with no playoff data
                      echo "<div class='bg-gray-800 rounded-lg overflow-hidden shadow-lg'>";
                      echo "<div class='bg-gray-700 px-4 py-2'>";
                      echo "<h3 class='text-xl font-bold text-center'>{$stat[2]}</h3>";
                      echo "</div>";
                      echo "<div class='p-4 text-center text-gray-400'>";
                      echo "No playoff data available";
                      echo "</div>";
                      echo "</div>";
                    }
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
            
      <?php
        } else {
          // If no season ID provided, show a list of available seasons
      ?>
          <div class="container mx-auto px-4 py-6 text-center">
            <h1 class="text-3xl font-bold mb-6">NHL Season Statistics</h1>
            <p class="mb-4">Select a season to view statistics:</p>
            
            <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 max-w-3xl mx-auto mb-10">
              <?php
              // Generate links for last 25 seasons (adjust as needed)
              $current_year = date("Y");
              for ($i = 0; $i < 25; $i++) {
                $year = $current_year - $i;
                $season_id = ($year - 1) . $year;
                $display = ($year - 1) . "-" . $year;
                echo "<a href='season_overview.php?season_id=$season_id' class='bg-gray-700 hover:bg-gray-600 p-3 rounded'>";
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