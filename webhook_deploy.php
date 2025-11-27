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
    $logEntry = date('Y-m-d H:i:s') . " - Deploying commit: " . $commitHash . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Change to public_html directory (this file should be in public_html)
    chdir(__DIR__);
    
    // Execute git pull
    $output = [];
    $returnCode = 0;
    
    // Use full path to git if needed, or ensure git is in PATH
    // Try git pull, if it fails, try with full path
    exec('git pull origin main 2>&1', $output, $returnCode);
    
    // If that failed, try with explicit git path (common cPanel locations)
    if ($returnCode !== 0) {
        $gitPaths = ['/usr/bin/git', '/usr/local/bin/git', '/opt/cpanel/ea-git/bin/git'];
        foreach ($gitPaths as $gitPath) {
            if (file_exists($gitPath)) {
                exec("$gitPath pull origin main 2>&1", $output, $returnCode);
                break;
            }
        }
    }
    
    // Log the result
    $logEntry = "Output: " . implode("\n", $output) . "\n";
    $logEntry .= "Return code: $returnCode\n\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Return success
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Deployment triggered',
        'commit' => $commitHash,
        'output' => implode("\n", $output)
    ]);
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

