# Connecting cPanel to Your Git Repository

Your repository: `https://github.com/connory33/Personal-and-NHL-Website.git`

## Method 1: Using cPanel Git Version Control (Easiest - if available)

Many modern cPanel installations have built-in Git support:

### Steps:

1. **Log into cPanel**
   - Navigate to your cPanel dashboard

2. **Find Git Version Control**
   - Look in the "Software" section
   - If you don't see it, your host may not have it enabled (try Method 2)

3. **Create Repository**
   - Click "Git Version Control" or "Create"
   - Repository Name: `Personal-and-NHL-Website` (or any name)
   - Repository URL: `https://github.com/connory33/Personal-and-NHL-Website.git`
   - Repository Branch: `main` (or `master` if that's your default)
   - Deployment Path: `/home/yourusername/public_html` (or just `public_html`)

4. **Clone Repository**
   - Click "Clone" or "Create"
   - Enter your GitHub credentials if prompted (or use SSH key)

5. **Deploy**
   - After cloning, click "Pull or Deploy" to sync files
   - Set up automatic deployment if desired

## Method 2: Using SSH Terminal in cPanel

If your cPanel has Terminal/SSH access:

### Steps:

1. **Open Terminal in cPanel**
   - Look for "Terminal" in the "Advanced" section
   - Or use SSH client (PuTTY on Windows, Terminal on Mac/Linux)

2. **Navigate to public_html**
   ```bash
   cd ~/public_html
   ```

3. **Initialize Git** (if not already done)
   ```bash
   git init
   ```

4. **Add your GitHub repository as remote**
   ```bash
   git remote add origin https://github.com/connory33/Personal-and-NHL-Website.git
   ```

5. **Pull from GitHub**
   ```bash
   git pull origin main
   ```
   (Use `master` instead of `main` if that's your default branch)

6. **Set up for future updates**
   ```bash
   git branch --set-upstream-to=origin/main main
   ```

## Method 3: Using SSH Keys (Most Secure)

For passwordless authentication:

### Steps:

1. **Generate SSH Key** (if you don't have one)
   ```bash
   ssh-keygen -t ed25519 -C "your_email@example.com"
   ```

2. **Copy your public key**
   ```bash
   cat ~/.ssh/id_ed25519.pub
   ```

3. **Add SSH Key to GitHub**
   - Go to GitHub → Settings → SSH and GPG keys
   - Click "New SSH key"
   - Paste your public key

4. **In cPanel Terminal, clone using SSH**
   ```bash
   cd ~
   git clone git@github.com:connory33/Personal-and-NHL-Website.git temp_repo
   cp -r temp_repo/* public_html/
   rm -rf temp_repo
   ```

## Method 4: Using cPanel File Manager + Manual Git

If you don't have Terminal access:

1. **Download repository as ZIP**
   - Go to: https://github.com/connory33/Personal-and-NHL-Website/archive/refs/heads/main.zip
   - Download the ZIP file

2. **Upload via cPanel File Manager**
   - Log into cPanel → File Manager
   - Navigate to `public_html`
   - Upload the ZIP file
   - Extract it
   - Move contents to `public_html` root

3. **For updates**: Repeat this process or use Method 5

## Method 5: Automated Deployment Script

Create a deployment script you can run via cron or manually:

### Create `deploy.sh` in your home directory:

```bash
#!/bin/bash
cd ~/public_html

# Check if git is initialized
if [ ! -d .git ]; then
    git init
    git remote add origin https://github.com/connory33/Personal-and-NHL-Website.git
fi

# Pull latest changes
git fetch origin
git reset --hard origin/main

# Set proper permissions (adjust as needed)
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
```

### Set up Cron Job in cPanel:

1. Go to cPanel → Cron Jobs
2. Add new cron job:
   - **Minute**: `0`
   - **Hour**: `*` (or specific hour)
   - **Day**: `*`
   - **Month**: `*`
   - **Weekday**: `*`
   - **Command**: `bash ~/deploy.sh`

Or run manually via Terminal when needed.

## Method 6: Git Hook Deployment (Advanced)

Set up automatic deployment when you push to GitHub:

### On Your cPanel Server:

1. **Create bare repository**
   ```bash
   cd ~
   git clone --bare https://github.com/connory33/Personal-and-NHL-Website.git repo.git
   ```

2. **Create post-receive hook**
   ```bash
   cat > ~/repo.git/hooks/post-receive << 'EOF'
   #!/bin/bash
   GIT_WORK_TREE=~/public_html git checkout -f main
   EOF
   ```

3. **Make hook executable**
   ```bash
   chmod +x ~/repo.git/hooks/post-receive
   ```

4. **On your local machine, add production remote**
   ```bash
   git remote add production username@yourdomain.com:~/repo.git
   ```

5. **Deploy by pushing**
   ```bash
   git push production main
   ```

## Recommended Setup for Your Workflow

Based on your repository structure, I recommend:

1. **Initial Setup**: Use Method 2 (SSH Terminal) to clone your repo
2. **Ongoing Updates**: Use Method 5 (Deployment Script) for easy updates
3. **Future**: Consider Method 6 (Git Hook) for automatic deployment

## Important Notes

- **File Paths**: Your `public_html` folder should contain the files from your repo
- **Database Configs**: Make sure `db_connection.php` and other config files are not committed with production credentials
- **Large Files**: You're using Git LFS, make sure it's installed on the server if you need those files
- **Permissions**: PHP files typically need 644 permissions, directories need 755

## Troubleshooting

- **"Git command not found"**: Ask your host to install Git
- **"Permission denied"**: Check file ownership and permissions
- **"Repository not found"**: Verify the repository URL and your access
- **"Authentication failed"**: Use SSH keys or check your GitHub credentials

## Quick Check Commands

After setup, verify connection:
```bash
cd ~/public_html
git remote -v
git status
git log --oneline -5
```


