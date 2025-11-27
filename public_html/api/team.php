<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include '../db_connection.php';

$teamId = isset($_GET['teamId']) ? mysqli_real_escape_string($conn, $_GET['teamId']) : '';

if (empty($teamId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Team ID is required']);
    exit;
}

$sql = "SELECT * FROM nhl_teams WHERE id = '$teamId'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($result) === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Team not found']);
    exit;
}

$team = mysqli_fetch_assoc($result);
echo json_encode($team, JSON_NUMERIC_CHECK);
mysqli_close($conn);
?>

