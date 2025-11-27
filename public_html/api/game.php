<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include '../db_connection.php';

$gameId = isset($_GET['gameId']) ? mysqli_real_escape_string($conn, $_GET['gameId']) : '';

if (empty($gameId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Game ID is required']);
    exit;
}

$sql = "SELECT
    nhl_games.*,
    home_teams.fullName AS home_team_name,
    home_teams.id AS home_team_id,
    home_teams.triCode AS home_team_tricode,
    home_teams.teamLogo AS home_team_logo,
    away_teams.fullName AS away_team_name,
    away_teams.id AS away_team_id,
    away_teams.triCode AS away_team_tricode,
    away_teams.teamLogo AS away_team_logo
FROM nhl_games
JOIN nhl_teams AS home_teams ON nhl_games.homeTeamId = home_teams.id
JOIN nhl_teams AS away_teams ON nhl_games.awayTeamId = away_teams.id
WHERE nhl_games.id = '$gameId'";

$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Game not found']);
    exit;
}

$game = mysqli_fetch_assoc($result);
echo json_encode($game, JSON_NUMERIC_CHECK);
mysqli_close($conn);
?>

