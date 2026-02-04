# Password Reset & Forgot Password Setup Guide

## Overview
This guide explains how to set up and configure the password reset and forgot password functionality for PG Spotter.

## Files Created/Modified

### 1. **handle_forgot_password.php** (NEW)
- Processes forgot password requests
- Generates secure reset tokens
- Stores token in database
- Sends password reset email to user
- Returns JSON response for AJAX handling

### 2. **reset_password.php** (NEW)
- Displays password reset form
- Validates reset token
- Checks token expiration
- Allows user to set new password
- Updates password in database
- Clears reset token after successful reset

### 3. **forgot_password.php** (EXISTING - Ready to use)
- Simple form that takes user email
- Submits to handle_forgot_password.php
- Already properly configured

### 4. **login.php** (EXISTING - Updated)
- Added Google login button with handler
- "Forgot Password?" link points to forgot_password.php
- Google login currently shows placeholder alert

### 5. **add_password_reset_columns.sql** (NEW)
- SQL migration file
- Adds `reset_token` and `reset_token_expiry` columns to users table
- Creates index for faster lookups

## Database Setup

### Step 1: Run Migration
Execute the SQL migration to add password reset columns:

```sql
-- Option A: Using phpMyAdmin
1. Go to phpMyAdmin
2. Select pgspotter_db database
3. Go to SQL tab
4. Copy and paste contents of database/add_password_reset_columns.sql
5. Click Execute

-- Option B: Using MySQL Command Line
mysql> USE pgspotter_db;
mysql> ALTER TABLE `users` ADD COLUMN `reset_token` VARCHAR(255) DEFAULT NULL AFTER `profile_pic`;
mysql> ALTER TABLE `users` ADD COLUMN `reset_token_expiry` DATETIME DEFAULT NULL AFTER `reset_token`;
mysql> CREATE INDEX `idx_reset_token` ON `users` (`reset_token`);
```

### Step 2: Verify Migration
```sql
DESCRIBE users;
-- Should show: reset_token, reset_token_expiry columns
```

## Email Configuration

The password reset system uses PHP's `mail()` function. For production, configure email in `includes/config.php`:

### Option 1: Using SendGrid API (Recommended)
```php
define('SENDGRID_API_KEY', 'your_sendgrid_api_key_here');
```

### Option 2: Using Mailtrap (Testing)
```php
define('EMAIL_HOST', 'smtp.mailtrap.io');
define('EMAIL_PORT', 465);
define('EMAIL_USERNAME', 'your_username');
define('EMAIL_PASSWORD', 'your_password');
```

### Option 3: Using System Mail (Local Development)
- No configuration needed
- Uses server's default mail configuration
- Good for local testing with Mailtrap

## How It Works

### User Forgot Password Flow:
1. User clicks "Forgot Password?" on login page
2. User enters email on forgot_password.php
3. Form submits to handle_forgot_password.php (POST)
4. Backend generates unique reset token
5. Token is saved to database with 1-hour expiry
6. Password reset email is sent to user
7. Email contains link: `reset_password.php?token=<token>`
8. User receives confirmation message

### User Reset Password Flow:
1. User clicks link in email
2. reset_password.php validates token
3. If valid and not expired, shows password form
4. User enters new password
5. Password is hashed with bcrypt
6. Database is updated
7. Reset token is cleared
8. User is redirected to login.php

## Testing the System

### Local Testing (Without Real Email)
1. Open phpMyAdmin
2. Run the SQL migration
3. Go to: `http://localhost/pg_spotter_project/forgot_password.php`
4. Enter a registered user's email
5. Check database for the reset token:
   ```sql
   SELECT email, reset_token FROM users WHERE email = 'test@gmail.com';
   ```
6. Manually construct URL: `http://localhost/pg_spotter_project/reset_password.php?token=<token_value>`
7. Test the password reset form

### With Real Email Testing
1. Configure SendGrid or Mailtrap in config.php
2. Get API key/credentials from respective services
3. Update config.php with credentials
4. Follow "Local Testing" steps above
5. Check email inbox for reset link

## Security Considerations

✅ **Implemented:**
- Tokens generated with `random_bytes(32)` (cryptographically secure)
- Tokens are hexadecimal (bin2hex conversion)
- Tokens expire after 1 hour
- Tokens are cleared after successful reset
- Passwords are hashed with bcrypt (PASSWORD_BCRYPT)
- SQL injection protection (prepared statements)
- Email validation (FILTER_VALIDATE_EMAIL)
- Security headers for JSON responses

## Troubleshooting

### Issue: "Database error occurred"
- **Solution:** Check database connection in config.php
- Run: `php -l includes/config.php`

### Issue: Reset email not received
- **Solution:** Check email configuration
- Verify SMTP credentials in config.php
- Check spam/junk folder
- For local testing, use Mailtrap to view emails

### Issue: Token validation fails
- **Solution:** Run database migration
- Verify columns exist: `DESCRIBE users;`
- Check token hasn't expired (1 hour limit)

### Issue: "Invalid reset link"
- **Solution:** Token expired or already used
- Have user request new password reset
- Ensure URL token matches database token exactly

## Google Login Integration

Google login button is ready for OAuth 2.0 implementation:

1. Set up Google OAuth in Google Cloud Console
2. Add your OAuth credentials to config.php
3. Replace placeholder alert with OAuth redirect
4. Update handle_login.php to handle OAuth response

For detailed instructions, see Google OAuth 2.0 documentation.

## File Locations

```
pg_spotter_project/
├── handle_forgot_password.php (NEW - Backend handler)
├── reset_password.php (NEW - Reset form)
├── forgot_password.php (EXISTING - Request form)
├── login.php (EXISTING - Updated with Google button)
├── includes/
│   └── config.php (Email configuration here)
└── database/
    └── add_password_reset_columns.sql (NEW - Database migration)
```

## API Responses

### handle_forgot_password.php (POST)

**Success (Email exists):**
```json
{
    "success": true,
    "message": "If this email exists, you will receive a password reset link shortly."
}
```

**Error (Invalid email):**
```json
{
    "success": false,
    "message": "Invalid email address"
}
```

## Next Steps

1. ✅ Run database migration
2. ✅ Configure email settings in config.php
3. ✅ Test forgot password flow
4. ✅ Test reset password flow
5. ⏳ Integrate Google OAuth 2.0
6. ⏳ Add email verification system
7. ⏳ Add password strength requirements

## Support

For issues or questions:
- Check error logs: `logs/errors.log`
- Test email configuration with test script
- Verify database schema with phpMyAdmin
- Check PHP version requirements (PHP 7.2+)
