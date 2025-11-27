<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include '../db_connection.php';

// Get query parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 25;
$offset = ($page - 1) * $limit;

$search_term = isset($_GET['search_term']) ? mysqli_real_escape_string($conn, $_GET['search_term']) : '';
$filter_name = isset($_GET['filter_name']) ? mysqli_real_escape_string($conn, $_GET['filter_name']) : '';
$filter_team = isset($_GET['filter_team']) ? mysqli_real_escape_string($conn, $_GET['filter_team']) : '';
$filter_hand = isset($_GET['filter_hand']) ? mysqli_real_escape_string($conn, $_GET['filter_hand']) : '';
$filter_country = isset($_GET['filter_country']) ? mysqli_real_escape_string($conn, $_GET['filter_country']) : '';
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';
$filter_number = isset($_GET['filter_number']) ? mysqli_real_escape_string($conn, $_GET['filter_number']) : '';
$filter_weight_min = isset($_GET['filter_weight_min']) ? intval($_GET['filter_weight_min']) : '';
$filter_weight_max = isset($_GET['filter_weight_max']) ? intval($_GET['filter_weight_max']) : '';

// Build WHERE conditions
$where_conditions = array();

if (!empty($search_term)) {
    $where_conditions[] = "(firstName LIKE '%$search_term%' OR lastName LIKE '%$search_term%' OR CONCAT(firstName, ' ', lastName) LIKE '%$search_term%')";
}

if (!empty($filter_name)) {
    $where_conditions[] = "(firstName LIKE '%$filter_name%' OR lastName LIKE '%$filter_name%' OR CONCAT(firstName, ' ', lastName) LIKE '%$filter_name%')";
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
    $where_conditions[] = "isActive = 'True'";
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

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : '';

// Count total
$count_sql = "SELECT COUNT(*) as total FROM nhl_players LEFT JOIN nhl_teams ON nhl_players.currentTeamID = nhl_teams.id $where_clause";
$count_result = mysqli_query($conn, $count_sql);
$total = mysqli_fetch_assoc($count_result)['total'];

// Get players
$sql = "SELECT 
    nhl_players.*,
    nhl_teams.triCode as currentTeamAbbrev,
    nhl_teams.teamLogo as teamLogo
FROM nhl_players
LEFT JOIN nhl_teams ON nhl_players.currentTeamID = nhl_teams.id
$where_clause
ORDER BY nhl_players.playerID DESC
LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
    exit;
}

$players = array();
while ($row = mysqli_fetch_assoc($result)) {
    $players[] = $row;
}

$response = array(
    'players' => $players,
    'total' => intval($total),
    'page' => $page,
    'totalPages' => ceil($total / $limit),
    'limit' => $limit
);

echo json_encode($response, JSON_NUMERIC_CHECK);
mysqli_close($conn);
?>

