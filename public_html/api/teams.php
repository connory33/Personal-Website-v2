<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

include '../db_connection.php';

$sql = "SELECT * FROM nhl_teams ORDER BY fullName";
$result = mysqli_query($conn, $sql);

if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Database query failed: ' . mysqli_error($conn)]);
    exit;
}

$teams = array();
while ($row = mysqli_fetch_assoc($result)) {
    $teams[] = $row;
}

echo json_encode($teams, JSON_NUMERIC_CHECK);
mysqli_close($conn);
?>

