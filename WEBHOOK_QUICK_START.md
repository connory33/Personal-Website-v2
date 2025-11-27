# Webhook Quick Start Checklist

Follow these steps in order:

## ✅ Step 1: Generate Secret Token
- [ ] Go to: https://www.random.org/strings/
- [ ] Generate a 32-character alphanumeric string
- [ ] **COPY AND SAVE IT** (you'll need it twice)

## ✅ Step 2: Update webhook_deploy.php
- [ ] Open `webhook_deploy.php`
- [ ] Find: `$secret = 'YOUR_SECRET_TOKEN_HERE_CHANGE_THIS';`
- [ ] Replace with: `$secret = 'your_generated_token_here';`
- [ ] Save the file

## ✅ Step 3: Upload to cPanel
- [ ] Log into cPanel → File Manager
- [ ] Navigate to `public_html`
- [ ] Upload `webhook_deploy.php`
- [ ] Set permissions to `644`

## ✅ Step 4: Set Up GitHub Webhook
- [ ] Go to: https://github.com/connory33/Personal-Website-v2/settings/hooks
- [ ] Click "Add webhook"
- [ ] Payload URL: `https://connoryoung.com/webhook_deploy.php`
- [ ] Content type: `application/json`
- [ ] Secret: (paste your token from Step 1)
- [ ] Events: "Just the push event"
- [ ] Click "Add webhook"

## ✅ Step 5: Test It!
- [ ] Make a small change (or commit existing changes)
- [ ] Push: `git push origin main`
- [ ] Check GitHub webhook page - should show ✅ green checkmark
- [ ] Check your website - should update automatically!

---

**Full instructions**: See `WEBHOOK_SETUP.md` for detailed troubleshooting.

