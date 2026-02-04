<?php
// Chatbot backend - handles user queries and FAQs
header('Content-Type: application/json');

// Handle both GET and POST requests
$user_query = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['q'])) {
    $user_query = strtolower(trim($_GET['q']));
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['q'])) {
    $user_query = strtolower(trim($_POST['q']));
}

// FAQ Database - More specific keywords first to avoid false matches
$faqs = [
    // Inquiries - Must be before Contact & Support to match "contact owner" first
    [
        'keywords' => ['inquiry', 'send inquiry', 'message owner', 'ask owner', 'contact owner', 'send message to owner', 'how to contact owner', 'how to message owner'],
        'answer' => "📧 Send Inquiry to Owner:
1. Open PG detail page
2. Scroll to \"Send Inquiry\" section
3. Fill inquiry form:
   • Your name
   • Email
   • Phone
   • Message
4. Click \"Send Inquiry\"
5. Owner receives notification
6. Check dashboard for replies

You can track all inquiries in your dashboard!"
    ],
    
    // Booking Process
    [
        'keywords' => ['how to apply', 'apply', 'how to book', 'booking', 'book a pg', 'reservation', 'how do i book'],
        'answer' => "📝 Booking Process:
1. Browse and select a PG you like
2. Click \"View Details\" to see full info
3. Click \"Contact Owner / Send Inquiry\"
4. Fill inquiry form with your details
5. Owner will respond with availability
6. Schedule a visit if interested
7. Complete booking with owner directly

Tip: Send inquiries to multiple PGs to compare options!

<div class='chatbot-action-btn-container'><a href='search.php' class='chatbot-action-btn'>📝 Browse PGs</a></div>"
    ],
    
    // Search & Find PGs
    [
        'keywords' => ['how to search', 'search pg', 'find pg', 'how do i search', 'looking for', 'how to find'],
        'answer' => "🔍 To search for PGs:
1. Click \"Search PGs\" in the menu or header
2. Enter your city/location
3. Set your budget range using the slider
4. Select PG type (Male/Female/Co-ed)
5. Choose amenities you need
6. Click \"Apply Filters\"

You can view detailed information, photos, and location maps for each PG!

<div class='chatbot-action-btn-container'><a href='search.php' class='chatbot-action-btn'>🔍 Search PGs Now</a></div>"
    ],
    
    // Pricing & Rent
    [
        'keywords' => ['rent', 'price', 'cost', 'how much', 'expensive', 'cheap', 'budget', 'pricing', 'tell me about pricing'],
        'answer' => "💰 Rent Information:
• Typical range: ₹3,000 to ₹15,000 per month
• Varies by: Location, amenities, PG type
• Use budget filter on search page
• All prices shown per person/month
• Most PGs include basic utilities

Tip: Compare multiple PGs to find the best deal!

<div class='chatbot-action-btn-container'><a href='search.php' class='chatbot-action-btn'>🔍 Browse PGs</a></div>"
    ],
    
    // Payment
    [
        'keywords' => ['payment', 'pay', 'how to pay', 'payment method', 'online payment', 'transaction'],
        'answer' => "💳 Payment Information:

PG Spotter is a listing platform. Payments are made directly to PG owners after:

1. Viewing the property
2. Agreeing to terms
3. Signing rental agreement

Payment Methods (Owner dependent):
• Cash
• Bank Transfer
• UPI/Digital wallets
• Cheque

Always get receipt and signed agreement!"
    ],
    
    // Security Deposit
    [
        'keywords' => ['security deposit', 'deposit', 'advance', 'refund', 'tell me about security'],
        'answer' => "🔒 Security Deposit:
• Usually 1-3 months rent
• Fully refundable upon checkout
• Amount shown on each listing
• Protects against damages
• Returned within 15-30 days

Check individual PG policies for specific terms."
    ],
    
    // Amenities
    [
        'keywords' => ['amenities', 'facilities', 'what is included', 'wifi', 'ac', 'food', 'laundry', 'parking', 'what amenities'],
        'answer' => "🏠 Common Amenities:
✓ WiFi & Internet
✓ Attached Bathroom
✓ Food (2-3 meals)
✓ Laundry Service
✓ Air Conditioning
✓ Parking Space
✓ CCTV Security
✓ Power Backup
✓ Hot Water (Geyser)
✓ Cleaning Service

Each PG listing shows available amenities. Use filters to find PGs with specific facilities!"
    ],
    
    // Favorites/Saved
    [
        'keywords' => ['favorite', 'save', 'saved listings', 'watchlist', 'bookmark', 'how to save', 'save favorites'],
        'answer' => "⭐ Save Favorites:
1. Login to your account
2. Go to any PG detail page
3. Click \"Save to Favorites\" button
4. Access saved PGs from your dashboard
5. View all favorites under \"Saved PGs\"

Save multiple PGs to compare later and make the best decision!

<div class='chatbot-action-btn-container'><a href='dashboards/saved_pgs.php' class='chatbot-action-btn'>⭐ View Saved PGs</a></div>"
    ],
    
    // Login & Registration
    [
        'keywords' => ['login', 'sign up', 'register', 'account', 'create account', 'signup', 'how to create', 'new account'],
        'answer' => "👤 Create Account:
1. Click \"Sign Up\" in header
2. Choose role: Tenant or Owner
3. Fill registration form
4. Verify your email
5. Login with credentials

Benefits:
✓ Save favorite PGs
✓ Send inquiries
✓ Track messages
✓ Leave reviews
✓ Access dashboard

<div class='chatbot-action-btn-container'><a href='signup.php' class='chatbot-action-btn'>📝 Sign Up</a><a href='login.php' class='chatbot-action-btn'>🔑 Login</a></div>"
    ],
    
    // Password Reset
    [
        'keywords' => ['change password', 'forgot password', 'reset password', 'cant login', 'password recovery'],
        'answer' => "🔑 Password Reset:
1. Go to Login page
2. Click \"Forgot Password?\"
3. Enter your email address
4. Check email for reset link
5. Follow instructions to reset
6. Login with new password

If you don't receive email, check spam folder or contact support.

<div class='chatbot-action-btn-container'><a href='forgot_password.php' class='chatbot-action-btn'>🔑 Reset Password</a></div>"
    ],
    
    // User Types
    [
        'keywords' => ['owner', 'tenant', 'difference', 'what is', 'admin', 'role', 'user type'],
        'answer' => "🏷️ User Types:

👨‍💼 TENANT:
• Looking for accommodation
• Can search & save PGs
• Send inquiries to owners
• Leave reviews

🏠 OWNER:
• List PG properties
• Manage listings
• Receive inquiries
• Reply to tenants

Choose your role during registration!"
    ],
    
    // Reviews & Ratings
    [
        'keywords' => ['review', 'rating', 'feedback', 'tell me about reviews', 'how to review', 'leave review'],
        'answer' => "⭐ Reviews & Ratings:

Leave a Review:
1. Visit any PG detail page
2. Scroll to \"Reviews\" section
3. Rate from 1-5 stars
4. Write your experience
5. Submit review

Benefits:
✓ Help other tenants
✓ Share your experience
✓ Improve PG quality
✓ Build community trust"
    ],
    
    // Location & Maps
    [
        'keywords' => ['map', 'location', 'where', 'address', 'area', 'tell me about location', 'view location'],
        'answer' => "📍 Location & Maps:

Every PG listing shows:
✓ Complete address
✓ City & Area
✓ Interactive map preview
✓ Nearby landmarks

View Location:
1. Open PG detail page
2. Scroll to \"Location\" section
3. See map with exact location
4. Click map for full view

Use location filters on search page to find PGs in your preferred area!"
    ],
    
    // Photos & Gallery
    [
        'keywords' => ['photos', 'pictures', 'gallery', 'images', 'view photos', 'see pictures', 'photo gallery'],
        'answer' => "📷 View Photos:

1. Browse PG listings (1 preview photo)
2. Click \"View Details\"
3. See multiple photos of:
   • Rooms
   • Common areas
   • Amenities
   • Building exterior
4. Click \"View Fullscreen\" for gallery

Photos help you:
✓ See actual conditions
✓ Check room size
✓ View amenities
✓ Make informed decision"
    ],
    
    // List Your PG
    [
        'keywords' => ['list my pg', 'add property', 'how to list', 'list property', 'add pg', 'become owner', 'how to list my pg'],
        'answer' => "🏠 List Your PG - Complete Guide:

Step 1: Create Owner Account
• Go to Signup page
• Select \"PG Owner\" as user type
• Fill email, password, phone
• Verify email

Step 2: Navigate to Listing
• Login to dashboard
• Click \"List a New PG\" or \"Add Property\"
• Or go to: list_pg.php

Step 3: Fill Property Details
Basic Info:
  • PG Name (e.g., \"Cozy 3BHK PG\")
  • Type (Hostel/Shared/Private)
  • Rent amount (per month)
  • Deposit amount

Location:
  • City
  • Area/Locality
  • Exact Address
  • Nearby landmarks

Step 4: Add Amenities
Select from:
✓ WiFi
✓ AC/Fan
✓ Furniture
✓ Kitchen
✓ Parking
✓ Laundry
✓ TV/Entertainment
✓ Security
+ Many more...

Step 5: Upload Photos
• Upload 3-5 clear photos
• Include room, bathroom, common areas
• Better photos = More inquiries

Step 6: Submit
• Review all details
• Click \"Submit for Approval\"
• Admin reviews within 24 hours
• Once approved, it goes LIVE!

💡 Pro Tips:
• Write attractive descriptions
• Use high-quality photos
• Update regularly to stay visible
• Respond quickly to inquiries
• Maintain competitive pricing

<div class='chatbot-action-btn-container'><a href='list_pg.php' class='chatbot-action-btn'>🏠 List Your PG Now</a></div>"
    ],

    // Owner: Manage Listings
    [
        'keywords' => ['manage listings', 'my listings', 'owner listings', 'manage my pg', 'listing management', 'how to manage listings'],
        'answer' => "📋 Manage Your PG Listings:

Access Your Listings:
1. Login to Owner Dashboard
2. Click \"My Listings\"
3. View all your PGs in one place

What You Can Do:
✏️ EDIT:
• Change rent/deposit
• Update amenities
• Upload new photos
• Update description
• Modify location info

🗑️ DELETE:
• Remove listing permanently
• Deleted listings can't be recovered

⏸️ SUSPEND:
• Hide listing temporarily
• You can unsuspend anytime
• Keeps all your data

📊 VIEW STATS:
• Number of views
• Inquiries received
• Listing status
• Created date

🔍 SEARCH & FILTER:
• Find specific PGs
• Sort by date/name/status
• View active/inactive listings

📈 Analytics:
• See which listings get most views
• Track inquiry trends
• Monitor listing performance

💡 Management Tips:
• Keep listings updated regularly
• Respond to inquiries quickly
• Monitor view counts
• Update photos seasonally
• Check for new inquiries daily

<div class='chatbot-action-btn-container'><a href='dashboards/owner_dashboard.php' class='chatbot-action-btn'>📊 Owner Dashboard</a><a href='dashboards/my_listings.php' class='chatbot-action-btn'>📋 My Listings</a></div>"
    ],

    // Owner: Edit Listing
    [
        'keywords' => ['edit listing', 'edit my listing', 'update listing', 'change listing', 'edit my pg', 'how to edit my listing'],
        'answer' => "✏️ Edit Your PG Listing:

Step-by-Step Guide:
1. Go to Owner Dashboard → \"My Listings\"
2. Find the PG you want to edit
3. Click the \"Edit\" button
4. Modify details:
   • Property name & type
   • Location/address
   • Rent amount
   • Deposit amount
   • Amenities (add/remove)
   • Description/notes
5. Update photos if needed
6. Click \"Save Changes\"
7. Changes are updated instantly on your listing

✨ Pro Tips:
• Update photos regularly to attract more tenants
• Keep rent and amenities accurate
• Add detailed descriptions to get more inquiries
• Check admin approval for major changes

<div class='chatbot-action-btn-container'><a href='dashboards/edit_listing.php' class='chatbot-action-btn'>✏️ Edit Listing</a></div>"
    ],

    // Owner: Delete Listing
    [
        'keywords' => ['delete listing', 'delete my listing', 'remove listing', 'delete my pg', 'remove pg', 'how to delete my listing'],
        'answer' => "🗑️ Delete Your PG Listing:

Step-by-Step:
1. Go to Owner Dashboard → \"My Listings\"
2. Find the PG you want to remove
3. Click the \"Delete\" button (trash icon)
4. Confirm deletion in the popup
5. Listing is immediately removed from the platform

⚠️ Important:
• Deleted listings cannot be recovered
• Make sure no active inquiries before deleting
• Consider suspending instead of deleting if temporary
• New tenants won't see deleted listings

If you want to SUSPEND instead (keep it hidden):
• Use the Suspend option instead of Delete
• You can unsuspend anytime

<div class='chatbot-action-btn-container'><a href='dashboards/my_listings.php' class='chatbot-action-btn'>📋 My Listings</a></div>"
    ],

    // Owner: Manage Inquiries
    [
        'keywords' => ['manage inquiries', 'check inquiries', 'view inquiries', 'inquiries received', 'tenant inquiries', 'how to manage inquiries'],
        'answer' => "📨 Manage Tenant Inquiries:

How to View Inquiries:
1. Open Owner Dashboard
2. Click \"My Inquiries\" or \"Manage Inquiries\"
3. View all messages from interested tenants
4. See tenant name, email, phone, message

How to Reply:
1. Click on an inquiry
2. Read the tenant's message
3. Type your reply
4. Click \"Send Reply\"
5. Tenant receives email notification

Inquiry Details Include:
• Tenant's name & contact info
• Message content
• Date received
• Reply status

💡 Best Practices:
• Reply within 24 hours for better response rate
• Be professional and courteous
• Share property details and availability
• Schedule property visits if interested
• Ask qualifying questions about the tenant

<div class='chatbot-action-btn-container'><a href='dashboards/my_inquiries.php' class='chatbot-action-btn'>📨 Manage Inquiries</a></div>"
    ],

    // Owner: Add Photos
    [
        'keywords' => ['add photos', 'upload photos', 'add pictures', 'upload pictures', 'update photos', 'add property photos'],
        'answer' => "📸 Add/Update Property Photos:

Why Photos Matter:
• Better photos = More inquiries
• Tenants browse visually first
• Professional images attract quality tenants
• Higher visibility in search results

How to Upload:
1. Go to My Listings
2. Click Edit on your PG
3. Scroll to \"Photos\" section
4. Click \"Add Photo\" or \"Upload\"
5. Select from your computer
6. Add title/description (optional)
7. Save changes

Photo Tips:
✓ Use good lighting
✓ Show multiple angles
✓ Include room, bathroom, common areas
✓ Show kitchen/living space
✓ 3-5 quality photos recommended
✓ Update seasonally for freshness
✓ Avoid blurry or dark images
✓ Feature best amenities

Photo Ideas:
• Room with furniture
• Bathroom facilities
• Common area/lounge
• Kitchen area
• Entrance/building exterior
• Parking (if available)

<div class='chatbot-action-btn-container'><a href='dashboards/edit_listing.php' class='chatbot-action-btn'>📸 Upload Photos</a></div>"
    ],

    // Owner: Set Pricing
    [
        'keywords' => ['pricing', 'set rent', 'set price', 'change rent', 'update price', 'how to set pricing'],
        'answer' => "💰 Set Your PG Pricing:

Pricing Components:
1. MONTHLY RENT:
   • Base rent amount
   • Per-person pricing
   • Market competitive rate

2. SECURITY DEPOSIT:
   • Usually 1-2 months rent
   • Refundable amount
   • Clearly specify conditions

3. MAINTENANCE/UTILITY:
   • Optional additional charges
   • Water/electricity
   • Internet (if included)

How to Set Pricing:
1. Go to My Listings
2. Click Edit on PG
3. Enter monthly rent
4. Enter deposit amount
5. Add any extra charges
6. Save changes

Pricing Tips:
💡 Research competitor rates
💡 Consider location premium
💡 Factor in amenities
💡 Update during seasons
💡 Be transparent about all charges
💡 Keep prices competitive

Best Practices:
• Display price clearly
• Mention what's included
• Be flexible for long-term tenants
• Respond quickly to inquiries
• Update pricing if market changes

<div class='chatbot-action-btn-container'><a href='dashboards/edit_listing.php' class='chatbot-action-btn'>💰 Edit Pricing</a></div>"
    ],

    // Owner: View Insights
    [
        'keywords' => ['insights', 'analytics', 'view insights', 'listing views', 'performance', 'statistics'],
        'answer' => "📊 View Listing Insights & Analytics:

What Insights Show:
📈 VIEWS:
• Total views on your listing
• Views this week/month
• Most viewed times
• Trending days

📨 INQUIRIES:
• Total inquiries received
• Inquiries this month
• Response rate
• Inquiry trends

⭐ REVIEWS:
• Average rating
• Recent reviews
• Review count
• Feedback summary

👥 VISITOR INFO:
• Visitor locations
• Device types (mobile/desktop)
• Traffic sources

How to Access:
1. Owner Dashboard
2. Click \"Insights\" or \"Analytics\"
3. Select time period
4. View your performance

How to Use:
✓ Track what attracts tenants
✓ Improve underperforming listings
✓ Identify best times to respond
✓ Optimize your property details
✓ Monitor competition

Actionable Tips:
• High views but low inquiries? Improve photos/description
• Low views? Update listing and promote better
• Slow responses? Respond faster to get higher ratings
• Analyze peak inquiry times and be available then

<div class='chatbot-action-btn-container'><a href='dashboards/insights.php' class='chatbot-action-btn'>📊 View Insights</a></div>"
    ],
    
    // Dashboard
    [
        'keywords' => ['dashboard', 'my account', 'profile', 'my dashboard', 'my profile'],
        'answer' => "📊 Your Dashboard:

Tenant Dashboard:
✓ Saved PGs
✓ Sent Inquiries
✓ Profile Settings

Owner Dashboard:
✓ My Listings
✓ Received Inquiries
✓ Manage Properties
✓ View Analytics

Access your dashboard by clicking your name in the header after login!

<div class='chatbot-action-btn-container'><a href='dashboards/tenant_dashboard.php' class='chatbot-action-btn'>👥 Tenant Dashboard</a><a href='dashboards/owner_dashboard.php' class='chatbot-action-btn'>🏠 Owner Dashboard</a></div>"
    ],
    
    // About Us
    [
        'keywords' => ['about', 'about us', 'who are you', 'company', 'about pg spotter', 'what is pg spotter'],
        'answer' => "🏢 About PG Spotter:

We help you find and list quality PG accommodations across India. Our platform connects tenants with verified PG owners.

🎯 Our Mission:
Make PG hunting easy, safe, and transparent.

✨ Features:
✓ Verified listings
✓ Easy search & filters
✓ Direct owner contact
✓ Reviews & ratings
✓ Secure platform

Click \"About Us\" in menu for more details!

<div class='chatbot-action-btn-container'><a href='about.php' class='chatbot-action-btn'>📜 About Us</a></div>"
    ],
    
    // Safety & Security
    [
        'keywords' => ['safe', 'safety', 'security', 'secure', 'verified', 'trust', 'is it safe'],
        'answer' => "🔐 Safety & Security:

✓ Verified PG listings
✓ Owner background checks
✓ Secure payment information
✓ Review system for transparency
✓ Direct owner communication
✓ Report suspicious listings

Tips:
• Read reviews carefully
• Visit PG in person
• Verify documents
• Check security features
• Trust your instincts

Your safety is our priority!"
    ],
    
    // Mobile App
    [
        'keywords' => ['app', 'mobile app', 'android', 'ios', 'download app', 'mobile'],
        'answer' => "📱 Mobile Access:

Our website is fully mobile-responsive! No separate app needed.

Access from:
✓ Any smartphone browser
✓ Tablet
✓ Desktop/Laptop

Features on mobile:
✓ Easy search
✓ Quick filters
✓ Swipe through photos
✓ One-tap call/message
✓ Save favorites

Bookmark our website for quick access!"
    ],
    
    // Contact & Support (kept general, after more specific contact-owner queries)
    [
        'keywords' => ['contact', 'help', 'support', 'customer service', 'email', 'phone', 'contact support', 'reach you'],
        'answer' => "💬 Contact Support:

📧 Email: info@pgspotter.com
📞 Phone: +91 1234-567890
📍 Location: Raipur, Chhattisgarh

📝 Contact Form:
1. Click \"Contact\" in menu
2. Fill the form with your query
3. We respond within 24 hours

Or use this chatbot for instant help!

<div class='chatbot-action-btn-container'><a href='contact.php' class='chatbot-action-btn'>📧 Contact Us</a></div>"
    ],
    
    // Greetings
    [
        'keywords' => ['hello', 'hi', 'hey', 'good morning', 'good evening', 'good afternoon', 'namaste'],
        'answer' => "👋 Hello! Welcome to PG Spotter!

I'm here to help you with:
🔍 Finding the perfect PG
💰 Pricing information
📝 Booking process
⭐ And much more!

How can I assist you today? Click a button below or type your question!"
    ],
    
    // Thanks
    [
        'keywords' => ['thank', 'thanks', 'thank you', 'thankyou', 'appreciate', 'helpful'],
        'answer' => "😊 You're welcome! I'm glad I could help!

If you have any more questions about:
• Finding PGs
• Booking process
• Account management
• Or anything else

Feel free to ask! Happy PG hunting! 🏠"
    ]
];

// Handler for incoming chatbot requests
if (!empty($user_query)) {
    // Check each FAQ entry
    foreach ($faqs as $entry) {
        foreach ($entry['keywords'] as $keyword) {
            if (strpos($user_query, $keyword) !== false) {
                echo json_encode([
                    'success' => true,
                    'message' => $entry['answer']
                ]);
                exit;
            }
        }
    }
}

// Default response if no match found
echo json_encode([
    'success' => true,
    'message' => "I'm not sure about that specific question, but I can help you with:

🔍 Finding & Searching PGs
💰 Pricing & Payments
📝 Booking Process
🏠 Amenities & Facilities
👤 Account & Login
⭐ Reviews & Ratings
📧 Contact Owner/Support
📍 Location & Maps

Try asking about these topics, or click the menu buttons above!

Need urgent help? Contact us:
📧 info@pgspotter.com
📞 +91 1234-567890"
]);
?>
