# Database Setup - Password Reset System

## SQL Migration Instructions

### Method 1: Using phpMyAdmin (Recommended)

**Step 1:** Open phpMyAdmin
- URL: `http://localhost/phpmyadmin`

**Step 2:** Select Database
- Left sidebar → Click `pgspotter_db`

**Step 3:** Go to SQL Tab
- Top menu → Click `SQL` tab

**Step 4:** Import SQL
- Copy contents of: `database/add_password_reset_columns.sql`
- Paste into SQL editor window

**Step 5:** Execute
- Click `Go` button or press Ctrl+Enter
- You should see: "2 rows affected" or similar message

### Method 2: Using MySQL Command Line

```bash
# Navigate to project directory
cd c:\xampp\htdocs\pg_spotter_project

# Run migration
mysql -u root pgspotter_db < database/add_password_reset_columns.sql

# Or if you need to specify password (if set)
mysql -u root -p pgspotter_db < database/add_password_reset_columns.sql
# Then enter password when prompted
```

### Method 3: Direct MySQL Execution

```sql
-- Connect to database
USE pgspotter_db;

-- Add reset_token column
ALTER TABLE `users` ADD COLUMN `reset_token` VARCHAR(255) DEFAULT NULL AFTER `profile_pic`;

-- Add reset_token_expiry column
ALTER TABLE `users` ADD COLUMN `reset_token_expiry` DATETIME DEFAULT NULL AFTER `reset_token`;

-- Create index for faster lookups
CREATE INDEX `idx_reset_token` ON `users` (`reset_token`);
```

## Verification

### Step 1: Check if Columns Exist
In phpMyAdmin or MySQL:

```sql
DESCRIBE users;
```

You should see columns:
- `reset_token` (VARCHAR 255)
- `reset_token_expiry` (DATETIME)

### Step 2: Check Index
```sql
SHOW INDEX FROM users;
```

You should see:
- `idx_reset_token` index on `reset_token` column

### Step 3: Run Test Script
Visit: `http://localhost/pg_spotter_project/test_password_reset.php`

Should show ✅ for:
- Database connection
- Password reset columns
- All required files

## SQL Content Reference

If you need to manually execute the migration, here's what it contains:

```sql
-- Add password reset columns to users table
ALTER TABLE `users` ADD COLUMN `reset_token` VARCHAR(255) DEFAULT NULL AFTER `profile_pic`;
ALTER TABLE `users` ADD COLUMN `reset_token_expiry` DATETIME DEFAULT NULL AFTER `reset_token`;

-- Create index for faster token lookups
CREATE INDEX `idx_reset_token` ON `users` (`reset_token`);
```

## Rollback (If Needed)

To remove these changes:

```sql
-- Drop index
DROP INDEX `idx_reset_token` ON `users`;

-- Drop columns
ALTER TABLE `users` DROP COLUMN `reset_token_expiry`;
ALTER TABLE `users` DROP COLUMN `reset_token`;
```

## Important Notes

- ⚠️ **BACKUP YOUR DATABASE FIRST!**
  - In phpMyAdmin: Export → pgspotter_db
  - Save the SQL file somewhere safe

- ✅ This migration is safe
  - No data loss
  - Adds optional columns only
  - Can be rolled back

- 🔒 These columns are used for:
  - `reset_token`: Stores secure token for password reset
  - `reset_token_expiry`: Stores expiry timestamp (1 hour from request)
  - Index: Makes token lookups fast

## User Table Structure (After Migration)

```
Column Name           | Type         | Key     | Default
─────────────────────┼──────────────┼─────────┼──────────
user_id              | INT          | PRIMARY | -
full_name            | VARCHAR(100) | -       | -
email                | VARCHAR(100) | UNIQUE  | -
password             | VARCHAR(255) | -       | -
user_type            | ENUM         | -       | 'tenant'
created_at           | TIMESTAMP    | -       | NOW()
profile_pic          | VARCHAR(255) | -       | NULL
reset_token          | VARCHAR(255) | INDEX   | NULL ← NEW
reset_token_expiry   | DATETIME     | -       | NULL ← NEW
```

## Testing After Migration

1. Run test script:
   - Visit: `test_password_reset.php`

2. Test forgot password:
   - Go to: `forgot_password.php`
   - Enter registered email
   - Check database for token

3. Verify in phpMyAdmin:
   - Select pgspotter_db
   - Click users table
   - Check for reset_token value (should have a long hex string)

## Troubleshooting

### Error: "Table 'pgspotter_db.users' doesn't exist"
- Solution: Make sure you're using the correct database name
- Check: `includes/config.php` for correct DB name

### Error: "Column 'reset_token' already exists"
- Solution: Migration already ran successfully
- Check: `DESCRIBE users` to confirm

### Error: "Access denied"
- Solution: Check MySQL credentials
- Update: Username/password in your MySQL setup
- Or run with correct user: `mysql -u root -p ...`

### Migration didn't show any message
- Check: phpMyAdmin browser console for errors
- Or: Run `DESCRIBE users` to verify columns exist

## Support

For issues:
1. Run `test_password_reset.php`
2. Check the error message it provides
3. See `PASSWORD_RESET_SETUP.md` for detailed troubleshooting
4. Verify database backup exists before trying anything

## Next Steps

After successful migration:
1. ✅ Database ready
2. ⏳ Test the system: visit `test_password_reset.php`
3. ⏳ Test forgot password: visit `forgot_password.php`
4. ⏳ Optional: Configure email in `includes/config.php`

---

**Status:** ✅ Ready
**Last Updated:** 2025-11-01
**Required For:** Password Reset System v1.0
