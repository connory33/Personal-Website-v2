# GitHub Webhook Setup Guide

This guide will help you set up automatic deployment from GitHub to your cPanel server.

## Step 1: Generate a Secret Token

You need a secure random string to verify webhook requests. Choose one method:

### Option A: Online Generator
1. Go to: https://www.random.org/strings/
2. Set:
   - **Length**: 32
   - **Type**: Alphanumeric
   - Click "Generate"
3. Copy the generated string

### Option B: Command Line (if you have access)
```bash
# On Linux/Mac
openssl rand -hex 32

# On Windows PowerShell
-join ((48..57) + (65..90) + (97..122) | Get-Random -Count 32 | ForEach-Object {[char]$_})
```

### Option C: Manual
Just create a long random string like: `MySecretToken2024!DeployWebhook#123`

**Save this token** - you'll need it in both Step 2 and Step 3!

---

## Step 2: Update webhook_deploy.php

1. Open `webhook_deploy.php` in your editor
2. Find this line:
   ```php
   $secret = 'YOUR_SECRET_TOKEN_HERE_CHANGE_THIS';
   ```
3. Replace `YOUR_SECRET_TOKEN_HERE_CHANGE_THIS` with your generated token:
   ```php
   $secret = 'your_actual_secret_token_here';
   ```
4. Save the file

---

## Step 3: Upload to cPanel

1. **Log into cPanel**
2. **Open File Manager**
3. **Navigate to `public_html`** (or your website root)
4. **Upload `webhook_deploy.php`** to this directory
5. **Set permissions** (right-click → Change Permissions):
   - Set to `644` (readable by web server, writable by owner)

**Important**: Make sure the file is in the same directory where your git repository is initialized (where you ran `git init`).

---

## Step 4: Set Up GitHub Webhook

1. **Go to your GitHub repository**: https://github.com/connory33/Personal-Website-v2
2. **Click "Settings"** (top menu)
3. **Click "Webhooks"** (left sidebar)
4. **Click "Add webhook"** (top right)
5. **Fill in the webhook form**:
   - **Payload URL**: `https://connoryoung.com/webhook_deploy.php`
     - Replace `connoryoung.com` with your actual domain if different
   - **Content type**: Select `application/json`
   - **Secret**: Paste your secret token (the same one from Step 1)
   - **Which events**: Select "Just the push event"
   - **Active**: ✅ Checked (should be checked by default)
6. **Click "Add webhook"**

---

## Step 5: Test the Webhook

1. **Make a small change** to your repository (or just commit and push existing changes)
2. **Push to GitHub**:
   ```bash
   git add .
   git commit -m "Test webhook deployment"
   git push origin main
   ```
3. **Check GitHub Webhook**:
   - Go back to Settings → Webhooks
   - Click on your webhook
   - You should see recent deliveries with green checkmarks ✅
4. **Check your website** - it should update automatically!
5. **Check the log file** (optional):
   - In cPanel File Manager, look for `deploy.log` in `public_html`
   - This will show deployment history

---

## Troubleshooting

### Webhook shows "Failed" in GitHub

1. **Check the webhook URL** - make sure it's accessible:
   - Try visiting `https://connoryoung.com/webhook_deploy.php` in your browser
   - You should see a JSON response (even if it says "ignored")

2. **Check file permissions**:
   - The file should be readable (644)
   - The directory should be executable (755)

3. **Check the secret token**:
   - Make sure it matches exactly in both `webhook_deploy.php` and GitHub
   - No extra spaces or characters

### Webhook succeeds but site doesn't update

1. **Check deploy.log**:
   - Look at `public_html/deploy.log` for error messages
   - Common issues:
     - Git not found: Need to specify full path to git
     - Permission errors: Git may not have write access
     - Not in correct directory: Git repo not initialized in public_html

2. **Verify git is set up correctly**:
   - SSH into cPanel terminal
   - Run: `cd ~/public_html && git status`
   - Should show git repository status

3. **Check git remote**:
   - Run: `git remote -v`
   - Should show your GitHub repository URL

### Webhook returns 403 Forbidden

- **Secret token mismatch**: Double-check the token in both places
- **Signature verification failing**: Make sure you're using the exact same token

### Git command not found

If you see "git: command not found" in the logs:

1. **Find git location** on your server:
   ```bash
   which git
   # or
   whereis git
   ```

2. **Update webhook_deploy.php** to use full path:
   ```php
   exec('/usr/bin/git pull origin main 2>&1', $output, $returnCode);
   ```

---

## Security Considerations

### Option 1: Protect with .htaccess (Recommended)

Create a `.htaccess` file in `public_html` with:

```apache
<Files "webhook_deploy.php">
    # Only allow POST requests
    <LimitExcept POST>
        Require all denied
    </LimitExcept>
    
    # Optional: Restrict by IP (GitHub webhook IPs)
    # Require ip 140.82.112.0/20
    # Require ip 192.30.252.0/22
</Files>
```

### Option 2: Move to Subdirectory

1. Create a directory: `public_html/webhooks/`
2. Move `webhook_deploy.php` there
3. Update GitHub webhook URL to: `https://connoryoung.com/webhooks/webhook_deploy.php`
4. Add `.htaccess` in that directory to restrict access

### Option 3: Use a Non-Obvious Filename

Instead of `webhook_deploy.php`, use something like:
- `deploy_abc123xyz.php`
- `update_site_secret.php`

This makes it harder for attackers to find the endpoint.

---

## Verify It's Working

After setup, you should see:

1. ✅ **GitHub Webhook**: Shows successful deliveries (green checkmarks)
2. ✅ **Website Updates**: Changes appear on your site within seconds
3. ✅ **Deploy Log**: `deploy.log` file shows deployment history

---

## Next Steps

Once working, you can:
- Monitor deployments via `deploy.log`
- Set up email notifications (modify the PHP script)
- Add deployment status badges to your README
- Set up staging/production environments

---

## Need Help?

If you're stuck:
1. Check `deploy.log` for error messages
2. Check GitHub webhook delivery logs (click on the webhook → Recent Deliveries)
3. Verify git is working: `cd ~/public_html && git pull origin main` (manual test)

