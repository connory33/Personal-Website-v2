<?php
/* --- turn on ALL error output before anything else ------------------- */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* --- try to include connection file and confirm it really loaded ----- */
$included = @include __DIR__ . '/db_connection.php';   // absolute path is safer
if ($included === false) {
    die('Could not include db_connection.php – check the path.');
}

/* if we reach here the include succeeded, so any crash is **inside**
   db_connection.php or in code that comes next.  */
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NHL Players Database</title>
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

<?php include 'header.php'; ?>

<body class="bg-gray-900 text-gray-100 font-sans antialiased">
<div class='mx-auto'>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialize the WHERE clause
$where_conditions = array();

// Get filter parameters - these will be used by both the form and the query
$filter_name = isset($_GET['filter_name']) ? mysqli_real_escape_string($conn, $_GET['filter_name']) : '';
$filter_team = isset($_GET['filter_team']) ? mysqli_real_escape_string($conn, $_GET['filter_team']) : '';
$filter_hand = isset($_GET['filter_hand']) ? mysqli_real_escape_string($conn, $_GET['filter_hand']) : '';
$filter_country = isset($_GET['filter_country']) ? mysqli_real_escape_string($conn, $_GET['filter_country']) : '';
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';
$filter_number = isset($_GET['filter_number']) ? mysqli_real_escape_string($conn, $_GET['filter_number']) : '';
$filter_weight_min = isset($_GET['filter_weight_min']) ? (int)$_GET['filter_weight_min'] : '';
$filter_weight_max = isset($_GET['filter_weight_max']) ? (int)$_GET['filter_weight_max'] : '';
$search_term = isset($_GET['search_term']) ? mysqli_real_escape_string($conn, $_GET['search_term']) : '';

// Base SQL query for counting and retrieving data
$base_sql = "SELECT nhl_players.*, nhl_teams.triCode as currentTeamAbbrev, nhl_teams.teamLogo as teamLogo
            FROM nhl_players
            LEFT JOIN nhl_teams on nhl_players.currentTeamID = nhl_teams.id";

// Apply search term if provided
if (!empty($search_term)) {
    $where_conditions[] = "(firstName LIKE '%$search_term%' 
                    OR lastName LIKE '%$search_term%'
                    OR CONCAT(firstName, ' ', lastName) LIKE '%$search_term%')";
}

// Apply filters
if (!empty($filter_name)) {
    $where_conditions[] = "(firstName LIKE '%$filter_name%' 
                    OR lastName LIKE '%$filter_name%'
                    OR CONCAT(firstName, ' ', lastName) LIKE '%$filter_name%')";
}

if (!empty($filter_team)) {
    $where_conditions[] = "(nhl_teams.triCode LIKE '%$filter_team%')";
}

if (!empty($filter_hand)) {
    $where_conditions[] = "(shootsCatches LIKE '%$filter_hand%')";
}

if (!empty($filter_country)) {
    $where_conditions[] = "(birthCountry LIKE '%$filter_country%')";
}

if ($filter_status === 'active') {
    $check_query = "SELECT DISTINCT isActive FROM nhl_players WHERE isActive IS NOT NULL AND isActive != '' LIMIT 5";
    $check_result = mysqli_query($conn, $check_query);
    if (!$check_result) {
        // debug_sql("Error checking isActive values: " . mysqli_error($conn));
    } else {
        $active_values = [];
        while ($check_row = mysqli_fetch_assoc($check_result)) {
            $active_values[] = $check_row['isActive'];
        }
        // debug_sql("isActive distinct values: " . implode(", ", $active_values));
        
        // First try with default
        $where_conditions[] = "isActive = 'True'";
    }
} elseif ($filter_status === 'inactive') {
    $where_conditions[] = "isActive = 'False'";
}

if (!empty($filter_number)) {
    $where_conditions[] = "sweaterNumber = '$filter_number'";
}

if (!empty($filter_weight_min)) {
    $where_conditions[] = "weightInPounds >= $filter_weight_min";
}

if (!empty($filter_weight_max)) {
    $where_conditions[] = "weightInPounds <= $filter_weight_max";
}

// Combine all WHERE conditions
if (!empty($where_conditions)) {
    $base_sql .= " WHERE " . implode(" AND ", $where_conditions);
}

// Order the results
$base_sql .= " ORDER BY nhl_players.playerID DESC";

// Count total rows for pagination
$count_sql = "SELECT COUNT(*) as total FROM (" . $base_sql . ") as count_table";
$count_result = mysqli_query($conn, $count_sql);
$total_rows = mysqli_fetch_assoc($count_result)['total'];

// Pagination setup
$limit = 25; // Results per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;
$total_pages = ceil($total_rows / $limit);

// Get paginated results
$sql = $base_sql . " LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

// Calculate pagination range
$start = $offset + 1;
$end = min($offset + $limit, $total_rows);
?>

<!-- Main content -->
<div class="py-12 px-4 animate-fade-in bg-gray-900">
    <div class="max-w-7xl mx-auto">
        
        <!-- Page Header Section - matching homepage style -->
        <div class="bg-gray-900/80 p-6 mb-10 rounded-lg border border-gray-700 max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-white tracking-tight mb-2">
                        NHL Players Database
                    </h1>
                    <p class="text-gray-300 text-lg">
                        Search and browse current and former NHL players
                    </p>
                </div>
                
                <!-- Search Form Section - matching homepage search style -->
                <div class="w-full md:w-auto md:max-w-xs">
                    <form id="nhl-search" method="GET" action="nhl_players.php" class="relative">
                        <input type="hidden" name="search_column" value="player">
                        <input 
                            id="search-term" 
                            name="search_term" 
                            type="text" 
                            placeholder="Enter player name" 
                            value="<?= htmlspecialchars($search_term) ?>"
                            class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-3 text-blue-400 hover:text-blue-300">
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
                    <div class="text-right mt-2">
                        <a href="nhl_games.php" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">Search games instead</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section - enhanced with homepage styling -->
        <div class="bg-gray-800/80 rounded-lg p-6 mb-8 border border-gray-700 shadow-xl">
            <h2 class="text-xl font-bold text-white mb-4">Filter Players</h2>
            
            <form id="filter-form" method="GET" action="nhl_players.php">
                <!-- Keep search term if it exists -->
                <?php if (!empty($search_term)): ?>
                    <input type="hidden" name="search_term" value="<?= htmlspecialchars($search_term) ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="filter_name" class="block text-sm font-medium text-gray-400 mb-2">Player Name</label>
                        <input type="text" id="filter_name" name="filter_name" value="<?= htmlspecialchars($filter_name) ?>" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. Connor">
                    </div>
                    <div>
                        <label for="filter_team" class="block text-sm font-medium text-gray-400 mb-2">Team</label>
                        <input type="text" id="filter_team" name="filter_team" value="<?= htmlspecialchars($filter_team) ?>" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. NYR">
                    </div>
                    <div>
                        <label for="filter_hand" class="block text-sm font-medium text-gray-400 mb-2">Shoots/Catches</label>
                        <input type="text" id="filter_hand" name="filter_hand" value="<?= htmlspecialchars($filter_hand) ?>" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. L">
                    </div>
                    <div>
                        <label for="filter_country" class="block text-sm font-medium text-gray-400 mb-2">Country</label>
                        <input type="text" id="filter_country" name="filter_country" value="<?= htmlspecialchars($filter_country) ?>" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. CAN">
                    </div>
                    <div>
                        <label for="filter_status" class="block text-sm font-medium text-gray-400 mb-2">Status</label>
                        <select id="filter_status" name="filter_status" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" <?= $filter_status === '' ? 'selected' : '' ?>>All Players</option>
                            <option value="active" <?= $filter_status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $filter_status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label for="filter_number" class="block text-sm font-medium text-gray-400 mb-2">Jersey Number</label>
                        <input type="text" id="filter_number" name="filter_number" value="<?= htmlspecialchars($filter_number) ?>" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g. 99">
                    </div>
                    <div>
                        <label for="filter_weight" class="block text-sm font-medium text-gray-400 mb-2">Weight (lbs)</label>
                        <div class="flex gap-2">
                            <input type="text" id="filter_weight_min" name="filter_weight_min" value="<?= htmlspecialchars($filter_weight_min) ?>" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Min">
                            <input type="text" id="filter_weight_max" name="filter_weight_max" value="<?= htmlspecialchars($filter_weight_max) ?>" class="w-full bg-gray-800 text-white rounded-md border border-gray-700 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Max">
                        </div>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" id="applyFilters" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-md transition-colors duration-200">
                            Apply Filters
                        </button>
                        <a href="nhl_players.php" class="w-full text-center bg-gray-700 hover:bg-gray-600 text-white font-semibold px-6 py-3 rounded-md transition-colors duration-200">
                            Clear All
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Information -->
        <div class="bg-gray-800/50 rounded-lg p-4 mb-6 border border-gray-700">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <span class="text-gray-300">
                    <?php if ($total_rows > 0): ?>
                        Showing players <?= $start ?> to <?= $end ?> of <?= number_format($total_rows) ?>
                    <?php else: ?>
                        No players match your search criteria
                    <?php endif; ?>
                </span>
                <span class="text-gray-400">
                    <?php if ($total_pages > 0): ?>
                        Page <?= $page ?> of <?= number_format($total_pages) ?>
                    <?php endif; ?>
                </span>
            </div>
        </div>

        <!-- Function to convert country code to flag image -->
        <?php
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
        ?>

        <!-- Players Table - enhanced with homepage styling -->
        <div class="bg-gray-800/80 rounded-lg overflow-hidden border border-gray-700 shadow-xl mb-8">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-gray-700 to-gray-800 text-white">
                            <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">ID</th>
                            <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Name</th>
                            <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Height</th>
                            <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Weight</th>
                            <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Birthdate</th>
                            <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Country</th>
                            <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Shoots/Catches</th>
                            <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Active</th>
                            <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Number</th>
                            <th class="px-4 py-4 text-left text-sm font-semibold uppercase tracking-wider">Team</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_rows === 0): ?>
                            <tr class="bg-gray-800/50">
                                <td colspan="10" class="px-6 py-8 text-center text-gray-400 italic">No players match your filter criteria.</td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $row_index = 0;
                            while ($row = mysqli_fetch_assoc($result)): 
                                $row_class = $row_index % 2 === 0 ? "bg-gray-800/50" : "bg-gray-700/30";
                                $row_index++;
                            ?>
                                <tr class="<?= $row_class ?> hover:bg-gray-700/50 transition-colors duration-200 border-b border-gray-700/50">
                                    <td class="px-4 py-3 font-semibold text-gray-200">
                                        <a href="player_details.php?player_id=<?= $row['playerId'] ?>" class="text-blue-400 hover:text-blue-300 transition-colors">
                                            <?= $row['playerId'] ?>
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-200">
                                        <a href="player_details.php?player_id=<?= $row['playerId'] ?>" class="text-blue-400 hover:text-blue-300 transition-colors">
                                            <?= $row['firstName'] . ' ' . $row['lastName'] ?>
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-gray-300"><?= $row['heightInInches'] ?? '-' ?> in / <?= $row['heightInCentimeters'] ?? '-' ?> cm</td>
                                    <td class="px-4 py-3 text-gray-300"><?= $row['weightInPounds'] ?? '-' ?> lbs / <?= $row['weightInKilograms'] ?? '-' ?> kg</td>
                                    <td class="px-4 py-3 text-gray-300"><?= $row['birthDate'] ? date('m/d/Y', strtotime($row['birthDate'])) : '-' ?></td>
                                    <td class="px-4 py-3"><?= getFlagSVG($row['birthCountry']) ?? $row['birthCountry'] ?></td>
                                    <?php if ($row['shootsCatches'] == 'L') {
                                      $shootsCatches = 'Left';
                                    } elseif ($row['shootsCatches'] == 'R') {
                                      $shootsCatches = 'Right';
                                    } else {
                                      $shootsCatches = 'Unknown';
                                    } ?>
                                    <td class="px-4 py-3 text-gray-300"><?= $shootsCatches ?></td>
                                    <td class="px-4 py-3">
                                        <?= $row['isActive'] == 'True' ? 
                                            '<span class="inline-flex items-center px-2 py-1 bg-emerald-500/20 border border-emerald-500/30 rounded-full text-xs font-medium text-emerald-400">Active</span>' : 
                                            '<span class="inline-flex items-center px-2 py-1 bg-red-500/20 border border-red-500/30 rounded-full text-xs font-medium text-red-400">Inactive</span>' 
                                        ?>
                                    </td>
                                    <td class="px-4 py-3 text-gray-300"><?= $row['sweaterNumber'] ?: '-' ?></td>
                                    <td class="px-4 py-3">
                                        <?php if (!empty($row['teamLogo'])): ?>
                                            <a href="team_details.php?team_id=<?= $row['currentTeamID'] ?? '' ?>" class="block w-12 h-12 mx-auto transition-transform hover:scale-110">
                                                <img src="<?= htmlspecialchars($row['teamLogo']) ?>" alt="<?= htmlspecialchars($row['currentTeamAbbrev'] ?? '') ?>" class="w-full h-full object-contain">
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-500">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Enhanced Pagination Controls - homepage style -->
        <?php if ($total_pages > 1): ?>
            <div class="flex flex-wrap justify-center gap-2 mb-8">
                <!-- First page button -->
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" class="flex items-center justify-center w-10 h-10 rounded-md bg-gray-800 hover:bg-gray-700 text-white border border-gray-600 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M9.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L5.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                <?php else: ?>
                    <div class="flex items-center justify-center w-10 h-10 rounded-md bg-gray-800/50 text-gray-500 border border-gray-600/50 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M15.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M9.707 15.707a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414l5-5a1 1 0 111.414 1.414L5.414 10l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                    </div>
                <?php endif; ?>

                <!-- Previous page button -->
                <?php if ($page > 1): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="flex items-center justify-center w-10 h-10 rounded-md bg-gray-800 hover:bg-gray-700 text-white border border-gray-600 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                <?php else: ?>
                    <div class="flex items-center justify-center w-10 h-10 rounded-md bg-gray-800/50 text-gray-500 border border-gray-600/50 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                <?php endif; ?>

                <!-- Page numbers -->
                <?php
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + 4);
                
                if ($end_page - $start_page < 4) {
                    $start_page = max(1, $end_page - 4);
                }
                
                // Show ellipsis before page numbers if needed
                if ($start_page > 1): 
                ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>" 
                    class="flex items-center justify-center w-10 h-10 rounded-md <?= 1 === $page ? 'bg-blue-600 text-white border-blue-500' : 'bg-gray-800 hover:bg-gray-700 text-white border-gray-600' ?> border transition-colors duration-200">
                        1
                    </a>
                    <?php if ($start_page > 2): ?>
                        <span class="flex items-center justify-center w-10 h-10 text-gray-500">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <!-- Number buttons -->
                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <?php if ($i !== 1 && $i !== $total_pages): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                        class="flex items-center justify-center w-10 h-10 rounded-md <?= $i === $page ? 'bg-blue-600 text-white border-blue-500' : 'bg-gray-800 hover:bg-gray-700 text-white border-gray-600' ?> border transition-colors duration-200">
                            <?= $i ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <!-- Show ellipsis after page numbers if needed -->
                <?php if ($end_page < $total_pages): ?>
                    <?php if ($end_page < $total_pages - 1): ?>
                        <span class="flex items-center justify-center w-10 h-10 text-gray-500">...</span>
                    <?php endif; ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" 
                    class="flex items-center justify-center w-10 h-10 rounded-md <?= $total_pages === $page ? 'bg-blue-600 text-white border-blue-500' : 'bg-gray-800 hover:bg-gray-700 text-white border-gray-600' ?> border transition-colors duration-200">
                        <?= $total_pages ?>
                    </a>
                <?php endif; ?>

                <!-- Next page button -->
                <?php if ($page < $total_pages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="flex items-center justify-center w-10 h-10 rounded-md bg-gray-800 hover:bg-gray-700 text-white border border-gray-600 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                <?php else: ?>
                    <div class="flex items-center justify-center w-10 h-10 rounded-md bg-gray-800/50 text-gray-500 border border-gray-600/50 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                <?php endif; ?>

                <!-- Last page button -->
                <?php if ($page < $total_pages): ?>
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>" class="flex items-center justify-center w-10 h-10 rounded-md bg-gray-800 hover:bg-gray-700 text-white border border-gray-600 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 6.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-3.293a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                <?php else: ?>
                    <div class="flex items-center justify-center w-10 h-10 rounded-md bg-gray-800/50 text-gray-500 border border-gray-600/50 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 15.707a1 1 0 010-1.414L8.586 10 4.293 6.707a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            <path fill-rule="evenodd" d="M10.293 15.707a1 1 0 010-1.414L14.586 10l-4.293-3.293a1 1 0 011.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Section Divider -->
        <div class="section-divider mx-auto mb-8"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced visual feedback when user clicks Apply Filters button
        const filterForm = document.getElementById('filter-form');
        const applyFilters = document.getElementById('applyFilters');
        
        if (filterForm && applyFilters) {
            applyFilters.addEventListener('click', function() {
                applyFilters.classList.remove('bg-blue-600');
                applyFilters.classList.add('bg-blue-700');
                applyFilters.innerHTML = 'Applying...';
                setTimeout(() => {
                    filterForm.submit();
                }, 200);
            });
        }
        
        // Enhanced visual feedback for search submission
        const searchForm = document.getElementById('nhl-search');
        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                const searchInput = document.getElementById('search-term');
                searchInput.classList.add('ring-2', 'ring-blue-500');
                setTimeout(() => {
                    searchInput.classList.remove('ring-2', 'ring-blue-500');
                }, 200);
            });
        }
    });
</script>

<?php include 'footer.php'; ?>
</body>
</html>