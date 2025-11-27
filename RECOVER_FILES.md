# Recovering Files After git clean -fd

## Immediate Recovery Steps

### Step 1: Restore All Files from GitHub

Run these commands in cPanel Terminal:

```bash
cd ~/public_html
git fetch origin main
git reset --hard origin/main
```

This will restore all tracked files from GitHub.

### Step 2: Check cPanel Backups

Most cPanel hosts have automatic backups:

1. **Log into cPanel**
2. **Look for "Backups"** in the main menu
3. **Check "Restore a Home Directory Backup"** or **"Backup Wizard"**
4. **Select a backup from before you ran git clean**
5. **Restore the public_html directory**

### Step 3: Check File Manager Trash

Some hosts keep deleted files in trash:

1. **cPanel → File Manager**
2. **Look for a "Trash" or "Deleted Files" folder**
3. **Restore any files you find**

### Step 4: Check if Files Were Tracked

If files were tracked in git, they should be restored by Step 1. If they were untracked (not in git), you'll need backups.

## What Happened

- `git reset --hard origin/main` - Reset tracked files to match GitHub (this is recoverable)
- `git clean -fd` - **Permanently deleted untracked files** (these need backups to recover)

## Prevention for Future

I'll update the webhook script to be safer - it should NOT run `git clean` automatically as it's too dangerous.

