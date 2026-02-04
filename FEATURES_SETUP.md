# PG Spotter - Setup Instructions for New Features

This document explains the new features added to the PG Spotter platform and how to set them up.

## New Features Added

### 1. Review System
- **Database Table**: `reviews` table created to store user reviews and ratings
- **Features**:
  - Users can leave 1-5 star ratings
  - Users can write review comments
  - Reviews are displayed on PG detail pages
  - Average rating is calculated and shown on listings
  - Each user can only leave one review per PG (can be updated)

### 2. View Tracking System
- **Database Table**: `pg_views` table tracks every view of a listing
- **Features**:
  - Automatically tracks when a listing is viewed
  - Records user ID and IP address
  - View count is incremented in `pg_listings` table
  - Available for analytics and insights

### 3. Admin Insights Dashboard
- **Access**: Dashboard > Insights (for owners and admins)
- **Features**:
  - View count statistics per listing
  - Review count and average ratings
  - Inquiry and favorite counts
  - Detailed analytics table
  - Owner-specific and admin-wide views

### 4. Chatbot
- **Location**: Fixed widget in bottom-right corner (appears on all pages)
- **Features**:
  - AI-powered Q&A bot with FAQ database
  - Answers common questions about:
    - How to search for PGs
    - Pricing and security deposits
    - Amenities and facilities
    - How to apply/book
    - Login and account management
    - Reviews and ratings
  - Expandable/minimizable widget
  - Clean, modern UI with gradient design

### 5. Enhanced Admin Dashboard
- **Features**:
  - Real-time statistics (users, listings, inquiries, views, reviews)
  - Pending inquiries counter
  - Recent user signups
  - Quick action buttons
  - Links to insights and review management

## Database Setup

### Run the SQL Migration

Execute the following SQL file to create necessary tables:

```bash
# In phpMyAdmin or MySQL CLI
mysql -u root -p pgspotter_db < database/add_reviews_table.sql
```

Or manually execute this SQL in phpMyAdmin:

```sql
-- Create reviews table
CREATE TABLE IF NOT EXISTS `reviews` (
  `review_id` int(11) NOT NULL AUTO_INCREMENT,
  `pg_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`review_id`),
  FOREIGN KEY (`pg_id`) REFERENCES `pg_listings`(`pg_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_review` (`pg_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create pg_views table for tracking views
CREATE TABLE IF NOT EXISTS `pg_views` (
  `view_id` int(11) NOT NULL AUTO_INCREMENT,
  `pg_id` int(11) NOT NULL,
  `user_id` int(11),
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45),
  PRIMARY KEY (`view_id`),
  FOREIGN KEY (`pg_id`) REFERENCES `pg_listings`(`pg_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add view_count column to pg_listings if not exists
ALTER TABLE `pg_listings` ADD COLUMN `view_count` int(11) DEFAULT 0;
```

## File Structure

### New Files Created

```
/pg_spotter_project/
├── chatbot.html                    # Chatbot UI and JS
├── chatbot_api.php                 # Chatbot FAQ backend
├── handle_review.php               # Review submission handler
├── track_view.php                  # View tracking handler
├── database/
│   └── add_reviews_table.sql       # Database migration
└── dashboards/
    ├── insights.php                # Owner/Admin insights dashboard
    ├── admin_dashboard.php         # Enhanced admin dashboard
    └── manage_reviews.php          # Admin review management
```

### Modified Files

- **pg_details.php**: Added reviews section, display, and form
- **includes/footer.php**: Added chatbot widget include
- **dashboards/owner_dashboard.php**: Added insights link

## How to Use Each Feature

### For Users - Leaving a Review

1. Navigate to a PG detail page (as a logged-in tenant)
2. Scroll down to "Reviews & Ratings" section
3. Click stars to select rating (1-5)
4. Write your review in the text area
5. Click "Submit Review"
6. Your review appears immediately

### For Owners/Admins - Viewing Insights

1. Go to Dashboard > Insights
2. See statistics for each listing:
   - View count
   - Review count and average rating
   - Inquiry count
   - Favorite count
3. Click "View Listing" to see full details

### For Admins - Complete Admin Panel

1. Go to Admin Dashboard
2. See system-wide statistics
3. Use Quick Actions to:
   - View detailed insights
   - Review contact messages
   - Manage user reviews
4. Access admin functions from sidebar

### For All Users - Chatbot

1. Look for the purple chat bubble in bottom-right corner
2. Click to expand chatbot
3. Ask questions or type keywords like:
   - "How to search"
   - "Rent price"
   - "Security deposit"
   - "Reviews"
   - etc.
4. Chatbot provides instant answers from FAQ database

## API Endpoints

### Track View
```
GET /track_view.php?pg_id=ID
Response: JSON with success status and updated view_count
```

### Submit Review
```
POST /handle_review.php
Data: {pg_id, rating, comment}
Response: JSON with success message or error
```

### Chatbot Query
```
GET /chatbot_api.php?q=QUERY
Response: JSON with answer from FAQ
```

## FAQ Database

The chatbot has built-in FAQs covering:
- How to search and find PGs
- Pricing information
- Security deposits
- Amenities and facilities
- How to apply/book
- Account management
- Reviews and ratings
- Contact information

To add more FAQs, edit `chatbot_api.php` and add entries to the `$faqs` array.

## Troubleshooting

### Reviews Not Showing
- Check database tables were created successfully
- Ensure user is logged in as tenant
- Check browser console for JavaScript errors

### Chatbot Not Working
- Verify `chatbot.html` is being included in footer
- Check `chatbot_api.php` is accessible
- Clear browser cache and reload

### View Count Not Increasing
- Ensure `track_view.php` is being called (check network tab)
- Verify `pg_views` table exists
- Check `pg_listings` table has `view_count` column

### Admin Dashboard Stats Wrong
- Run database queries directly to verify data
- Check SQL queries in `admin_dashboard.php`
- Ensure all tables were created properly

## Security Notes

- Review submissions require user login
- XSS protection via `htmlspecialchars()`
- SQL injection prevention via prepared statements
- View tracking uses both user_id and IP for fallback tracking
- Admin functions verify user type before access

## Future Enhancements

Potential improvements:
- Filtered reviews (most helpful, recent, etc.)
- Review flagging for inappropriate content
- More advanced analytics (trends, graphs)
- Chatbot learning from user feedback
- Review moderation queue for admins
- Notification system for new reviews
