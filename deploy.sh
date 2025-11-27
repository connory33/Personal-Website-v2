#!/bin/bash
# Deployment script for cPanel
# Usage: Run this script from your cPanel server to pull latest changes from GitHub

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}Starting deployment...${NC}"

# Navigate to public_html
cd ~/public_html || {
    echo -e "${RED}Error: Could not navigate to public_html${NC}"
    exit 1
}

# Check if git is initialized
if [ ! -d .git ]; then
    echo -e "${YELLOW}Git not initialized. Initializing...${NC}"
    git init
    git remote add origin https://github.com/connory33/Personal-and-NHL-Website.git
fi

# Fetch latest changes
echo -e "${YELLOW}Pulling latest changes from GitHub...${NC}"
git fetch origin

# Check current branch
CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "main")

# Pull or checkout main branch
if [ "$CURRENT_BRANCH" = "main" ] || [ "$CURRENT_BRANCH" = "master" ]; then
    git pull origin main 2>/dev/null || git pull origin master 2>/dev/null
else
    echo -e "${YELLOW}Switching to main branch...${NC}"
    git checkout -b main origin/main 2>/dev/null || git checkout -b master origin/master 2>/dev/null
    git pull origin main 2>/dev/null || git pull origin master 2>/dev/null
fi

# Set proper file permissions
echo -e "${YELLOW}Setting file permissions...${NC}"
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Make PHP files executable if needed (optional)
# find . -name "*.php" -exec chmod 755 {} \;

echo -e "${GREEN}Deployment complete!${NC}"
echo -e "${GREEN}Latest commit: $(git log -1 --oneline)${NC}"


