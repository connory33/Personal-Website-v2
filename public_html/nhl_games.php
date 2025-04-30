<?php include('db_connection.php'); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">
    <title>Connor Young</title>
    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="../resources/css/album.css" rel="stylesheet">
    <link href="/resources/css/default_v3.css" rel="stylesheet" type="text/css" />
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

  </head>
  <body>
<!-- Header -->
<?php include 'header.php'; ?>



        <?php
        ini_set('display_errors', 1); error_reporting(E_ALL);

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            # Set default values for search column and term
            $searchColumn = '';
            $searchTerm = '';
            $originalSearchTerm = '';

            $sortColumnMap = [
                'gameDate' => 'nhl_games.gameDate',
                'home_team_name' => 'home_teams.fullName',
                'away_team_name' => 'away_teams.fullName',
                'home_score' => 'nhl_games.homeScore',
                'away_score' => 'nhl_games.awayScore',
                'game_id' => 'nhl_games.id'
            ];
            
            $requestedSortColumn = $_GET['sort_by'] ?? 'gameDate';
            $sortColumn = isset($sortColumnMap[$requestedSortColumn]) ? $sortColumnMap[$requestedSortColumn] : 'nhl_games.gameDate';
            $sortOrder = (isset($_GET['sort_order']) && strtolower($_GET['sort_order']) === 'asc') ? 'ASC' : 'DESC';
        
            # base query
            $sql = "SELECT
                        nhl_games.*,
                        home_teams.fullName AS home_team_name,
                        home_teams.id AS home_team_id,
                        away_teams.fullName AS away_team_name,
                        away_teams.id AS away_team_id
                    FROM
                        nhl_games
                    JOIN nhl_teams AS home_teams
                        ON nhl_games.homeTeamId = home_teams.id
                    JOIN nhl_teams AS away_teams
                        ON nhl_games.awayTeamId = away_teams.id";

            ### Pagination logic ###
            $limit = 25; // Results per page
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = ($page - 1) * $limit;

            if (!empty($_GET['search_column']) && !empty($_GET['search_term'])) {

                $searchColumn = mysqli_real_escape_string($conn, $_GET['search_column']);
                $searchTerm = mysqli_real_escape_string($conn, $_GET['search_term']);
                $originalSearchTerm = $searchTerm;

                // Get total count (for Load More logic)
                $count_sql = "SELECT COUNT(*) as total FROM (" . preg_replace("/SELECT.+?FROM/", "SELECT 1 FROM", $sql, 1) . ") as count_table";
                $count_result = mysqli_query($conn, $count_sql);
                $total_rows = mysqli_fetch_assoc($count_result)['total'];

                $start = $offset + 1;
                $end = min($offset + $limit, $total_rows);
                $total_pages = ceil($total_rows / $limit);

                # get lowercase version of search term to use in mapping numeric values
                $lowerTerm = strtolower($searchTerm);
                # map and assign new value
                $gameType_duration_map = [
                        'preseason' => 1, 'pre' => 1,
                        'regular season' => 2, 'reg' => 2,
                        'playoffs' => 3, 'postseason' => 3, 'post' => 3,
                        'reg' => 3, 'ot' => 4, 'so' => 5
                    ];
                    if (isset($termMap[$lowerTerm])) {
                        $searchTerm = $termMap[$termLower];
                    }

                // Convert date search term to DB format (YYYY-MM-DD)s
                if ($searchColumn == 'gameDate') { # assuming MM/DD/YY input - BUILD OUT TO MAKE ROBUST TO OTHER INPUTS
                    $year = substr($searchTerm, 6);
                    $month = substr($searchTerm, 0, 2);
                    $day = substr($searchTerm, 3, 2);
                    $searchTerm = $year."-".$month."-".$day;
                }


                // Convert search term to numeric ID values for different teams
                $teamMap = [
                    'anaheim' => 24, 'ducks' => 24, 'anaheim ducks' => 24, 'ana' => 24,
                    'arizona' => 53, 'coyotes' => 53, 'arizona coyotes' => 53, 'ari' => 53,
                    'boston' => 6, 'bruins' => 6, 'boston bruins' => 6, 'bos' => 6,
                    'buffalo' => 7, 'sabres' => 7, 'buffalo sabres' => 7, 'buf' => 7,
                    'calgary' => 20, 'flames' => 20, 'calgary flames' => 20, 'cgy' => 20,
                    'carolina' => 12, 'hurricanes' => 12, 'carolina hurricanes' => 12, 'car' => 12,
                    'chicago' => 16, 'blackhawks' => 16, 'chicago blackhawks' => 16, 'chi' => 16,
                    'colorado' => 21, 'avalanche' => 21, 'colorado avalanche' => 21, 'col' => 21,
                    'columbus' => 29, 'blue jackets' => 29, 'columbus blue jackets' => 29, 'cbj' => 29,
                    'dallas' => 25, 'stars' => 25, 'dallas stars' => 25, 'dal' => 25,
                    'detroit' => 17, 'red wings' => 17, 'detroit red wings' => 17, 'det' => 17,
                    'edmonton' => 22, 'oilers' => 22, 'edmonton oilers' => 22, 'edm' => 22,
                    'florida' => 13, 'panthers' => 13, 'florida panthers' => 13, 'fla' => 13,
                    'los angeles' => 26, 'kings' => 26, 'los angeles kings' => 26, 'lak' => 26,
                    'minnesota' => 30, 'wild' => 30, 'minnesota wild' => 30, 'min' => 30,
                    'montreal' => 8, 'canadiens' => 8, 'montreal canadiens' => 8, 'mon' => 8,
                    'nashville' => 18, 'predators' => 18, 'nashville predators' => 18, 'nas' => 18,
                    'new jersey' => 1, 'devils' => 1, 'new jersey devils' => 1, 'njd' => 1,
                    'islanders' => 2, 'new york islanders' => 2, 'nyi' => 2,
                    'rangers' => 2, 'new york rangers' => 3, 'nyr' => 3,
                    'ottawa' => 9, 'senators' => 9, 'ottawa senators' => 9, 'ott' => 9,
                    'philadelphia' => 4, 'flyers' => 4, 'philadelphia flyers' => 4, 'phi' => 4,
                    'pittsburgh' => 5, 'penguins' => 5, 'pittsburgh penguins' => 5, 'pit' => 5,
                    'san jose' => 28, 'sharks' => 28, 'san jose sharks' => 28, 'sjs' => 28,
                    'seattle' => 55, 'kraken' => 55, 'seattle kraken' => 55, 'sea' => 55,
                    'st. louis' => 19, 'blues' => 19, 'st. louis blues' => 19, 'stl' => 19,
                    'tampa bay' => 14, 'lightning' => 14, 'tampa bay lightning' => 14, 'tbl' => 14,
                    'toronto' => 10, 'maple leafs' => 10, 'toronto maple leafs' => 10, 'tor' => 10,
                    'vancouver' => 23, 'canucks' => 23, 'vancouver canucks' => 23, 'van' => 23,
                    'las vegas' => 5, 'vegas' => 5, 'golden knights' => 5, 'vegas golden knights' => 5, 'vgk' => 5,
                    'washington' => 15, 'capitals' => 15, 'washington capitals' => 15, 'wsh' => 15,
                    'winnipeg' => 52, 'jets' => 52, 'winnipeg jets' => 52, 'wpg' => 52
                ];
                $lowerTerm = strtolower($searchTerm);
                if (isset($teamMap[$lowerTerm])) {
                    $searchTerm = $teamMap[$lowerTerm];
                }

                // Add SQL WHERE clause based on search column and term
                if ($searchColumn === "team") {
                    $sql .= " WHERE home_teams.id = '$searchTerm' OR away_teams.id = '$searchTerm'";
                } else {
                    $sql .= " WHERE $searchColumn LIKE '%$searchTerm%'";
                }
                

                // Date range filter
                if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
                    $startDate = $_GET['startDate'];
                    $endDate = $_GET['endDate'];
                    $sql .= (strpos($sql, 'WHERE') !== false ? " AND" : " WHERE") . " gameDate BETWEEN '$startDate' AND '$endDate'";
                }
                

                // Add "counting" query to get total number of result rows independent of pagination limit
                // Do this BEFORE adding ORDER BY and LIMIT clauses to the main query
                $count_query = "SELECT COUNT(*) as total
                FROM nhl_games
                JOIN nhl_teams AS home_teams ON nhl_games.homeTeamId = home_teams.id
                JOIN nhl_teams AS away_teams ON nhl_games.awayTeamId = away_teams.id";
                // Apply same WHERE clause
                $where_clauses = [];
                if ($searchColumn === "team") {
                    $where_clauses[] = "(home_teams.id = '$searchTerm' OR away_teams.id = '$searchTerm')";
                } else {
                    $where_clauses[] = "$searchColumn LIKE '%$searchTerm%'";
                }
                if (!empty($_GET['startDate']) && !empty($_GET['endDate'])) {
                    $startDate = $_GET['startDate'];
                    $endDate = $_GET['endDate'];
                    $where_clauses[] = "gameDate BETWEEN '$startDate' AND '$endDate'";
                }

                if (!empty($where_clauses)) {
                    $count_query .= " WHERE " . implode(" AND ", $where_clauses);
                }
                $count_result = mysqli_query($conn, $count_query) or die("Count query failed: " . mysqli_error($conn));
                $total_rows = mysqli_fetch_assoc($count_result)['total'] ?? 0;

                // Add order and limit clauses
                $sql .= " ORDER BY $sortColumn $sortOrder";
                $sql .= " LIMIT $limit OFFSET $offset";

                // Execute and check query
                $result = mysqli_query($conn, $sql) or die("Query failed: " . mysqli_error($conn));

                if (!$result) {
                    die("Query failed: " . mysqli_error($conn));
                }
                
                ?>

            <div id="nhl-games-players-summary-content-container" class='bg-dark'>
                <br>
                <?php echo "<h5 style='text-align: center'>" . $total_rows . " results found where " . $searchColumn . " = '" . $originalSearchTerm . "'</h5><br>"; ?>

                    <p class="text-lg text-center">Search again:</p>
                    <div class="flex justify-center">
                        <form id='nhl-search' method="GET" action="nhl_games.php"
                            class="backdrop-blur-sm px-4 sm:px-6 py-4 rounded-lg flex flex-col sm:flex-row gap-4 items-stretch sm:items-center w-full max-w-4xl nhl-search-column">
                
                        <!-- Dropdown -->
                        <select name="search_column" id='nhl-search-column' class="w-full sm:w-auto flex-1 bg-white text-black text-sm rounded-md border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="season">Season</option>
                            <option value="gameDate">Game Date</option>
                            <option value="easternStartTime">Start Time</option>
                            <option value="gameType">Game Type</option>
                            <option value="team">Team</option>
                            <option value="homeTeamId">Home Team</option>
                            <option value="awayTeamId">Away Team</option>
                            <option value="player">Player Name</option>
                        </select>

                        <!-- Text input -->
                        <input type="text" name="search_term" id="search-term" placeholder="Enter search term" required
                            class="w-full sm:flex-2 text-black px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        <!-- Submit button -->
                        <input type="submit" value="Search"
                            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-md transition-colors duration-200 cursor-pointer">
                        </form>
                    </div>

                <!-- Display results in a table format -->
                <p>Click on any team name or game ID to view additional details about the player or game.</p><br>
                <div class="table-container shadow-md rounded-lg overflow-x-auto mx-auto">
                        <!-- Search Filter Fields -->
                    <div class="flex mx-auto mb-4">
                    <h2 class="text-4xl font-bold text-white text-center">Game Results</h2><br>
                    <input type="text" id="searchByTeam" class="filter-input border rounded px-3 py-2 text-black" style='border: 2px solid #1F2833' placeholder="Search by Team">
            </div>

                    <!-- Table -->
                    <table id='games-players-summary-table' class="min-w-max table-auto default-zebra-table">
                        <colgroup>
                        <col class="games-players-summary-col-season">
                        <col class="games-players-summary-col-gameNumber">
                        <col class="games-players-summary-col-date">
                        <col class="games-players-summary-col-startTime">
                        <col class="games-players-summary-col-gameType">
                        <col class="games-players-summary-col-homeTeam">
                        <col class="games-players-summary-col-homeScore">
                        <col class="games-players-summary-col-awayTeam">
                        <col class="games-players-summary-col-awayScore">
                        <col class="games-players-summary-col-id">
                        </colgroup>
                        <thead class='default-zebra-table'>
                            <tr class='default-zebra-table'>
                                <th>Season<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=season&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=season&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Game #<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameNumber&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameNumber&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Date<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameDate&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameDate&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Start (EST)<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=easternStartTime&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=easternStartTime&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Game Type<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameType&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=gameType&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Home Team<br>
                                    <span class='sort-arrows'>
                                        <a href='?search_column=" . urlencode($searchColumn) . "&search_term=" . urlencode($searchTerm)
                                        . "&sort_by=home_team_name&sort_order=asc'>△</a><a href='?search_column=" . urlencode($searchColumn)
                                        . "&search_term=" . urlencode($searchTerm)
                                        . "&sort_by=home_team_name&sort_order=desc'>▽</a>
                                    </span>
                                </th>
                                <th>Home Score<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=homeScore&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=homeScore&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Away Team<br>
                                    <span class='sort-arrows'>
                                        <a href='?search_column=" . urlencode($searchColumn) . "&search_term=" . urlencode($searchTerm)
                                        . "&sort_by=away_team_name&sort_order=asc' class="text-xs">△</a><a href='?search_column=" . urlencode($searchColumn)
                                        . "&search_term=" . urlencode($searchTerm)
                                        . "&sort_by=away_team_name&sort_order=desc' class="text-xs">▽</a>
                                    </span>
                                </th>
                                <th>Away Score<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=awayScore&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=awayScore&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                                <th>Game ID<br>
                                <span class='sort-arrows'>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=id&sort_order=asc' class="text-xs">△</a>
                                    <a href='?search_column=<?= urlencode($searchColumn) ?>&search_term=<?= urlencode($searchTerm) ?>&sort_by=id&sort_order=desc' class="text-xs">▽</a>
                                </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                    <?php
                    while ($row = $result->fetch_assoc()){
                        echo "<tr>";

                        # Season
                        $formatted_season_1 = substr($row['season'], 0, 4);
                        $formatted_season_2 = substr($row['season'], 4);
                        // echo "<td>".htmlspecialchars($formatted_season_1)."-".htmlspecialchars($formatted_season_2)."</td>";
                        echo "<td><a href='playoff_results.php?season_id=" . htmlspecialchars($formatted_season_1) . htmlspecialchars($formatted_season_2) . "'>" 
                        . htmlspecialchars($formatted_season_1) . "-" . htmlspecialchars($formatted_season_2) . "</a></td>";

                        
                        # Game Number
                        echo "<td>".$row['gameNumber']."</td>";

                        # Date
                        $gameDate = $row['gameDate'];
                        $gameDatetime = new DateTime($gameDate);
                        $formatted_gameDate = $gameDatetime->format('m/d/Y');
                        echo "<td>".htmlspecialchars($formatted_gameDate)."</td>";

                        # Time
                        $formatted_startTime = substr($row['easternStartTime'], 11, -3);
                        echo "<td>".htmlspecialchars($formatted_startTime)."</td>";

                        # Game Type (i.e. Preseason, Regular Season, etc.)
                        $gameType_num = $row['gameType'];
                        if ($gameType_num == 1) {
                            $gameType_text = "Pre.";
                        } elseif ($gameType_num == 2) {
                            $gameType_text = "Reg.";
                        } elseif ($gameType_num == 3) {
                            $gameType_text = "Post.";
                        } else {
                            $gameType_text = "Unknown";
                        }
                        echo "<td>".$gameType_text."</td>";

                        # Home Team
                        if ($row['homeScore']>$row['awayScore']) {
                            echo "<td style='font-weight: bold'><a href='team_details.php?team_id={$row['home_team_id']}'>" . htmlspecialchars($row['home_team_name']) . "</a></td>";
                            // echo "<td style='font-weight: bold'>".$row['home_team_name']."</td>";
                            echo "<td style='font-weight: bold'>".$row['homeScore']."</td>";
                        } else {
                            echo "<td><a href='team_details.php?team_id={$row['home_team_id']}'>" . htmlspecialchars($row['home_team_name']) . "</a></td>";
                            echo "<td>".$row['homeScore']."</td>";
                        }
                        
                        # Away Team
                        if ($row['homeScore']<$row['awayScore']) {
                            echo "<td style='font-weight: bold'><a href='team_details.php?team_id={$row['away_team_id']}'>" . htmlspecialchars($row['away_team_name']) . "</a></td>";
                            echo "<td style='font-weight: bold'>".$row['awayScore']."</td>";
                        } else {
                            echo "<td><a href='team_details.php?team_id={$row['away_team_id']}'>" . htmlspecialchars($row['away_team_name']) . "</a></td>";
                            echo "<td>".$row['awayScore']."</td>";
                        }

                        # Game ID
                        echo "<td><a href='game_details.php?game_id=" . $row['id'] . "'>" . $row['id'] . "</a></td>";

                        echo "</tr>";
                    }
                    
                        echo "</tbody>";
                    echo "</table>";
                echo "</div>";

            
                $total_pages = ceil($total_rows / $limit);
                
                if ($total_pages > 1) {
                    echo "<div style='text-align:center; margin-top: 20px;'>";
                
                    // Previous button
                    if ($page > 1) {
                        $prev_page = http_build_query(array_merge($_GET, ['page' => $page - 1]));
                        echo "<a class='btn btn-secondary' href='?" . $prev_page . "' style='margin-right: 5px'>Previous</a>";
                    }
                
                    // Numbered page buttons (e.g., 1 2 3 4 5)
                    $range = 2; // how many pages to show on each side
                    $start = max(1, $page - $range);
                    $end = min($total_pages, $page + $range);
                
                    for ($i = $start; $i <= $end; $i++) {
                        $page_query = http_build_query(array_merge($_GET, ['page' => $i]));
                        $btn_class = $i == $page ? 'btn btn-primary' : 'btn btn-secondary';
                        echo "<a class='$btn_class' href='?$page_query' style='margin: 0 2px;'>$i</a>";
                    }
                
                    // Next button
                    if ($page < $total_pages) {
                        $next_page = http_build_query(array_merge($_GET, ['page' => $page + 1]));
                        echo "<a class='btn btn-secondary' href='?" . $next_page . "' style='margin-left: 5px'>Next</a>";
                    }
                
                    echo "</div>";
                }
            
                
    
               

                if ($page==1) {
                    $next_page = $page + 1;
                    $advance_page = http_build_query(array_merge($_GET, ['page' => $next_page]));
                    echo "<div><a class='btn btn-secondary' href='?" . $advance_page . "'>Next</a>
                        </div>";
                } else if ($page>1 and $page<$total_pages) {
                    $prev_page = $page - 1;
                    $next_page = $page + 1;
                    $prev_page = http_build_query(array_merge($_GET, ['page' => $prev_page]));
                    $advance_page = http_build_query(array_merge($_GET, ['page' => $next_page]));
                    echo "<div style='text-align:center; margin-top: 20px;'>
                        <a class='btn btn-secondary' href='?" . $prev_page . "' style='margin-right: 10px'>Previous</a>";
                    echo "<a class='btn btn-secondary' href='?" . $advance_page . "'>Next</a>
                        </div>";
                } else {
                    $prev_page = $page - 1;
                    echo "<div style='text-align:center; margin-top: 20px;'>
                        <a class='btn btn-secondary' href='?" . $prev_page . "'>Previous</a></div>";
                }


                    

                echo "</div>";
                
                
        
            }


            $conn->close();
        }
        ?>

    <br>
    <br>
    </div>
    

    <?php include 'footer.php'; ?>


    <!-- JS for pagination -->
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector("#games-players-summary-table tbody");
    const searchByPlayer = document.getElementById("searchByPlayer");
    const searchByTeam = document.getElementById("searchByTeam");
    const pagination = document.getElementById("pagination");

    let currentPage = 1;
    const pageSize = 50;
    let allData = [];       // All raw data
    let filteredData = [];  // Filtered data to be paginated

    // Fetch the initial table data from the DOM
    function loadDataFromDOM() {
        const rows = document.querySelectorAll("#games-players-summary-table tbody tr");
        rows.forEach(row => {
            const cells = row.querySelectorAll("td");
            allData.push({
                playerName: cells[0].innerText.trim(),
                playerID: cells[0].querySelector("a")?.href.split("=").pop(),
                shiftNumber: cells[1].innerText.trim(),
                period: cells[2].innerText.trim(),
                startTime: cells[3].innerText.trim(),
                endTime: cells[4].innerText.trim(),
                duration: cells[5].innerText.trim(),
                teamTricode: cells[6].innerText.trim(),
                eventDescription: cells[7].innerText.trim()
            });
        });
        filteredData = [...allData];
        renderTable(filteredData);
        // renderPagination(filteredData);
    }

    // Render table page
    function renderTable(data) {
        tableBody.innerHTML = "";
        const start = (currentPage - 1) * pageSize;
        const end = start + pageSize;
        const page = data.slice(start, end);

        page.forEach(row => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td><a href="player_details.php?team_id=${row.playerID}">${row.playerName}</a></td>
                <td>${row.shiftNumber}</td>
                <td>${row.period}</td>
                <td>${row.startTime}</td>
                <td>${row.endTime}</td>
                <td>${row.duration}</td>
                <td>${row.teamTricode}</td>
                <td>${row.eventDescription}</td>
            `;
            tableBody.appendChild(tr);
        });
    }


    // Combined render
    function updateTableAndPagination() {
        renderTable(filteredData);
        // renderPagination(filteredData);
    }



    // Filter handler
    function applyFilters() {
        const playerFilter = searchByPlayer.value.toLowerCase();
        const teamFilter = searchByTeam.value.toLowerCase();

        filteredData = allData.filter(row => {
            const matchPlayer = row.playerName.toLowerCase().includes(playerFilter);
            const matchTeam = row.teamTricode.toLowerCase().includes(teamFilter);
            return matchPlayer && matchTeam;
        });

        currentPage = 1;
        updateTableAndPagination();
    }

    // Add input event listeners
    searchByPlayer.addEventListener("input", applyFilters);
    searchByTeam.addEventListener("input", applyFilters);

    // Init on page load
    loadDataFromDOM();
});
</script>



    <!-- JS for search filter on table -->
    <script>
document.addEventListener("DOMContentLoaded", function () {
    // const searchByPlayerInput = document.getElementById("searchByPlayer");
    const searchByTeamInput = document.getElementById("searchByTeam");
    const table = document.getElementById("games-players-summary-table");
    const rows = table.querySelectorAll("tbody tr");

    function filterTable() {
        // const playerFilter = searchByPlayerInput.value.toLowerCase();
        const teamFilter = searchByTeamInput.value.toLowerCase();

        rows.forEach(row => {
            const playerText = row.innerText.toLowerCase();
            const homeTeam = row.children[5]?.innerText.toLowerCase() || ""; // Home team column
            const awayTeam = row.children[7]?.innerText.toLowerCase() || ""; // Away team column

            // const matchesPlayer = !playerFilter || playerText.includes(playerFilter);
            const matchesTeam = !teamFilter || homeTeam.includes(teamFilter) || awayTeam.includes(teamFilter);

            if (matchesTeam) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // searchByPlayerInput.addEventListener("input", filterTable);
    searchByTeamInput.addEventListener("input", filterTable);
});
</script>


  </body>
</html>