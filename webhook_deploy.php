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
    
    // Execute git fetch and reset (better for deployments - discards local changes)
    $output = [];
    $returnCode = 0;
    
    // First, fetch to make sure we have latest refs
    $fetchCmd = "cd " . escapeshellarg($deployDir) . " && $gitPath fetch origin main 2>&1";
    exec($fetchCmd, $fetchOutput, $fetchReturnCode);
    file_put_contents($logFile, "Fetch output: " . implode("\n", $fetchOutput) . "\n", FILE_APPEND);
    
    // Reset hard to match remote exactly (discards any local changes/divergence)
    // This is safer for deployments - we want the server to match GitHub exactly
    $resetCmd = "cd " . escapeshellarg($deployDir) . " && $gitPath reset --hard origin/main 2>&1";
    exec($resetCmd, $output, $returnCode);
    
    // If reset worked, also clean up any untracked files (optional but recommended)
    if ($returnCode === 0) {
        $cleanCmd = "cd " . escapeshellarg($deployDir) . " && $gitPath clean -fd 2>&1";
        exec($cleanCmd, $cleanOutput, $cleanReturnCode);
        if (!empty($cleanOutput)) {
            file_put_contents($logFile, "Clean output: " . implode("\n", $cleanOutput) . "\n", FILE_APPEND);
        }
    }
    
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

