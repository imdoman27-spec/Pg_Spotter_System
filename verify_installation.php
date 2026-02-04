<?php
/**
 * PG Spotter - Feature Verification Script
 * This script verifies all new features are properly installed
 */

session_start();

// Styling
$style = "
<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 900px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
h1 { color: #333; border-bottom: 3px solid #667eea; padding-bottom: 10px; }
.section { margin-bottom: 30px; }
.section h2 { color: #667eea; margin-top: 0; }
.check-item { padding: 10px; margin: 5px 0; border-radius: 4px; }
.check-pass { background: #d1e7dd; color: #0f5132; border-left: 4px solid #198754; }
.check-fail { background: #f8d7da; color: #842029; border-left: 4px solid #dc3545; }
.check-warn { background: #fff3cd; color: #664d03; border-left: 4px solid #ffc107; }
.footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
th { background: #f8f9fa; font-weight: bold; }
code { background: #f5f5f5; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
</style>
";

echo $style;
echo "<div class='container'>";
echo "<h1>✅ PG Spotter - Feature Verification Report</h1>";

$base_path = dirname(__FILE__);
$passes = 0;
$failures = 0;

// Section 1: File Checks
echo "<div class='section'>";
echo "<h2>1. File Structure Verification</h2>";

$files_to_check = [
    '/chatbot.html' => 'Chatbot UI',
    '/chatbot_api.php' => 'Chatbot API',
    '/handle_review.php' => 'Review Handler',
    '/track_view.php' => 'View Tracking',
    '/database/add_reviews_table.sql' => 'Database Migration',
    '/dashboards/insights.php' => 'Insights Dashboard',
    '/dashboards/manage_reviews.php' => 'Review Management',
    '/IMPLEMENTATION_SUMMARY.md' => 'Implementation Guide',
    '/FEATURES_SETUP.md' => 'Setup Guide',
    '/QUICK_START.md' => 'Quick Start Guide',
];

foreach ($files_to_check as $file => $label) {
    $full_path = $base_path . $file;
    if (file_exists($full_path)) {
        echo "<div class='check-item check-pass'>✓ $label <code>$file</code></div>";
        $passes++;
    } else {
        echo "<div class='check-item check-fail'>✗ $label <code>$file</code> - FILE MISSING</div>";
        $failures++;
    }
}

echo "</div>";

// Section 2: Database Checks
echo "<div class='section'>";
echo "<h2>2. Database Verification</h2>";

try {
    include 'includes/config.php';
    
    // Check reviews table
    $query = "SHOW TABLES LIKE 'reviews'";
    $result = $conn->query($query);
    if ($result && $result->rowCount() > 0) {
        echo "<div class='check-item check-pass'>✓ Reviews table exists</div>";
        $passes++;
    } else {
        echo "<div class='check-item check-fail'>✗ Reviews table missing - RUN MIGRATION</div>";
        $failures++;
    }
    
    // Check pg_views table
    $query = "SHOW TABLES LIKE 'pg_views'";
    $result = $conn->query($query);
    if ($result && $result->rowCount() > 0) {
        echo "<div class='check-item check-pass'>✓ PG Views table exists</div>";
        $passes++;
    } else {
        echo "<div class='check-item check-fail'>✗ PG Views table missing - RUN MIGRATION</div>";
        $failures++;
    }
    
    // Check view_count column
    $query = "DESCRIBE pg_listings";
    $result = $conn->query($query);
    $columns = $result->fetchAll(PDO::FETCH_COLUMN);
    if (in_array('view_count', $columns)) {
        echo "<div class='check-item check-pass'>✓ view_count column exists in pg_listings</div>";
        $passes++;
    } else {
        echo "<div class='check-item check-fail'>✗ view_count column missing - RUN MIGRATION</div>";
        $failures++;
    }
    
    // Check some data
    $query = "SELECT COUNT(*) FROM reviews";
    $count = $conn->query($query)->fetchColumn();
    echo "<div class='check-item check-warn'>ℹ Reviews in database: $count</div>";
    
    $query = "SELECT COUNT(*) FROM pg_views";
    $count = $conn->query($query)->fetchColumn();
    echo "<div class='check-item check-warn'>ℹ Views tracked: $count</div>";
    
} catch (Exception $e) {
    echo "<div class='check-item check-fail'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</div>";
    $failures++;
}

echo "</div>";

// Section 3: Code Checks
echo "<div class='section'>";
echo "<h2>3. Integration Checks</h2>";

// Check if footer includes chatbot
$footer_path = $base_path . '/includes/footer.php';
if (file_exists($footer_path)) {
    $footer_content = file_get_contents($footer_path);
    if (strpos($footer_content, 'chatbot') !== false) {
        echo "<div class='check-item check-pass'>✓ Chatbot included in footer</div>";
        $passes++;
    } else {
        echo "<div class='check-item check-fail'>✗ Chatbot NOT included in footer</div>";
        $failures++;
    }
}

// Check if pg_details includes review form
$pd_path = $base_path . '/pg_details.php';
if (file_exists($pd_path)) {
    $pd_content = file_get_contents($pd_path);
    if (strpos($pd_content, 'reviewForm') !== false && strpos($pd_content, 'Reviews') !== false) {
        echo "<div class='check-item check-pass'>✓ Review system integrated in pg_details</div>";
        $passes++;
    } else {
        echo "<div class='check-item check-fail'>✗ Review system NOT integrated in pg_details</div>";
        $failures++;
    }
    
    if (strpos($pd_content, 'track_view') !== false) {
        echo "<div class='check-item check-pass'>✓ View tracking integrated in pg_details</div>";
        $passes++;
    } else {
        echo "<div class='check-item check-fail'>✗ View tracking NOT integrated in pg_details</div>";
        $failures++;
    }
}

// Check owner dashboard link
$od_path = $base_path . '/dashboards/owner_dashboard.php';
if (file_exists($od_path)) {
    $od_content = file_get_contents($od_path);
    if (strpos($od_content, 'insights.php') !== false) {
        echo "<div class='check-item check-pass'>✓ Insights link in owner dashboard</div>";
        $passes++;
    } else {
        echo "<div class='check-item check-fail'>✗ Insights link missing from owner dashboard</div>";
        $failures++;
    }
}

echo "</div>";

// Section 4: API Endpoints
echo "<div class='section'>";
echo "<h2>4. API Endpoints</h2>";

echo "<p>Available endpoints:</p>";
echo "<ul>";
echo "<li><code>GET /track_view.php?pg_id=ID</code> - Track listing views</li>";
echo "<li><code>POST /handle_review.php</code> - Submit/update reviews</li>";
echo "<li><code>GET /chatbot_api.php?q=QUERY</code> - Chatbot FAQ queries</li>";
echo "</ul>";

echo "</div>";

// Section 5: Summary
echo "<div class='section'>";
echo "<h2>Summary Report</h2>";

$total = $passes + $failures;
$percentage = $total > 0 ? round(($passes / $total) * 100) : 0;

echo "<table>";
echo "<tr>";
echo "<th>Status</th>";
echo "<th>Count</th>";
echo "<th>Percentage</th>";
echo "</tr>";
echo "<tr>";
echo "<td style='background: #d1e7dd;'>✓ Passed Checks</td>";
echo "<td style='background: #d1e7dd;'><strong>$passes</strong></td>";
echo "<td style='background: #d1e7dd;'><strong>$percentage%</strong></td>";
echo "</tr>";
if ($failures > 0) {
    echo "<tr>";
    echo "<td style='background: #f8d7da;'>✗ Failed Checks</td>";
    echo "<td style='background: #f8d7da;'><strong>$failures</strong></td>";
    echo "<td style='background: #f8d7da;'><strong>" . (100 - $percentage) . "%</strong></td>";
    echo "</tr>";
}
echo "</table>";

echo "<br>";

if ($failures == 0) {
    echo "<div class='check-item check-pass' style='font-size: 18px; text-align: center;'>";
    echo "🎉 All checks passed! Your PG Spotter is ready to go!";
    echo "</div>";
} else {
    echo "<div class='check-item check-fail' style='font-size: 18px; text-align: center;'>";
    echo "⚠️ $failures issue(s) found. Please check the items above.";
    echo "</div>";
}

echo "</div>";

// Next Steps
echo "<div class='section'>";
echo "<h2>Next Steps</h2>";

if ($failures > 0) {
    echo "<ol>";
    echo "<li>Fix all failed checks above</li>";
    echo "<li>Run database migration if needed:";
    echo "<pre>mysql -u root -p pgspotter_db < database/add_reviews_table.sql</pre>";
    echo "</li>";
    echo "<li>Refresh this page to verify</li>";
    echo "</ol>";
} else {
    echo "<p>✓ All systems operational!</p>";
    echo "<ol>";
    echo "<li>Read <strong>QUICK_START.md</strong> to test features</li>";
    echo "<li>Try leaving a review on a PG details page</li>";
    echo "<li>Chat with the chatbot (bottom-right corner)</li>";
    echo "<li>View insights from your dashboard</li>";
    echo "</ol>";
}

echo "</div>";

echo "<div class='footer'>";
echo "Generated: " . date('Y-m-d H:i:s') . " | ";
echo "PHP Version: " . phpversion() . " | ";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'];
echo "</div>";

echo "</div>";
?>
