# Using Git Version Control with cPanel Hosting

There are several ways to use Git with cPanel hosting. Here are the most common approaches:

## Method 1: Git Hook Deployment (Recommended)

This method automatically deploys your code when you push to your repository.

### Setup Steps:

1. **SSH into your cPanel server** (if SSH access is enabled)
   ```bash
   ssh username@yourdomain.com
   ```

2. **Navigate to your public_html directory**
   ```bash
   cd ~/public_html
   ```

3. **Initialize a bare Git repository** (if not already done)
   ```bash
   git init --bare ~/repo.git
   ```

4. **Create a post-receive hook** in `~/repo.git/hooks/post-receive`:
   ```bash
   #!/bin/bash
   GIT_WORK_TREE=~/public_html git checkout -f
   ```

5. **Make the hook executable**
   ```bash
   chmod +x ~/repo.git/hooks/post-receive
   ```

6. **Add remote to your local repository**
   ```bash
   git remote add production username@yourdomain.com:~/repo.git
   ```

7. **Push to deploy**
   ```bash
   git push production main
   ```

## Method 2: Manual Pull via SSH

If you have SSH access, you can pull directly on the server:

1. **SSH into your server**
2. **Navigate to public_html**
   ```bash
   cd ~/public_html
   ```
3. **Initialize git** (if not already done)
   ```bash
   git init
   git remote add origin https://github.com/yourusername/yourrepo.git
   ```
4. **Pull updates**
   ```bash
   git pull origin main
   ```

## Method 3: cPanel Git Version Control (if available)

Many cPanel hosts have built-in Git support:

1. Log into cPanel
2. Find "Git Version Control" in the Software section
3. Create a new repository or clone an existing one
4. Set the deployment path to `public_html`
5. Use the "Pull or Deploy" button to update your site

## Method 4: Deployment Script via cPanel Cron Jobs

Create a deployment script and run it via cron:

1. **Create a deployment script** (`~/deploy.sh`):
   ```bash
   #!/bin/bash
   cd ~/public_html
   git pull origin main
   ```

2. **Make it executable**
   ```bash
   chmod +x ~/deploy.sh
   ```

3. **Set up a cron job in cPanel** to run the script periodically or manually trigger it

## Method 5: Manual File Upload (Fallback)

If Git isn't available on your server:

1. Push changes to GitHub/GitLab
2. Download the repository as ZIP
3. Upload via cPanel File Manager
4. Extract files to public_html

## Recommended Workflow

1. **Local Development**: Make changes locally
2. **Commit**: `git add .` and `git commit -m "message"`
3. **Push to GitHub**: `git push origin main`
4. **Deploy**: Use one of the methods above to sync to cPanel

## Important Notes

- **File Permissions**: Ensure PHP files have correct permissions (usually 644)
- **Database Configs**: Don't commit sensitive database credentials. Use environment variables or separate config files
- **Large Files**: Use Git LFS for large files (you already have this set up)
- **Backup**: Always backup your site before deploying
- **.gitignore**: Make sure sensitive files are ignored (already configured)

## Troubleshooting

- **SSH Access**: Contact your host to enable SSH if not available
- **Git Not Installed**: Ask your host to install Git on the server
- **Permission Errors**: Check file ownership and permissions
- **Large File Errors**: Use Git LFS (you're already using this)


