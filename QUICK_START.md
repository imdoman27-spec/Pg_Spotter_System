# PG Spotter - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Prerequisites
- PHP 7.4+
- MySQL 5.7+
- Web Server (Apache/Nginx)
- XAMPP installed (for local development)

---

## ⚡ Quick Setup

### Step 1: Database Migration (2 minutes)

#### Option A: Using phpMyAdmin
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select database: `pgspotter_db`
3. Click "SQL" tab
4. Copy & paste contents of `database/add_reviews_table.sql`
5. Click "Go"
6. ✅ Done!

#### Option B: Using Command Line
```bash
cd c:\xampp\htdocs\pg_spotter_project
mysql -u root -p pgspotter_db < database/add_reviews_table.sql
```

### Step 2: Verify Files Exist (1 minute)
Check these files are in your project:
- ✅ `/chatbot.html`
- ✅ `/chatbot_api.php`
- ✅ `/handle_review.php`
- ✅ `/track_view.php`
- ✅ `/dashboards/insights.php`
- ✅ `/dashboards/manage_reviews.php`

### Step 3: Clear Browser Cache (1 minute)
- Press `Ctrl + Shift + Delete` (Chrome/Edge/Firefox)
- Clear "All time"
- Close and reopen browser

### Step 4: Test Each Feature (2 minutes)

---

## 📋 Feature Testing Guide

### Test 1: Review System ⭐
```
1. Login as Tenant (username: test_tenant)
2. Go to: PG Details page
3. Find: "Reviews & Ratings" section (scroll down)
4. Action: Click stars (1-5) to rate
5. Type a test comment
6. Click "Submit Review"
7. ✅ Should reload and show your review
```

**Expected Result**: Review appears with your name, rating, and comment

---

### Test 2: View Tracking 👀
```
1. Open Browser DevTools (F12)
2. Go to: Network Tab
3. Load any PG Details page
4. Look for: "track_view.php" request
5. Click on it, view Response tab
6. ✅ Should show {"success": true, "view_count": NUMBER}
```

**Expected Result**: View count increments each time you load page

---

### Test 3: Chatbot 🤖
```
1. Scroll to bottom-right corner
2. Look for: Purple chat bubble with comment icon
3. Click: The purple bubble
4. Type: "how to search"
5. Press: Enter or click send button
6. ✅ Should respond with answer about searching
```

**Try these questions:**
- "How to search"
- "Rent price"
- "Security deposit"
- "Amenities"
- "How to book"
- "Review"

**Expected Result**: Immediate answer appears in chat

---

### Test 4: Insights Dashboard 👁️
```
For Owners:
1. Login as Owner (username: test_owner)
2. Go to: Dashboard
3. Click: "Insights" (in sidebar)
4. ✅ Should show table with:
   - PG Name
   - View Count
   - Review Count
   - Rating
   - Inquiries
   - Favorites

For Admins:
1. Login as Admin (username: admin)
2. Go to: Admin Dashboard
3. See: Statistics cards at top
4. Click: "Insights & Analytics" button
5. ✅ Same table as owner but for ALL listings
```

**Expected Result**: Table shows listing metrics

---

### Test 5: Admin Dashboard 🎛️
```
1. Login as Admin
2. Go to: Admin Dashboard
3. See: 6 statistics cards showing:
   - Total Users
   - Total Listings
   - Total Inquiries
   - Total Views
   - Total Reviews
   - Pending Inquiries
4. Click: "Quick Actions" buttons
5. ✅ Links should work
```

**Expected Result**: All stats display correctly

---

## 🔧 Troubleshooting

### ❌ Reviews Not Showing

**Problem**: Reviews section missing or not working

**Solution**:
1. Make sure database migration ran (check tables exist)
   ```sql
   SHOW TABLES LIKE 'reviews';
   SHOW TABLES LIKE 'pg_views';
   ```

2. Clear browser cache (Ctrl+Shift+Delete)

3. Make sure you're logged in as TENANT (not owner/admin)

4. Check console for errors (F12 > Console)

---

### ❌ Chatbot Not Appearing

**Problem**: No purple chat bubble visible

**Solution**:
1. Hard refresh page (Ctrl+Shift+R)

2. Check footer.php includes chatbot:
   ```php
   <?php include BASE_URL . 'chatbot.html'; ?>
   ```

3. Verify `chatbot.html` exists in root directory

4. Check console for JavaScript errors (F12 > Console)

---

### ❌ View Count Not Updating

**Problem**: View count always shows 0

**Solution**:
1. Check Network tab in DevTools (F12)
2. Look for "track_view.php" request
3. If not showing:
   - Page might be cached
   - Hard refresh (Ctrl+Shift+R)
   - Check JavaScript errors in Console

4. Verify `track_view.php` exists

---

### ❌ Insights Page Empty

**Problem**: Insights showing no data

**Solution**:
1. Make sure you have listings (for owners)
2. Make sure you're viewing as owner/admin
3. Check database has data:
   ```sql
   SELECT COUNT(*) FROM pg_listings;
   SELECT COUNT(*) FROM pg_views;
   ```

---

## 📊 Database Verification

Check if migration was successful:

```sql
-- In phpMyAdmin or MySQL CLI

-- Check reviews table exists
SHOW TABLES LIKE 'reviews';

-- Check pg_views table exists
SHOW TABLES LIKE 'pg_views';

-- Check view_count column in pg_listings
DESCRIBE pg_listings;
-- Should have 'view_count' column

-- See some test data
SELECT COUNT(*) FROM reviews;
SELECT COUNT(*) FROM pg_views;
```

---

## 🎯 Common Tasks

### How do I see all reviews on a listing?
1. Login as any user
2. Go to PG details page
3. Scroll down to "Reviews & Ratings" section
4. All reviews visible there

### How do I leave a review?
1. Login as TENANT
2. Go to PG details
3. Scroll to "Reviews & Ratings"
4. Select stars and write comment
5. Click "Submit Review"

### How do I access Insights as Owner?
1. Login as owner
2. Click "Dashboard" (or go to dashboards/owner_dashboard.php)
3. In sidebar, click "Insights"
4. See all listing metrics

### How do I access Admin Dashboard?
1. Login as admin
2. Go to dashboards/admin_dashboard.php
3. See all system statistics

### How do I ask the chatbot a question?
1. Look for purple chat bubble (bottom-right)
2. Click it
3. Type your question
4. Press Enter or click send
5. Get instant answer

---

## 📱 Mobile Testing

### Test on Mobile Devices:
1. **Chatbot**: Should be accessible at bottom
2. **Reviews**: Should be readable on small screens
3. **Insights**: Table should scroll horizontally if needed

**URL for mobile testing:**
- Local: `http://localhost/pg_spotter_project/`

---

## 🔐 Security Check

Verify security features are working:

✅ SQL Injection Prevention
- Try: `INSERT INTO reviews...` in review form
- Result: Should be treated as text, not executed

✅ XSS Protection
- Try: `<script>alert('test')</script>` in review
- Result: Should display as text, not execute

✅ Authentication
- Try accessing `/dashboards/insights.php` without login
- Result: Should redirect to login

---

## 📞 Support

### If Something Breaks:

1. **Check the Console** (F12 > Console)
   - Look for red error messages
   - Screenshot and note the error

2. **Check Database**
   - Make sure tables exist
   - Make sure data is there

3. **Check File Permissions**
   - Make sure files are readable

4. **Clear Cache**
   - Hard refresh (Ctrl+Shift+R)
   - Clear all browser data

5. **Check Logs**
   - XAMPP Control Panel > Logs
   - Look for error messages

---

## 🎓 Learning Resources

### For Understanding the Code:

1. **Database Design**
   - Open: `database/add_reviews_table.sql`
   - Understand: How reviews are stored

2. **Backend Logic**
   - Open: `handle_review.php`
   - Understand: How reviews are processed

3. **Frontend Display**
   - Open: `pg_details.php` (search for "Reviews & Ratings")
   - Understand: How reviews are displayed

4. **Chatbot**
   - Open: `chatbot_api.php`
   - Understand: How FAQ matching works

---

## ✅ Success Checklist

Mark off as you test:

- [ ] Database migration completed
- [ ] File structure verified
- [ ] Review system working
- [ ] View tracking functional
- [ ] Chatbot responding
- [ ] Insights dashboard accessible
- [ ] Admin dashboard displaying stats
- [ ] All features tested on mobile
- [ ] No console errors
- [ ] Ready for production

---

## 🎉 You're All Set!

All features are now active and ready to use. 

For detailed documentation, see:
- `IMPLEMENTATION_SUMMARY.md` - Complete feature overview
- `FEATURES_SETUP.md` - Detailed setup guide

Happy coding! 🚀
