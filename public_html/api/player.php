<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include '../db_connection.php';

$playerId = isset($_GET['playerId']) ? mysqli_real_escape_string($conn, $_GET['playerId']) : '';

if (empty($playerId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Player ID is required']);
    exit;
}

$sql = "SELECT 
    nhl_players.*,
    nhl_teams.triCode as currentTeamAbbrev,
    nhl_teams.teamLogo as teamLogo,
    nhl_teams.fullName as currentTeamName
FROM nhl_players
LEFT JOIN nhl_teams ON nhl_players.currentTeamID = nhl_teams.id
WHERE nhl_players.playerId = '$playerId'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Player not found']);
    exit;
}

$player = mysqli_fetch_assoc($result);
echo json_encode($player, JSON_NUMERIC_CHECK);
mysqli_close($conn);
?>

