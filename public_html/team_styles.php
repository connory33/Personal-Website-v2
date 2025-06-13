<?php
header("Content-type: text/css");
require_once('db_connection.php'); // Get access to your variables

// Get team_id from URL parameter
$team_id = $_GET['team_id'];

// Query to get team colors
$teamQuery = "SELECT * FROM nhl_teams WHERE id = $team_id";
$teamResult = mysqli_query($conn, $teamQuery);
$teamData = mysqli_fetch_assoc($teamResult);

$teamColor1 = $teamData['teamColor1'];
$teamColor2 = $teamData['teamColor2'];
if ($teamData['teamColor3']) {
    $teamColor3 = $teamData['teamColor3'];
} else {
    $teamColor3 = $teamColor1; // Fallback to teamColor1 if teamColor3 is not set
}
if ($teamData['teamColor4']) {
    $teamColor4 = $teamData['teamColor4'];
} else {
    $teamColor4 = $teamColor2; // Fallback to teamColor2 if teamColor4 is not set
}

$teamLogo = $teamData['teamLogo'];
?>


<style>
    .season-tabs-container {
    background-color: rgba(28, 35, 51, 0.5);
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

/* Tab Navigation */
.season-tabs {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.5rem;
    background-color: rgba(19, 26, 36, 0.7);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    overflow-x: auto;
    scrollbar-width: none; /* Firefox */
}

.season-tabs::-webkit-scrollbar {
    display: none; /* Chrome, Safari, Edge */
}

/* Tab Buttons */
.season-tab-button {
    padding: 0.75rem 1.25rem;
    border-radius: 0.2rem;
    color: white;
    font-weight: 500;
    font-size: 0.9rem;
    background: transparent;
    border: none;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s;
}

.season-tab-button:hover {
    color: #fff;
    background-color: rgba(255, 255, 255, 0.05);
}

.season-tab-button.active {
    color: #fff;
    background-color: var(--team-color, #437be4);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

/* Tab Content */
.season-tab-content {
    min-height: 400px;
    padding: 1.5rem;
}

.season-tab-pane {
    display: none;
    animation: fadeIn 0.3s ease-in-out;
}

.season-tab-pane.active {
    display: block;
}

/* Team Stats Table */
.team-stats-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    overflow: hidden;
    border-radius: 0.5rem;
    background-color: rgba(19, 26, 36, 0.5);
}

.team-stats-table thead th {
    background-color: rgba(38, 48, 68, 0.8);
    font-weight: 600;
    text-align: left;
    padding: 0.75rem 1rem;
    color: #fff;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.team-stats-table tbody tr:nth-child(even) {
    background-color: rgba(38, 48, 68, 0.3);
}

.team-stats-table td {
    padding: 0.75rem 1rem;
    color: #e0e0e0;
    font-size: 0.9rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    transition: background-color 0.15s;
}

.team-stats-table tbody tr:hover td {
    background-color: rgba(0, 230, 255, 0.05);
}

</style>

