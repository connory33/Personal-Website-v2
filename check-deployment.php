<?php
/**
 * Diagnostic script to check deployment setup
 * Upload this to public_html and visit it in your browser
 * URL: https://connoryoung.com/check-deployment.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Deployment Diagnostic</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        pre { background: white; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
        h2 { border-bottom: 2px solid #333; padding-bottom: 5px; }
    </style>
</head>
<body>
    <h1>🔍 Deployment Diagnostic</h1>
    
    <?php
    $deployDir = __DIR__;
    $logFile = $deployDir . '/deploy.log';
    
    echo "<h2>1. Directory Check</h2>";
    echo "<p>Current directory: <code>$deployDir</code></p>";
    echo "<p>Expected: Should be in public_html</p>";
    
    echo "<h2>2. Git Repository Check</h2>";
    $gitDir = $deployDir . '/.git';
    if (is_dir($gitDir)) {
        echo "<p class='success'>✅ .git directory exists</p>";
        
        // Check remote
        $output = [];
        exec("cd " . escapeshellarg($deployDir) . " && git remote -v 2>&1", $output, $returnCode);
        echo "<p><strong>Git Remotes:</strong></p>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
        
        // Check current branch
        $output = [];
        exec("cd " . escapeshellarg($deployDir) . " && git branch --show-current 2>&1", $output, $returnCode);
        echo "<p><strong>Current Branch:</strong> " . (isset($output[0]) ? $output[0] : 'unknown') . "</p>";
        
        // Check last commit
        $output = [];
        exec("cd " . escapeshellarg($deployDir) . " && git log -1 --oneline 2>&1", $output, $returnCode);
        echo "<p><strong>Last Commit:</strong></p>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
        
    } else {
        echo "<p class='error'>❌ .git directory NOT FOUND</p>";
        echo "<p>Git repository is not initialized. You need to run:</p>";
        echo "<pre>cd ~/public_html\ngit init\ngit remote add origin https://github.com/connory33/Personal-Website-v2.git\ngit pull origin main</pre>";
    }
    
    echo "<h2>3. Git Executable Check</h2>";
    $gitPaths = ['git', '/usr/bin/git', '/usr/local/bin/git', '/opt/cpanel/ea-git/bin/git', '/usr/local/cpanel/3rdparty/bin/git'];
    $gitFound = false;
    
    foreach ($gitPaths as $path) {
        $output = [];
        $returnCode = 0;
        exec("$path --version 2>&1", $output, $returnCode);
        if ($returnCode === 0) {
            echo "<p class='success'>✅ Git found at: <code>$path</code></p>";
            echo "<p>Version: " . implode("\n", $output) . "</p>";
            $gitFound = true;
            break;
        }
    }
    
    if (!$gitFound) {
        echo "<p class='error'>❌ Git NOT FOUND in any of these locations:</p>";
        echo "<ul>";
        foreach ($gitPaths as $path) {
            echo "<li><code>$path</code></li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>4. Test Git Pull</h2>";
    if ($gitFound && is_dir($gitDir)) {
        echo "<p class='info'>Attempting test pull (dry-run)...</p>";
        $output = [];
        $returnCode = 0;
        exec("cd " . escapeshellarg($deployDir) . " && git fetch origin main --dry-run 2>&1", $output, $returnCode);
        echo "<pre>" . implode("\n", $output) . "</pre>";
        
        if ($returnCode === 0) {
            echo "<p class='success'>✅ Git fetch works!</p>";
        } else {
            echo "<p class='error'>❌ Git fetch failed (return code: $returnCode)</p>";
        }
    } else {
        echo "<p class='warning'>⚠️ Cannot test - git or repository not found</p>";
    }
    
    echo "<h2>5. Deployment Log</h2>";
    if (file_exists($logFile)) {
        echo "<p class='success'>✅ deploy.log exists</p>";
        $logContent = file_get_contents($logFile);
        if (!empty($logContent)) {
            echo "<p><strong>Last 50 lines of log:</strong></p>";
            $lines = explode("\n", $logContent);
            $lastLines = array_slice($lines, -50);
            echo "<pre>" . htmlspecialchars(implode("\n", $lastLines)) . "</pre>";
        } else {
            echo "<p class='warning'>⚠️ Log file is empty - webhook may not have run yet</p>";
        }
    } else {
        echo "<p class='warning'>⚠️ deploy.log NOT FOUND</p>";
        echo "<p>This is normal if the webhook hasn't run yet. It will be created on first webhook trigger.</p>";
    }
    
    echo "<h2>6. File Permissions</h2>";
    $webhookFile = $deployDir . '/webhook_deploy.php';
    if (file_exists($webhookFile)) {
        $perms = substr(sprintf('%o', fileperms($webhookFile)), -4);
        echo "<p>webhook_deploy.php permissions: <code>$perms</code> (should be 0644)</p>";
        echo "<p>" . (is_readable($webhookFile) ? "✅" : "❌") . " Readable</p>";
        echo "<p>" . (is_writable($deployDir) ? "✅" : "❌") . " Directory writable (needed for deploy.log)</p>";
    } else {
        echo "<p class='error'>❌ webhook_deploy.php NOT FOUND</p>";
        echo "<p>Make sure you uploaded it to public_html</p>";
    }
    
    echo "<h2>7. Recommendations</h2>";
    echo "<ul>";
    if (!is_dir($gitDir)) {
        echo "<li class='error'><strong>CRITICAL:</strong> Initialize git repository in public_html</li>";
    }
    if (!$gitFound) {
        echo "<li class='error'><strong>CRITICAL:</strong> Git executable not found - contact your host</li>";
    }
    if (is_dir($gitDir) && $gitFound) {
        echo "<li class='success'>✅ Setup looks good! Check deploy.log after next webhook trigger</li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<p><small>After checking, delete this file for security: <code>check-deployment.php</code></small></p>";
    ?>

</body>
</html>

