# Local Development Setup

## Viewing Your Site Locally

Since your site uses PHP, you have several options to view it locally:

### Option 1: PHP Built-in Server (Easiest - No Setup)

PHP comes with a built-in development server. Perfect for quick testing:

```bash
# Navigate to your project directory
cd C:\Users\conno\OneDrive\Documents\Personal-and-NHL-Website

# Start the PHP server (serves from public_html directory)
php -S localhost:8000 -t public_html

# Or if you want to serve from the root:
php -S localhost:8000
```

Then open your browser to: `http://localhost:8000`

**Note:** The built-in server doesn't support all features (like .htaccess), but it's great for basic PHP development.

### Option 2: XAMPP (Full-featured Local Server)

1. **Download XAMPP**: https://www.apachefriends.org/
2. **Install** XAMPP (includes Apache, MySQL, PHP)
3. **Copy your project**:
   - Copy `public_html` contents to `C:\xampp\htdocs\your-site-name\`
4. **Start Apache** from XAMPP Control Panel
5. **Access**: `http://localhost/your-site-name`

**Pros:**
- Full Apache server with .htaccess support
- MySQL database included
- More similar to production environment

### Option 3: Laragon (Windows-specific, Recommended)

1. **Download Laragon**: https://laragon.org/
2. **Install** Laragon
3. **Copy your project** to Laragon's `www` directory
4. **Start Laragon**
5. **Access**: `http://your-site-name.test` (auto-configured)

**Pros:**
- Very easy to use
- Auto SSL certificates
- Great for PHP development
- Includes MySQL, Redis, etc.

### Option 4: Docker (Advanced)

If you want containerized development:

```bash
# Create a docker-compose.yml in your project root
```

I can help you set this up if needed.

## Recommended Workflow

1. **Local Development**: Use Laragon or XAMPP for full-featured testing
2. **Quick Testing**: Use PHP built-in server for quick checks
3. **Commit Changes**: `git add .` and `git commit -m "message"`
4. **Push to GitHub**: `git push origin main`
5. **Auto-Deploy**: Your webhook/cron will update the live site

## Database Considerations

If your site uses a database:

- **Local**: Set up a local MySQL database (XAMPP/Laragon includes this)
- **Config**: Update `db_connection.php` to use local credentials
- **Don't commit**: Make sure production database credentials aren't in your repo

## File Paths

Your site structure:
- `public_html/` - Main website files (this is what gets served)
- `public_html/index.php` - Homepage
- `public_html/header.php`, `footer.php` - Includes

When testing locally, make sure paths in your PHP files work correctly (they should since you're using relative paths).

## Quick Start Script

Create a `start-local.bat` file in your project root:

```batch
@echo off
echo Starting local PHP server...
cd public_html
php -S localhost:8000
pause
```

Double-click to start your local server!

