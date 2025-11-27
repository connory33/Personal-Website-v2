<?php
/**
 * GitHub Webhook Deployment Script
 * 
 * SETUP INSTRUCTIONS:
 * 1. Generate a secret token (see WEBHOOK_SETUP.md)
 * 2. Update $secret below with your generated token
 * 3. Upload this file to your cPanel public_html directory
 * 4. Set up the webhook in GitHub (see WEBHOOK_SETUP.md)
 * 
 * SECURITY NOTE:
 * Consider protecting this file with .htaccess to restrict access
 * or place it in a subdirectory with restricted access
 */

// SECURITY: Replace this with a random string (32+ characters recommended)
// Generate one at: https://www.random.org/strings/ or use: openssl rand -hex 32
$secret = 'uwaRmlTPRoVjIuhClyR2mEyu03OCmh85';

// Get the raw POST data
$payload = file_get_contents('php://input');
$headers = getallheaders();

// Verify the webhook signature (if using secret)
if (isset($headers['X-Hub-Signature-256'])) {
    $signature = $headers['X-Hub-Signature-256'];
    $hash = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    
    if (!hash_equals($signature, $hash)) {
        http_response_code(403);
        die('Invalid signature');
    }
}

// Parse the JSON payload
$data = json_decode($payload, true);

// Only proceed if this is a push to main branch
if (isset($data['ref']) && $data['ref'] === 'refs/heads/main') {
    // Log the deployment
    $logFile = __DIR__ . '/deploy.log';
    $commitHash = isset($data['head_commit']['id']) ? substr($data['head_commit']['id'], 0, 7) : 'unknown';
    $logEntry = "\n" . str_repeat("=", 60) . "\n";
    $logEntry .= date('Y-m-d H:i:s') . " - Deploying commit: " . $commitHash . "\n";
    $logEntry .= "Current directory: " . __DIR__ . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Change to public_html directory (this file should be in public_html)
    $deployDir = __DIR__;
    chdir($deployDir);
    
    // Check if .git directory exists
    $gitDir = $deployDir . '/.git';
    if (!is_dir($gitDir)) {
        $errorMsg = "ERROR: .git directory not found in $deployDir\n";
        $errorMsg .= "Git repository may not be initialized. Run: git init && git remote add origin https://github.com/connory33/Personal-Website-v2.git\n";
        file_put_contents($logFile, $errorMsg, FILE_APPEND);
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Git repository not initialized',
            'error' => $errorMsg
        ]);
        exit;
    }
    
    // Find git executable
    $gitPath = null;
    $gitPaths = ['git', '/usr/bin/git', '/usr/local/bin/git', '/opt/cpanel/ea-git/bin/git', '/usr/local/cpanel/3rdparty/bin/git'];
    
    foreach ($gitPaths as $path) {
        $output = [];
        $returnCode = 0;
        exec("$path --version 2>&1", $output, $returnCode);
        if ($returnCode === 0) {
            $gitPath = $path;
            file_put_contents($logFile, "Found git at: $path\n", FILE_APPEND);
            break;
        }
    }
    
    if (!$gitPath) {
        $errorMsg = "ERROR: Git executable not found. Tried: " . implode(", ", $gitPaths) . "\n";
        file_put_contents($logFile, $errorMsg, FILE_APPEND);
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Git executable not found',
            'error' => $errorMsg
        ]);
        exit;
    }
    
    // Set environment variables that git might need
    putenv('HOME=' . dirname($deployDir));
    putenv('GIT_DIR=' . $gitDir);
    putenv('GIT_WORK_TREE=' . $deployDir);
    
    // Check for and remove git lock file if it exists (from interrupted git operations)
    $lockFile = $gitDir . '/index.lock';
    if (file_exists($lockFile)) {
        $lockAge = time() - filemtime($lockFile);
        // Only remove if lock is older than 5 minutes (likely stale)
        if ($lockAge > 300) {
            unlink($lockFile);
            file_put_contents($logFile, "Removed stale git lock file (age: {$lockAge}s)\n", FILE_APPEND);
        } else {
            // Lock is recent, wait a bit and try again
            sleep(2);
            if (file_exists($lockFile)) {
                unlink($lockFile);
                file_put_contents($logFile, "Removed git lock file after wait\n", FILE_APPEND);
            }
        }
    }
    
    // Execute git fetch and reset (better for deployments - discards local changes)
    $output = [];
    $returnCode = 0;
    
    // First, fetch to make sure we have latest refs
    $fetchCmd = "cd " . escapeshellarg($deployDir) . " && $gitPath fetch origin main 2>&1";
    exec($fetchCmd, $fetchOutput, $fetchReturnCode);
    file_put_contents($logFile, "Fetch output: " . implode("\n", $fetchOutput) . "\n", FILE_APPEND);
    
    // Reset hard to match remote exactly (discards any local changes/divergence)
    // This is safer for deployments - we want the server to match GitHub exactly
    // NOTE: We do NOT run git clean -fd as it permanently deletes untracked files
    // If you need to clean untracked files, do it manually after verifying backups
    $resetCmd = "cd " . escapeshellarg($deployDir) . " && $gitPath reset --hard origin/main 2>&1";
    exec($resetCmd, $output, $returnCode);
    
    // Log the result
    $logEntry = "Reset command: $resetCmd\n";
    $logEntry .= "Reset output: " . implode("\n", $output) . "\n";
    $logEntry .= "Return code: $returnCode\n";
    
    // Check current commit after pull
    $currentCommit = [];
    exec("cd " . escapeshellarg($deployDir) . " && $gitPath log -1 --oneline 2>&1", $currentCommit, $commitReturnCode);
    $logEntry .= "Current commit after pull: " . implode("\n", $currentCommit) . "\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Return response
    if ($returnCode === 0) {
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Deployment completed',
            'commit' => $commitHash,
            'output' => implode("\n", $output),
            'current_commit' => implode("\n", $currentCommit)
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Git pull failed',
            'commit' => $commitHash,
            'output' => implode("\n", $output),
            'return_code' => $returnCode
        ]);
    }
} else {
    // Not a push to main, ignore
    $ref = isset($data['ref']) ? $data['ref'] : 'unknown';
    http_response_code(200);
    echo json_encode([
        'status' => 'ignored', 
        'message' => 'Not a push to main branch',
        'ref' => $ref
    ]);
}

