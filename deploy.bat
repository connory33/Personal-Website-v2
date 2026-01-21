@echo off
REM Windows deployment script (for local testing or if you have Windows server)
REM This is mainly for reference - cPanel typically runs on Linux

echo Starting deployment...

cd public_html

REM Check if git is initialized
if not exist .git (
    echo Git not initialized. Please initialize manually.
    pause
    exit /b 1
)

REM Pull latest changes
echo Pulling latest changes from GitHub...
git pull origin main

echo Deployment complete!
pause



