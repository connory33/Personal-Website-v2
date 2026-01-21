# API 404 Error - Troubleshooting

## The Problem
Getting "404 Not Found" when trying to access API endpoints.

## Quick Checks

### 1. Are the files uploaded to the server?
The API files need to be in `public_html/api/` on your cPanel server:
- `public_html/api/health.php`
- `public_html/api/players.php`
- `public_html/api/games.php`
- etc.

**Check:** Log into cPanel File Manager and verify the files exist at `/public_html/api/`

### 2. Test the API directly
Try accessing the API directly in your browser:
```
https://connoryoung.com/api/health.php
```

**Expected result:** Should return JSON like:
```json
{"status":"ok","timestamp":"2025-01-XX..."}
```

**If you get 404:** Files aren't uploaded or path is wrong.

**If you get 500 error:** PHP error - check error logs.

### 3. Check file permissions
In cPanel File Manager:
- Right-click `api` folder → Change Permissions
- Set to `755` (folders) or `644` (files)

### 4. Verify the path
The React app calls `/api/health.php` which should resolve to:
- `https://connoryoung.com/api/health.php` ✅

If your React app is at `/nhl-react-frontend/`, the relative path `/api/` should still work because it's an absolute path from the root.

## Common Issues

### Issue 1: Files not uploaded
**Solution:** Upload all files from `public_html/api/` to your cPanel server.

### Issue 2: Wrong path in React app
**Solution:** The API base URL should be `/api` (absolute from root), not `./api` or `../api`

### Issue 3: PHP not enabled
**Solution:** Check that PHP is enabled in cPanel (usually is by default)

### Issue 4: Database connection error
**Solution:** Check that `db_connection.php` path is correct in API files (`../db_connection.php`)

## Testing Steps

1. **Test API directly:**
   ```
   https://connoryoung.com/api/health.php
   ```

2. **Test with parameters:**
   ```
   https://connoryoung.com/api/players.php?page=1&limit=5
   ```

3. **Check browser console:**
   - Open React app
   - Open browser DevTools (F12)
   - Check Network tab
   - See what URL is being called
   - Check if it's getting 404

4. **Check server error logs:**
   - In cPanel → Error Logs
   - Look for PHP errors

## Quick Fix

If files aren't uploaded yet:

1. **Upload via cPanel File Manager:**
   - Navigate to `public_html/`
   - Create folder `api` if it doesn't exist
   - Upload all `.php` files from `public_html/api/` to `public_html/api/` on server

2. **Or upload via Git:**
   ```bash
   git add public_html/api/
   git commit -m "Add API endpoints"
   git push
   ```

3. **Then test:**
   ```
   https://connoryoung.com/api/health.php
   ```

## Still Not Working?

Share:
1. The exact URL you're trying to access
2. Whether you've uploaded the files to the server
3. What you see when you visit `https://connoryoung.com/api/health.php` directly


