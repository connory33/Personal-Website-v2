<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include '../db_connection.php';

// Get query parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 50;
$offset = ($page - 1) * $per_page;

$search_column = isset($_GET['search_column']) ? mysqli_real_escape_string($conn, $_GET['search_column']) : '';
$search_term = isset($_GET['search_term']) ? mysqli_real_escape_string($conn, $_GET['search_term']) : '';
$season = isset($_GET['season']) ? mysqli_real_escape_string($conn, $_GET['season']) : '';
$gameDate = isset($_GET['gameDate']) ? mysqli_real_escape_string($conn, $_GET['gameDate']) : '';
$startTime = isset($_GET['startTime']) ? mysqli_real_escape_string($conn, $_GET['startTime']) : '';
$gameType = isset($_GET['gameType']) ? mysqli_real_escape_string($conn, $_GET['gameType']) : '';
$homeTeam = isset($_GET['homeTeam']) ? mysqli_real_escape_string($conn, $_GET['homeTeam']) : '';
$awayTeam = isset($_GET['awayTeam']) ? mysqli_real_escape_string($conn, $_GET['awayTeam']) : '';

$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';
$sort_order = isset($_GET['sort_order']) ? strtoupper($_GET['sort_order']) : 'DESC';

// Sort column mapping
$sortColumnMap = array(
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
);

$sortColumn = isset($sortColumnMap[$sort_by]) ? $sortColumnMap[$sort_by] : 'nhl_games.id';
$sortOrderUpper = ($sort_order === 'ASC') ? 'ASC' : 'DESC';

// Build WHERE conditions
$where_conditions = array('1=1');

if ($search_column && $search_term) {
    if ($search_column === 'team') {
        $where_conditions[] = "(home_teams.id = '$search_term' OR away_teams.id = '$search_term')";
    } else {
        $where_conditions[] = "nhl_games.$search_column LIKE '%$search_term%'";
    }
}

if (!empty($season)) {
    $where_conditions[] = "nhl_games.season LIKE '%$season%'";
}

if (!empty($gameDate)) {
    $where_conditions[] = "nhl_games.gameDate LIKE '%$gameDate%'";
}

if (!empty($startTime)) {
    $where_conditions[] = "nhl_games.easternStartTime LIKE '%$startTime%'";
}

if (!empty($gameType)) {
    $gameTypeMap = array(
        'Pre.' => 1, 'pre' => 1,
        'Reg.' => 2, 'reg' => 2,
        'Post.' => 3, 'post' => 3
    );
    $gameTypeNum = isset($gameTypeMap[strtolower($gameType)]) ? $gameTypeMap[strtolower($gameType)] : $gameType;
    $where_conditions[] = "nhl_games.gameType = $gameTypeNum";
}

if (!empty($homeTeam)) {
    $where_conditions[] = "home_teams.fullName LIKE '%$homeTeam%'";
}

if (!empty($awayTeam)) {
    $where_conditions[] = "away_teams.fullName LIKE '%$awayTeam%'";
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Count total
$count_sql = "SELECT COUNT(*) as total
FROM nhl_games
JOIN nhl_teams AS home_teams ON nhl_games.homeTeamId = home_teams.id
JOIN nhl_teams AS away_teams ON nhl_games.awayTeamId = away_teams.id
$where_clause";

$count_result = mysqli_query($conn, $count_sql);
if (!$count_result) {
    http_response_code(500);
    echo json_encode(['error' => 'Count query failed: ' . mysqli_error($conn)]);
    exit;
}
$total = mysqli_fetch_assoc($count_result)['total'];

// Get games
$sql = "SELECT
    nhl_games.*,
    home_teams.fullName AS home_team_name,
    home_teams.id AS home_team_id,
    away_teams.fullName AS away_team_name,
    away_teams.id AS away_team_id
FROM nhl_games
JOIN nhl_teams AS home_teams ON nhl_games.homeTeamId = home_teams.id
JOIN nhl_teams AS away_teams ON nhl_games.awayTeamId = away_teams.id
$where_clause
ORDER BY $sortColumn $sortOrderUpper
LIMIT $per_page OFFSET $offset";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . mysqli_error($conn)]);
    exit;
}

$games = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Format season
    $formatted_season_1 = substr($row['season'], 0, 4);
    $formatted_season_2 = substr($row['season'], 4);
    $formatted_season = $formatted_season_1 . "-" . $formatted_season_2;
    
    // Format date
    $gameDate = $row['gameDate'];
    $formatted_gameDate = $gameDate ? date('m/d/Y', strtotime($gameDate)) : '';
    
    // Format time
    $formatted_startTime = $row['easternStartTime'] ? substr($row['easternStartTime'], 11, 5) : '';
    
    // Format game type
    $gameType_text = 'Unknown';
    if ($row['gameType'] == 1) {
        $gameType_text = "Pre.";
    } elseif ($row['gameType'] == 2) {
        $gameType_text = "Reg.";
    } elseif ($row['gameType'] == 3) {
        $gameType_text = "Post.";
    }
    
    $games[] = array(
        'season' => $formatted_season,
        'gameNumber' => $row['gameNumber'],
        'gameDate' => $formatted_gameDate,
        'easternStartTime' => $formatted_startTime,
        'gameType' => $gameType_text,
        'home_team_id' => $row['home_team_id'],
        'home_team_name' => $row['home_team_name'],
        'homeScore' => intval($row['homeScore']),
        'away_team_id' => $row['away_team_id'],
        'away_team_name' => $row['away_team_name'],
        'awayScore' => intval($row['awayScore']),
        'id' => $row['id']
    );
}

$response = array(
    'games' => $games,
    'total' => intval($total),
    'page' => $page,
    'pages' => ceil($total / $per_page),
    'recordsPerPage' => $per_page,
    'sort_by' => $sort_by,
    'sort_order' => strtolower($sortOrderUpper)
);

echo json_encode($response, JSON_NUMERIC_CHECK);
mysqli_close($conn);
?>

