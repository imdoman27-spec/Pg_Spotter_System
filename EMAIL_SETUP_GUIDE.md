# Free Email Service Setup Guide for PGSpotter

## Overview
Choose one of these FREE online email services to send contact replies:

| Service | Free Tier | Setup Time | Best For |
|---------|-----------|-----------|----------|
| **Mailtrap** ⭐ | Unlimited emails | 5 min | Testing & Development (see all emails in web inbox) |
| **SendGrid** | 100 emails/day | 5 min | Production (reliable, scalable) |
| **Brevo** | 300 emails/day | 5 min | Production (more generous limit) |

---

## Option 1: Mailtrap (Recommended for Testing) ⭐

**Best for:** Seeing all test emails instantly in a web dashboard

### Setup Steps:

#### Step 1: Create Mailtrap Account
1. Go to [https://mailtrap.io](https://mailtrap.io)
2. Click "Sign Up" (free account)
3. Verify your email

#### Step 2: Get SMTP Credentials
1. Log in to Mailtrap dashboard
2. Click on your **Inbox** (default inbox is created automatically)
3. Click **"Integrations"** tab
4. Select **"SMTP Settings"** or **"SMTP Server"**
5. Copy the credentials:
   - **Host:** `send.mailtrap.io`
   - **Port:** `587` (TLS)
   - **Username:** (looks like a number)
   - **Password:** (long random string)

#### Step 3: Configure PGSpotter
1. Open `includes/email_config.php`
2. Update:
```php
define('EMAIL_HOST', 'send.mailtrap.io');
define('EMAIL_PORT', 587);
define('EMAIL_USERNAME', 'paste-your-mailtrap-username');
define('EMAIL_PASSWORD', 'paste-your-mailtrap-password');
define('EMAIL_FROM_ADDRESS', 'noreply@pgspotter.com');
define('EMAIL_FROM_NAME', 'PGSpotter Admin');
define('EMAIL_SERVICE', 'smtp');
define('EMAIL_ENABLED', true);
```

#### Step 4: Test Email
1. Go to Admin Dashboard → Contact Messages
2. Click "Reply" on any message
3. Write a test reply and click "Send Reply via Email"
4. Go to [https://mailtrap.io](https://mailtrap.io) → Your Inbox
5. **You'll see the email appear instantly!** ✅

**Advantages:**
- ✅ See all emails in web interface
- ✅ No limits on testing
- ✅ Perfect for development
- ✅ Email appears instantly
- ✅ Can inspect email headers, attachments, etc.

**Disadvantages:**
- ❌ Only for testing (can't use for real customer emails)
- ❌ Emails don't actually reach customer mailbox

---

## Option 2: SendGrid (For Production) 

**Best for:** Sending real emails to actual customers

### Setup Steps:

#### Step 1: Create SendGrid Account
1. Go to [https://sendgrid.com](https://sendgrid.com)
2. Sign up for **free account** (100 emails/day limit)
3. Verify your email

#### Step 2: Get API Key
1. Log in to SendGrid dashboard
2. Go to **Settings** → **API Keys**
3. Click **"Create API Key"**
4. Name it: `PGSpotter Email`
5. Select **"Full Access"**
6. Copy the API key (long string starting with `SG.`)

#### Step 3: Verify Sender Email
1. Go to **Settings** → **Sender Authentication**
2. Click **"Verify a Single Sender"**
3. Enter your email address
4. Check your email for verification link
5. Verify the sender

#### Step 4: Configure PGSpotter
1. Open `includes/email_config.php`
2. Update to use SendGrid:
```php
define('EMAIL_SERVICE', 'sendgrid');
define('SENDGRID_API_KEY', 'SG.your-api-key-here');
define('EMAIL_FROM_ADDRESS', 'your-verified-email@gmail.com');
define('EMAIL_FROM_NAME', 'PGSpotter Admin');
define('EMAIL_ENABLED', true);
```

#### Step 5: Test Email
1. Go to Admin Dashboard → Contact Messages
2. Click "Reply" on any message
3. Send the email
4. Go to SendGrid dashboard → **Activity** to see sent emails

**Advantages:**
- ✅ Emails go to real customer inboxes
- ✅ 100 emails/day free
- ✅ Professional service
- ✅ See email delivery status
- ✅ Scalable for production

**Disadvantages:**
- ❌ Need to verify sender email
- ❌ Daily limit (100 emails)
- ❌ Emails don't appear instantly in dashboard

---

## Option 3: Brevo (Sendinblue)

**Best for:** Higher free email limit (300/day)

### Setup Steps:

#### Step 1: Create Brevo Account
1. Go to [https://www.brevo.com](https://www.brevo.com)
2. Sign up for free account
3. Verify email

#### Step 2: Get SMTP Credentials
1. Log in to Brevo dashboard
2. Click on your **Profile** (top right)
3. Select **"SMTP & API"**
4. Under **SMTP Settings**, copy:
   - **Host:** `smtp-relay.brevo.com`
   - **Port:** `587`
   - **Username:** Your Brevo email address
   - **Password:** (shown as "SMTP Key")

#### Step 3: Configure PGSpotter
1. Open `includes/email_config.php`
2. Update:
```php
define('EMAIL_HOST', 'smtp-relay.brevo.com');
define('EMAIL_PORT', 587);
define('EMAIL_USERNAME', 'your-brevo-email@gmail.com');
define('EMAIL_PASSWORD', 'your-smtp-key');
define('EMAIL_FROM_ADDRESS', 'your-brevo-email@gmail.com');
define('EMAIL_FROM_NAME', 'PGSpotter Admin');
define('EMAIL_SERVICE', 'smtp');
define('EMAIL_ENABLED', true);
```

#### Step 4: Test Email
1. Go to Admin Dashboard → Contact Messages
2. Click "Reply" on any message
3. Send the email
4. Check the recipient's email inbox

**Advantages:**
- ✅ 300 emails/day (generous free tier)
- ✅ Emails go to real customer inboxes
- ✅ SMTP-based (easy to configure)
- ✅ No sender verification needed
- ✅ Professional service

---

## Quick Recommendation

| Your Use Case | Choose |
|---------------|--------|
| **Just testing/developing** | 👉 **Mailtrap** (see emails instantly) |
| **Sending real customer emails** | 👉 **Brevo** (300 emails/day) or **SendGrid** (100 emails/day) |
| **Production ready** | 👉 **SendGrid** or upgrade to paid plan |

---

## Troubleshooting

### Problem: "Reply saved but email sending failed"

**Check these:**
1. Verify credentials are correct in `email_config.php`
2. For Mailtrap: Make sure you copied credentials from **"Integrations"** tab
3. For SendGrid: Make sure you have the **full API key**
4. For Brevo: Make sure you copied SMTP key (not API key)

### Problem: Email not appearing

**Mailtrap:**
- Refresh the Mailtrap inbox page
- Check if EMAIL_ENABLED is set to `true`

**SendGrid/Brevo:**
- Check recipient's spam folder
- Go to SendGrid Activity tab to see delivery status
- For Brevo, go to **Campaign** → **Reports**

### Problem: Connection timeout

**Try:**
1. Change port from 587 to 25 (if supported)
2. Check if your firewall is blocking the connection
3. Restart your XAMPP Apache server

---

## Upgrade Path

- **Mailtrap** → Use for development, then switch to SendGrid/Brevo for production
- **SendGrid** → Start free (100/day), upgrade to paid plan when needed
- **Brevo** → Start free (300/day), upgrade to paid plan when needed

---

**Which service would you like to use?** Let me know and I'll help you configure it!
