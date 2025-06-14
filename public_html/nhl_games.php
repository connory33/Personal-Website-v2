<?php include('db_connection.php'); ?>
<?php
// Handle AJAX filter requests
if (isset($_GET['ajax_filter']) && $_GET['ajax_filter'] === 'true') {
    header('Content-Type: application/json');
    
    // Build query with all filters
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
        ON nhl_games.awayTeamId = away_teams.id
    WHERE 1=1";
    
    // Add filters for each field
    if (!empty($_GET['season'])) {
        $season = mysqli_real_escape_string($conn, $_GET['season']);
        $sql .= " AND nhl_games.season LIKE '%$season%'";
    }
    
    if (!empty($_GET['gameDate'])) {
        $gameDate = mysqli_real_escape_string($conn, $_GET['gameDate']);
        $sql .= " AND nhl_games.gameDate LIKE '%$gameDate%'";
    }
    
    if (!empty($_GET['startTime'])) {
        $startTime = mysqli_real_escape_string($conn, $_GET['startTime']);
        $sql .= " AND nhl_games.easternStartTime LIKE '%$startTime%'";
    }
    
    if (!empty($_GET['gameType'])) {
        $gameType = mysqli_real_escape_string($conn, $_GET['gameType']);
        if ($gameType == "Pre." || $gameType == "pre") {
            $sql .= " AND nhl_games.gameType = 1";
        } else if ($gameType == "Reg." || $gameType == "reg") {
            $sql .= " AND nhl_games.gameType = 2";
        } else if ($gameType == "Post." || $gameType == "post") {
            $sql .= " AND nhl_games.gameType = 3";
        } else {
            $sql .= " AND nhl_games.gameType LIKE '%$gameType%'";
        }
    }
    
    if (!empty($_GET['homeTeam'])) {
        $homeTeam = mysqli_real_escape_string($conn, $_GET['homeTeam']);
        $sql .= " AND home_teams.fullName LIKE '%$homeTeam%'";
    }
    
    if (!empty($_GET['awayTeam'])) {
        $awayTeam = mysqli_real_escape_string($conn, $_GET['awayTeam']);
        $sql .= " AND away_teams.fullName LIKE '%$awayTeam%'";
    }
    
    // Add search term from main search if it exists
    if (!empty($_GET['search_column']) && !empty($_GET['search_term'])) {
        $searchColumn = mysqli_real_escape_string($conn, $_GET['search_column']);
        $searchTerm = mysqli_real_escape_string($conn, $_GET['search_term']);
        
        if ($searchColumn === "team") {
            $sql .= " AND (home_teams.id = '$searchTerm' OR away_teams.id = '$searchTerm')";
        } else {
            $sql .= " AND $searchColumn LIKE '%$searchTerm%'";
        }
    }
    
    // Add sorting
    $sortColumnMap = [
        'gameDate' => 'nhl_games.gameDate',
        'home_team_name' => 'home_teams.fullName',
        'away_team_name' => 'away_teams.fullName',
        'homeScore' => 'nhl_games.homeScore',
        'awayScore' => 'nhl_games.awayScore',
        'id' => 'nhl_games.id',
        'season' => 'nhl_games.season',
        'gameNumber' => 'nhl_games.gameNumber',
        'easternStartTime' => 'nhl_games.easternStartTime',
        'gameType' => 'nhl_games.gameType'
    ];
    
    $requestedSortColumn = $_GET['sort_by'] ?? 'id';
    $sortColumn = isset($sortColumnMap[$requestedSortColumn]) ? $sortColumnMap[$requestedSortColumn] : 'nhl_games.id';
    $sortOrder = (isset($_GET['sort_order']) && strtolower($_GET['sort_order']) === 'asc') ? 'ASC' : 'DESC';
    
    $sql .= " ORDER BY $sortColumn $sortOrder";
    
    // Count total matching records
    $count_query = "SELECT COUNT(*) as total FROM ($sql) as subquery";
    $count_result = mysqli_query($conn, $count_query) or die("Count query failed: " . mysqli_error($conn));
    $total = mysqli_fetch_assoc($count_result)['total'] ?? 0;
    
    // Add pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $recordsPerPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 50;
    $offset = ($page - 1) * $recordsPerPage;
    
    $sql .= " LIMIT $recordsPerPage OFFSET $offset";
    
    $result = mysqli_query($conn, $sql) or die("Query failed: " . mysqli_error($conn));
    $games = [];
    
    while ($row = $result->fetch_assoc()) {
        // Format data
        $formatted_season_1 = substr($row['season'], 0, 4);
        $formatted_season_2 = substr($row['season'], 4);
        $formatted_season = $formatted_season_1 . "-" . $formatted_season_2;
        
        $gameDate = $row['gameDate'];
        $gameDatetime = new DateTime($gameDate);
        $formatted_gameDate = $gameDatetime->format('m/d/Y');
        
        $formatted_startTime = substr($row['easternStartTime'], 11, -3);
        
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
        
        $games[] = [
            'season' => $formatted_season,
            'gameNumber' => $row['gameNumber'],
            'gameDate' => $formatted_gameDate,
            'easternStartTime' => $formatted_startTime,
            'gameType' => $gameType_text,
            'home_team_id' => $row['home_team_id'],
            'home_team_name' => $row['home_team_name'],
            'homeScore' => $row['homeScore'],
            'away_team_id' => $row['away_team_id'],
            'away_team_name' => $row['away_team_name'],
            'awayScore' => $row['awayScore'],
            'id' => $row['id']
        ];
    }
    
    echo json_encode([
        'games' => $games,
        'total' => $total,
        'page' => $page,
        'pages' => ceil($total / $recordsPerPage),
        'recordsPerPage' => $recordsPerPage,
        'sort_by' => $requestedSortColumn,
        'sort_order' => strtolower($sortOrder)
    ]);
    
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">
    <title>NHL Games Database</title>
    <link href="/resources/css/default_v3.css" rel="stylesheet" type="text/css" />
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
  </head>
  <body class='bg-gray-900 text-gray-100 font-sans antialiased'>
<!-- Header -->
<?php include 'header.php'; ?>

        <?php
        ini_set('display_errors', 1); error_reporting(E_ALL);

        // Initialize empty array for games
        $all_games = array();
        
        // Define pagination variables
        $recordsPerPage = 50;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $recordsPerPage;

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            # Set default values for search column and term
            $searchColumn = '';
            $searchTerm = '';
            $originalSearchTerm = '';

            $sortColumnMap = [
                'gameDate' => 'nhl_games.gameDate',
                'home_team_name' => 'home_teams.fullName',
                'away_team_name' => 'away_teams.fullName',
                'homeScore' => 'nhl_games.homeScore',
                'awayScore' => 'nhl_games.awayScore',
                'id' => 'nhl_games.id'
            ];
            
            $requestedSortColumn = $_GET['sort_by'] ?? 'id';
            $sortColumn = isset($sortColumnMap[$requestedSortColumn]) ? $sortColumnMap[$requestedSortColumn] : 'nhl_games.id';
            $sortOrder = (isset($_GET['sort_order']) && strtolower($_GET['sort_order']) === 'asc') ? 'ASC' : 'DESC';
    

            if (!empty($_GET['search_column']) && !empty($_GET['search_term'])) {

                $searchColumn = mysqli_real_escape_string($conn, $_GET['search_column']);
                $searchTerm = mysqli_real_escape_string($conn, $_GET['search_term']);
                $originalSearchTerm = $searchTerm;
            } else if (!empty($_GET['search_column'])) {
                $searchColumn = mysqli_real_escape_string($conn, $_GET['search_column']);
                $searchTerm = '';
            } else if (!empty($_GET['search_term'])) {
                $searchTerm = mysqli_real_escape_string($conn, $_GET['search_term']);
                $searchColumn = '';
            } else {
                $searchColumn = '';
                $searchTerm = '';
            }

                # get lowercase version of search term to use in mapping numeric values
                $lowerTerm = strtolower($searchTerm);
                # map and assign new value
                $gameType_duration_map = [
                        'preseason' => 1, 'pre' => 1,
                        'regular season' => 2, 'reg' => 2,
                        'playoffs' => 3, 'postseason' => 3, 'post' => 3,
                        'reg' => 3, 'ot' => 4, 'so' => 5
                    ];
                    if (isset($gameType_duration_map[$lowerTerm])) {
                        $searchTerm = $gameType_duration_map[$lowerTerm];
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

                // Add SQL WHERE clause based on search column and term
                if ($searchColumn === "team") {
                    $sql .= " WHERE home_teams.id = '$searchTerm' OR away_teams.id = '$searchTerm'";
                } else if ($searchColumn === '') {
                    $sql .= " WHERE 1=1"; // Changed from 1-1 to 1=1
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
                } else if ($searchColumn === '') {
                    $where_clauses[] = "1=1"; // Changed from 1-1 to 1=1
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
                $sql .= " LIMIT $recordsPerPage OFFSET $offset"; // Add pagination limit

                // Execute and check query
                $result = mysqli_query($conn, $sql) or die("Query failed: " . mysqli_error($conn));

                if (!$result) {
                    die("Query failed: " . mysqli_error($conn));
                }
                
                ?>

<!-- Main content -->
<div class="py-12 px-4 animate-fade-in bg-gray-900">
    <div class="max-w-7xl mx-auto">
        
        <!-- Page Header Section - matching homepage style -->
        <div class="bg-gray-900/80 p-6 mb-10 rounded-lg border border-gray-700 max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-white tracking-tight mb-2">
                        NHL Games Database
                    </h1>
                    <p class="text-gray-300 text-lg">
                        Search and browse future and past NHL games
                    </p>
                </div>
                
                <!-- Search Form Section - matching homepage search style -->
                <div class="w-full md:w-auto md:max-w-md">
                    <form id="nhl-search" method="GET" action="nhl_games.php" class="relative">
                        <div class="flex gap-2">
                            <!-- Dropdown -->
                            <select name="search_column" id="nhl-search-column"
                            class="bg-gray-800 text-white rounded-md border border-gray-700 px-3 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="season">Season</option>
                                <option value="gameDate">Game Date</option>
                                <option value="easternStartTime">Start Time</option>
                                <option value="gameType">Game Type</option>
                                <option value="team" selected>Team</option>
                                <option value="homeTeamId">Home Team</option>
                                <option value="awayTeamId">Away Team</option>
                            </select>

                            <!-- Input and Search Button -->
                            <div class="relative flex-1">
                                <input type="text" name="search_term" id="search-term" placeholder="Search for game"
                                    class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                                <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-3 text-blue-400 hover:text-blue-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class='text-right mt-2'>
                        <a href="nhl_players.php"
                        class="text-sm text-blue-400 hover:text-blue-300 transition-colors">Search players instead</a>
                    </div>
                </div>
            </div>
        </div>

                    <?php
                    while ($row = $result->fetch_assoc()){
                        # Season
                        $formatted_season_1 = substr($row['season'], 0, 4);
                        $formatted_season_2 = substr($row['season'], 4);
                        $formatted_season = $formatted_season_1 . "-" . $formatted_season_2;

                        # Date
                        $gameDate = $row['gameDate'];
                        $gameDatetime = new DateTime($gameDate);
                        $formatted_gameDate = $gameDatetime->format('m/d/Y');

                        # Time
                        $formatted_startTime = substr($row['easternStartTime'], 11, -3);

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

                        $all_games[] = [
                            'season' => $formatted_season,
                            'gameNumber' => $row['gameNumber'],
                            'gameDate' => $formatted_gameDate,
                            'easternStartTime' => $formatted_startTime,
                            'gameType' => $gameType_text,
                            'home_team_id' => $row['home_team_id'],
                            'home_team_name' => $row['home_team_name'],
                            'homeScore' => $row['homeScore'],
                            'away_team_id' => $row['away_team_id'],
                            'away_team_name' => $row['away_team_name'],
                            'awayScore' => $row['awayScore'],
                            'id' => $row['id']
                        ];

                    } 
                    
                    // Pass data to JavaScript as JSON - only current page
                    echo "<script>const currentPageGames = " . json_encode($all_games) . ";</script>";
                    echo "<script>const totalGames = " . $total_rows . ";</script>";
                    echo "<script>const currentPage = " . $page . ";</script>";
                    echo "<script>const recordsPerPage = " . $recordsPerPage . ";</script>";
                    
        }
        ?>

        <!-- Filters Section - enhanced with homepage styling -->
        <div class="bg-gray-800/80 rounded-lg p-6 mb-8 border border-gray-700 shadow-xl">
            <h2 class="text-xl font-bold text-white mb-4">Filter Games</h2>
            
            <form id="filter-form" method="GET" action="nhl_games.php">
                <!-- Keep search term if it exists -->
                <?php if (!empty($search_term)): ?>
                    <input type="hidden" name="search_term" value="<?= htmlspecialchars($search_term) ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="searchBySeason" class="block text-sm font-medium text-gray-400 mb-2">Season</label>
                        <input type="text" id="searchBySeason" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. 20222023">
                    </div>
                    <div>
                        <label for="searchByDate" class="block text-sm font-medium text-gray-400 mb-2">Date</label>
                        <input type="text" id="searchByDate" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. 4/22/2024">
                    </div>
                    <div>
                        <label for="searchByStartTime" class="block text-sm font-medium text-gray-400 mb-2">Start Time (EST)</label>
                        <input type="text" id="searchByStartTime" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. 21:30">
                    </div>
                    <div>
                        <label for="searchByGameType" class="block text-sm font-medium text-gray-400 mb-2">Game Type</label>
                        <input type="text" id="searchByGameType" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. Reg, Post">
                    </div>
                    <div>
                        <label for="searchByHomeTeam" class="block text-sm font-medium text-gray-400 mb-2">Home Team</label>
                        <input type="text" id="searchByHomeTeam" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. San Jose Sharks, SJS">
                    </div>
                    <div>
                        <label for="searchByAwayTeam" class="block text-sm font-medium text-gray-400 mb-2">Away Team</label>
                        <input type="text" id="searchByAwayTeam" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. Chicago Blackhawks, CHI">
                    </div>
                </div>
            </form>
        </div>

        <!-- Games Table - enhanced with homepage styling -->
        <div class="bg-gray-800/80 rounded-lg overflow-hidden border border-gray-700 shadow-xl mb-8">
            <div class="overflow-x-auto">
                <table id='games-players-summary-table' class="w-full">
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

                    <thead>
                        <tr class="bg-gradient-to-r from-gray-700 to-gray-800 text-white">
                            <?php foreach ([
                                'season' => 'Season',
                                'gameNumber' => 'Game #',
                                'gameDate' => 'Date',
                                'easternStartTime' => 'Start (EST)',
                                'gameType' => 'Game Type',
                                'home_team_name' => 'Home Team',
                                'homeScore' => 'Home Score',
                                'away_team_name' => 'Away Team',
                                'awayScore' => 'Away Score',
                                'game_id' => 'Game ID'
                            ] as $columnName => $displayName): ?>
                                <?php
                                $isActive = ($requestedSortColumn == $columnName);
                                $isAscending = ($sortOrder == 'ASC');
                                $nextSortOrder = ($isActive && $isAscending) ? 'desc' : 'asc';
                                
                                // Use inline styles to ensure they take effect
                                $style = '';
                                if ($isActive) {
                                    if ($isAscending) {
                                        $style = 'border-top: 2px solid #2563eb;'; // blue-600 equivalent
                                    } else {
                                        $style = 'border-bottom: 2px solid #2563eb;'; // blue-600 equivalent
                                    }
                                }
                                ?>
                                
                                <th style="<?= $style ?>" class="relative px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">
                                    <a href="#" onclick="updateSort('<?= $columnName ?>', '<?= $nextSortOrder ?>'); return false;" class="block w-full h-full hover:text-blue-300 transition-colors">
                                        <?= $displayName ?>
                                    </a>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be dynamically generated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Enhanced Pagination Controls - homepage style -->
        <div id="pagination" class="flex justify-center flex-wrap gap-2 mb-8">
            <!-- Pagination buttons will be dynamically generated by JavaScript -->
        </div>

        <!-- Section Divider -->
        <div class="section-divider mx-auto mb-8"></div>
    </div>
</div>

    <?php include 'footer.php'; ?>

    <!-- JS for filtering and pagination -->
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const tableBody = document.querySelector("#games-players-summary-table tbody");
        const paginationContainer = document.getElementById("pagination");
        const searchBySeason = document.getElementById("searchBySeason");
        const searchByDate = document.getElementById("searchByDate");
        const searchByStartTime = document.getElementById("searchByStartTime");
        const searchByGameType = document.getElementById("searchByGameType");
        const searchByHomeTeam = document.getElementById("searchByHomeTeam");
        const searchByAwayTeam = document.getElementById("searchByAwayTeam");
        
        let currentFilters = {
            season: '',
            gameDate: '',
            startTime: '',
            gameType: '',
            homeTeam: '',
            awayTeam: '',
            page: 1,
            sort_by: '<?= $requestedSortColumn ?>',
            sort_order: '<?= strtolower($sortOrder) ?>'
        };
        
        // Apply URL parameters to filters
        function applyUrlParams() {
            const params = new URLSearchParams(window.location.search);
            
            // Set filters from URL parameters if they exist
            if (params.has('season')) {
                searchBySeason.value = params.get('season');
                currentFilters.season = params.get('season');
            }
            
            if (params.has('gameDate')) {
                searchByDate.value = params.get('gameDate');
                currentFilters.gameDate = params.get('gameDate');
            }
            
            if (params.has('startTime')) {
                searchByStartTime.value = params.get('startTime');
                currentFilters.startTime = params.get('startTime');
            }
            
            if (params.has('gameType')) {
                searchByGameType.value = params.get('gameType');
                currentFilters.gameType = params.get('gameType');
            }
            
            if (params.has('homeTeam')) {
                searchByHomeTeam.value = params.get('homeTeam');
                currentFilters.homeTeam = params.get('homeTeam');
            }
            
            if (params.has('awayTeam')) {
                searchByAwayTeam.value = params.get('awayTeam');
                currentFilters.awayTeam = params.get('awayTeam');
            }
            
            if (params.has('page')) {
                currentFilters.page = parseInt(params.get('page')) || 1;
            }
            
            if (params.has('sort_by')) {
                currentFilters.sort_by = params.get('sort_by');
            }
            
            if (params.has('sort_order')) {
                currentFilters.sort_order = params.get('sort_order');
            }
        }
        
        // Debounce function to limit how often filter requests are sent
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), wait);
            };
        }
        
        // Function to load games with filters
        function loadFilteredGames(page = 1) {
            // Show loading indicator
            tableBody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-gray-300">Loading games...</td></tr>';
            
            // Get current search parameters
            const params = new URLSearchParams(window.location.search);
            const searchColumn = params.get('search_column') || '';
            const searchTerm = params.get('search_term') || '';
            
            // Update current page
            currentFilters.page = page;
            
            // Build filter parameters
            const filterParams = new URLSearchParams({
                ajax_filter: 'true',
                page: currentFilters.page,
                per_page: 50,
                sort_by: currentFilters.sort_by,
                sort_order: currentFilters.sort_order,
                search_column: searchColumn,
                search_term: searchTerm,
                season: currentFilters.season,
                gameDate: currentFilters.gameDate,
                startTime: currentFilters.startTime,
                gameType: currentFilters.gameType,
                homeTeam: currentFilters.homeTeam,
                awayTeam: currentFilters.awayTeam
            });
            
            // Make AJAX request
            fetch(`nhl_games.php?${filterParams.toString()}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    renderTable(data.games);
                    renderPagination(data.total, data.page, data.pages);
                    
                    // Update URL to reflect current filters (optional)
                    // Only if we have values that aren't empty
                    const url = new URL(window.location.href);
                    if (currentFilters.season) url.searchParams.set('season', currentFilters.season);
                    else url.searchParams.delete('season');
                    
                    if (currentFilters.gameDate) url.searchParams.set('gameDate', currentFilters.gameDate);
                    else url.searchParams.delete('gameDate');
                    
                    if (currentFilters.startTime) url.searchParams.set('startTime', currentFilters.startTime);
                    else url.searchParams.delete('startTime');
                    
                    if (currentFilters.gameType) url.searchParams.set('gameType', currentFilters.gameType);
                    else url.searchParams.delete('gameType');
                    
                    if (currentFilters.homeTeam) url.searchParams.set('homeTeam', currentFilters.homeTeam);
                    else url.searchParams.delete('homeTeam');
                    
                    if (currentFilters.awayTeam) url.searchParams.set('awayTeam', currentFilters.awayTeam);
                    else url.searchParams.delete('awayTeam');
                    
                    url.searchParams.set('page', currentFilters.page);
                    url.searchParams.set('sort_by', currentFilters.sort_by);
                    url.searchParams.set('sort_order', currentFilters.sort_order);
                    
                    window.history.replaceState(null, '', url);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    tableBody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-red-400">Error loading games. Please try again.</td></tr>';
                });
        }
        
        // Function to render rows
        function renderTable(games) {
            tableBody.innerHTML = "";
            
            if (!games || games.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-gray-400">No games found matching your filters.</td></tr>';
                return;
            }
            
            games.forEach((row, index) => {
                const tr = document.createElement("tr");
                const rowClass = index % 2 === 0 ? "bg-gray-800/50" : "bg-gray-700/30";
                tr.className = `${rowClass} hover:bg-gray-700/50 transition-colors duration-200 border-b border-gray-700/50`;
                
                // Build the static part of the row
                tr.innerHTML = `
                    <td class='px-4 py-3 text-center text-gray-200'>${row.season}</td>
                    <td class='px-4 py-3 text-center text-gray-200'>${row.gameNumber}</td>
                    <td class='px-4 py-3 text-gray-200'>${row.gameDate}</td>
                    <td class='px-4 py-3 text-center text-gray-200'>${row.easternStartTime}</td>
                    <td class='px-4 py-3 text-center text-gray-200'>${row.gameType}</td>
                `;
                
                let homeScoreCell, awayScoreCell, homeTeamCell, awayTeamCell;
                
                // Conditional logic to populate the score and team cells
                if (row.homeScore > row.awayScore) {
                    homeTeamCell = `<td class='px-4 py-3 font-bold text-gray-200'><a href='team_details.php?team_id=${row.home_team_id}' class='text-blue-400 hover:text-blue-300 transition-colors'>${row.home_team_name}</a></td>`;
                    homeScoreCell = `<td class='px-4 py-3 font-bold text-gray-200'>${row.homeScore}</td>`;
                    awayTeamCell = `<td class='px-4 py-3 text-gray-200'><a href='team_details.php?team_id=${row.away_team_id}' class='text-blue-400 hover:text-blue-300 transition-colors'>${row.away_team_name}</a></td>`;
                    awayScoreCell = `<td class='px-4 py-3 text-gray-200'>${row.awayScore}</td>`;
                } else if (row.homeScore < row.awayScore) {
                    homeTeamCell = `<td class='px-4 py-3 text-gray-200'><a href='team_details.php?team_id=${row.home_team_id}' class='text-blue-400 hover:text-blue-300 transition-colors'>${row.home_team_name}</a></td>`;
                    homeScoreCell = `<td class='px-4 py-3 text-gray-200'>${row.homeScore}</td>`;
                    awayTeamCell = `<td class='px-4 py-3 font-bold text-gray-200'><a href='team_details.php?team_id=${row.away_team_id}' class='text-blue-400 hover:text-blue-300 transition-colors'>${row.away_team_name}</a></td>`;
                    awayScoreCell = `<td class='px-4 py-3 font-bold text-gray-200'>${row.awayScore}</td>`;
                } else {
                    homeTeamCell = `<td class='px-4 py-3 text-gray-200'><a href='team_details.php?team_id=${row.home_team_id}' class='text-blue-400 hover:text-blue-300 transition-colors'>${row.home_team_name}</a></td>`;
                    homeScoreCell = `<td class='px-4 py-3 text-gray-200'>${row.homeScore}</td>`;
                    awayTeamCell = `<td class='px-4 py-3 text-gray-200'><a href='team_details.php?team_id=${row.away_team_id}' class='text-blue-400 hover:text-blue-300 transition-colors'>${row.away_team_name}</a></td>`;
                    awayScoreCell = `<td class='px-4 py-3 text-gray-200'>${row.awayScore}</td>`;
                }
                
                // Add the team and score cells to the row
                tr.innerHTML += homeTeamCell + homeScoreCell + awayTeamCell + awayScoreCell;
                
                // Add the last column for the game ID link
                tr.innerHTML += `<td class='px-4 py-3 text-center'><a href='game_details.php?game_id=${row.id}' class='text-blue-400 hover:text-blue-300 transition-colors'>${row.id}</a></td>`;
                
                tableBody.appendChild(tr);
            });
        }
        
        // Function to render pagination controls - enhanced homepage style
        function renderPagination(total, currentPage, totalPages) {
            paginationContainer.innerHTML = "";
            
            if (totalPages <= 1) return;
            
            // Previous button
            if (currentPage > 1) {
                const prevBtn = document.createElement('button');
                prevBtn.textContent = 'Previous';
                prevBtn.className = 'px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md border border-gray-600 transition-colors duration-200';
                prevBtn.addEventListener('click', () => loadFilteredGames(currentPage - 1));
                paginationContainer.appendChild(prevBtn);
            }
            
            // First page
            addPageButton(1);
            
            // Ellipsis if needed
            if (currentPage > 3) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'px-2 py-2 text-gray-500';
                paginationContainer.appendChild(ellipsis);
            }
            
            // Pages around current
            for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
                if (i !== 1 && i !== totalPages) {
                    addPageButton(i);
                }
            }
            
            // Ellipsis if needed
            if (currentPage < totalPages - 2) {
                const ellipsis = document.createElement('span');
                ellipsis.textContent = '...';
                ellipsis.className = 'px-2 py-2 text-gray-500';
                paginationContainer.appendChild(ellipsis);
            }
            
            // Last page
            if (totalPages > 1) {
                addPageButton(totalPages);
            }
            
            // Next button
            if (currentPage < totalPages) {
                const nextBtn = document.createElement('button');
                nextBtn.textContent = 'Next';
                nextBtn.className = 'px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md border border-gray-600 transition-colors duration-200';
                nextBtn.addEventListener('click', () => loadFilteredGames(currentPage + 1));
                paginationContainer.appendChild(nextBtn);
            }
            
            // Stats
            const stats = document.createElement('div');
            stats.className = 'w-full text-center mt-4 text-sm text-gray-300';
            stats.textContent = `Showing ${total > 0 ? (currentPage - 1) * 50 + 1 : 0} - ${Math.min(currentPage * 50, total)} of ${total} games`;
            paginationContainer.appendChild(stats);
            
            function addPageButton(pageNum) {
                const btn = document.createElement('button');
                btn.textContent = pageNum;
                btn.className = 'px-4 py-2 mx-1 rounded-md border transition-colors duration-200 ' + 
                    (pageNum === currentPage ? 
                        'bg-blue-600 text-white border-blue-500' : 
                        'bg-gray-800 hover:bg-gray-700 text-white border-gray-600');
                if (pageNum !== currentPage) {
                    btn.addEventListener('click', () => loadFilteredGames(pageNum));
                }
                paginationContainer.appendChild(btn);
            }
        }
        
        // Handle input events for filters
        function handleFilterInput(field, value) {
            currentFilters[field] = value;
            currentFilters.page = 1; // Reset to page 1 when filters change
            loadFilteredGames(1);
        }
        
        // Attach debounced event listeners for filtering
        const debouncedFilter = debounce((field, value) => handleFilterInput(field, value), 500);
        
        searchBySeason.addEventListener("input", () => debouncedFilter('season', searchBySeason.value));
        searchByDate.addEventListener("input", () => debouncedFilter('gameDate', searchByDate.value));
        searchByStartTime.addEventListener("input", () => debouncedFilter('startTime', searchByStartTime.value));
        searchByGameType.addEventListener("input", () => debouncedFilter('gameType', searchByGameType.value));
        searchByHomeTeam.addEventListener("input", () => debouncedFilter('homeTeam', searchByHomeTeam.value));
        searchByAwayTeam.addEventListener("input", () => debouncedFilter('awayTeam', searchByAwayTeam.value));
        
        // Initialize
        applyUrlParams();
        loadFilteredGames(currentFilters.page);
    });
    </script>
    <script>
        // Function to handle sorting changes
        function updateSort(column, order) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort_by', column);
            currentUrl.searchParams.set('sort_order', order);
            
            // Instead of navigating, trigger the filter with new sort parameters
            if (typeof currentFilters !== 'undefined') {
                currentFilters.sort_by = column;
                currentFilters.sort_order = order;
                loadFilteredGames(currentFilters.page);
            } else {
                // Fallback to page navigation if JS variables aren't available
                window.location.href = currentUrl.toString();
            }
        }
    </script>
    <script>
        //   <!-- JS for search form, allowing player to access nhl_players.php and others to nhl_games.php -->
        document.getElementById('nhl-search').addEventListener('submit', function (e) {
            const column = document.getElementById('nhl-search-column').value;
            console.log("Search column selected:", column); // Debugging
            if (column === 'player') {
                this.action = 'nhl_players.php';
                console.log("Form action set to nhl_players.php"); // Debugging
            } else {
                this.action = 'nhl_games.php';
                console.log("Form action set to nhl_games.php"); // Debugging
            }
        });
    </script>
  </body>
</html>