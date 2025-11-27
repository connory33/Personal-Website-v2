<?php
/**
 * Test script to verify webhook_deploy.php is accessible
 * 
 * Usage: Upload this to public_html and visit it in your browser
 * URL: https://connoryoung.com/test-webhook.php
 * 
 * This will help verify:
 * 1. PHP is working
 * 2. File permissions are correct
 * 3. Git is accessible
 */

echo "<h1>Webhook Deployment Test</h1>";

// Test 1: PHP is working
echo "<h2>✅ Test 1: PHP is working</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Test 2: Check if webhook_deploy.php exists
echo "<h2>Test 2: webhook_deploy.php file</h2>";
if (file_exists(__DIR__ . '/webhook_deploy.php')) {
    echo "<p>✅ webhook_deploy.php exists</p>";
    $perms = substr(sprintf('%o', fileperms(__DIR__ . '/webhook_deploy.php')), -4);
    echo "<p>Permissions: " . $perms . " (should be 0644)</p>";
} else {
    echo "<p>❌ webhook_deploy.php NOT FOUND</p>";
    echo "<p>Make sure you uploaded it to public_html</p>";
}

// Test 3: Check if git is available
echo "<h2>Test 3: Git availability</h2>";
$gitPaths = ['git', '/usr/bin/git', '/usr/local/bin/git', '/opt/cpanel/ea-git/bin/git'];
$gitFound = false;
$gitPath = '';

foreach ($gitPaths as $path) {
    $output = [];
    $returnCode = 0;
    exec("$path --version 2>&1", $output, $returnCode);
    if ($returnCode === 0) {
        $gitFound = true;
        $gitPath = $path;
        echo "<p>✅ Git found at: <code>$path</code></p>";
        echo "<p>Version: " . implode("\n", $output) . "</p>";
        break;
    }
}

if (!$gitFound) {
    echo "<p>❌ Git NOT FOUND</p>";
    echo "<p>You may need to specify the full path to git in webhook_deploy.php</p>";
}

// Test 4: Check if we're in a git repository
echo "<h2>Test 4: Git repository</h2>";
if ($gitFound) {
    chdir(__DIR__);
    $output = [];
    $returnCode = 0;
    exec("$gitPath status 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "<p>✅ We're in a git repository</p>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
        
        // Check remote
        exec("$gitPath remote -v 2>&1", $output, $returnCode);
        echo "<p><strong>Remotes:</strong></p>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    } else {
        echo "<p>❌ Not in a git repository or git not initialized</p>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
} else {
    echo "<p>⚠️ Cannot test - git not found</p>";
}

// Test 5: Check write permissions
echo "<h2>Test 5: Write permissions</h2>";
$testFile = __DIR__ . '/webhook_test_write.txt';
if (file_put_contents($testFile, 'test')) {
    echo "<p>✅ Can write files</p>";
    unlink($testFile);
} else {
    echo "<p>❌ Cannot write files - check permissions</p>";
}

// Test 6: Check current directory
echo "<h2>Test 6: Current directory</h2>";
echo "<p>Current directory: <code>" . __DIR__ . "</code></p>";
echo "<p>Expected: Should be in public_html</p>";

echo "<hr>";
echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>If all tests pass ✅, proceed with webhook setup</li>";
echo "<li>If any tests fail ❌, see WEBHOOK_SETUP.md for troubleshooting</li>";
echo "<li>After setup, delete this test file for security</li>";
echo "</ol>";

