<?php
/**
 * Diagnostic script to check if API files exist
 * Visit: https://connoryoung.com/check-api.php
 */

echo "<h1>API Files Check</h1>";
echo "<p>Current directory: " . __DIR__ . "</p>";

$apiDir = __DIR__ . '/api';
echo "<h2>Checking: $apiDir</h2>";

if (is_dir($apiDir)) {
    echo "<p style='color: green;'>✓ API directory exists!</p>";
    echo "<h3>Files in API directory:</h3>";
    echo "<ul>";
    $files = scandir($apiDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $fullPath = $apiDir . '/' . $file;
            $exists = file_exists($fullPath) ? '✓' : '✗';
            $size = file_exists($fullPath) ? filesize($fullPath) . ' bytes' : 'missing';
            echo "<li>$exists $file ($size)</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'>✗ API directory does NOT exist!</p>";
}

echo "<h2>Git Status Check</h2>";
$gitDir = __DIR__ . '/.git';
if (is_dir($gitDir)) {
    echo "<p style='color: green;'>✓ Git repository found</p>";
    
    // Check if API files are tracked
    $gitFiles = [];
    exec("cd " . escapeshellarg(__DIR__) . " && git ls-files public_html/api/ 2>&1", $gitFiles);
    echo "<h3>Files tracked in git:</h3>";
    echo "<pre>" . implode("\n", $gitFiles) . "</pre>";
} else {
    echo "<p style='color: red;'>✗ Git repository not found</p>";
}

echo "<h2>File System Check</h2>";
$checkFiles = [
    'api/health.php',
    'api/players.php',
    'api/games.php',
    'api/.htaccess'
];

foreach ($checkFiles as $file) {
    $path = __DIR__ . '/' . $file;
    $exists = file_exists($path);
    $status = $exists ? '<span style="color: green;">✓ EXISTS</span>' : '<span style="color: red;">✗ MISSING</span>';
    echo "<p>$file: $status</p>";
    if ($exists) {
        echo "<p style='margin-left: 20px;'>Size: " . filesize($path) . " bytes, Modified: " . date('Y-m-d H:i:s', filemtime($path)) . "</p>";
    }
}
?>

