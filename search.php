<?php
session_start();
include 'includes/config.php';
include 'includes/header.php';

// --- Build Filtered Query ---
$listings = [];
$sql_base = "SELECT p.pg_id, p.pg_name, p.location_city, p.rent_per_person, MIN(ph.photo_path) AS photo_path
             FROM pg_listings p
             LEFT JOIN pg_photos ph ON p.pg_id = ph.pg_id"; // Select only needed columns for list view
$where_clauses = [];
$parameters = [];
$location_display = '';
$use_positional_params = false;

// Location Filter
if (!empty($_GET['location'])) {
    $where_clauses[] = "p.location_city LIKE ?";
    $parameters[] = '%' . $_GET['location'] . '%';
    $location_display = 'in ' . htmlspecialchars($_GET['location']);
    $use_positional_params = true;
}
// PG Type Filter
if (!empty($_GET['pg_type']) && in_array($_GET['pg_type'], ['male', 'female', 'coed'])) {
    $where_clauses[] = "p.pg_type = ?";
    $parameters[] = $_GET['pg_type'];
    $use_positional_params = true;
}
// Budget Filter
if (!empty($_GET['max_budget']) && is_numeric($_GET['max_budget'])) {
    $where_clauses[] = "p.rent_per_person <= ?";
    $parameters[] = (int)$_GET['max_budget'];
    $use_positional_params = true;
}
// Amenities Filter
if (!empty($_GET['amenities']) && is_array($_GET['amenities'])) {
    $selected_amenities = $_GET['amenities'];
    $selected_amenities_count = count($selected_amenities);
    if ($selected_amenities_count > 0) {
        $use_positional_params = true;
        $amenity_placeholders = implode(',', array_fill(0, $selected_amenities_count, '?'));
        $where_clauses[] = "p.pg_id IN (SELECT pa.pg_id FROM pg_amenities pa JOIN amenities a ON pa.amenity_id = a.amenity_id WHERE a.amenity_name IN ($amenity_placeholders) GROUP BY pa.pg_id HAVING COUNT(DISTINCT a.amenity_id) = ?)";
        $parameters = array_merge($parameters, $selected_amenities);
        $parameters[] = $selected_amenities_count;
    }
}
// Status check (uncomment later)
// $where_clauses[] = "p.status = ?";
// $parameters[] = 'approved';
// $use_positional_params = true;

// Filter out suspended listings
$where_clauses[] = "(p.is_suspended = 0 OR p.is_suspended IS NULL)";

// Combine SQL
$sql_query = $sql_base;
if (!empty($where_clauses)) {
    $sql_query .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql_query .= " GROUP BY p.pg_id ORDER BY p.created_at DESC";

// Execute Query
try {
    $stmt = $conn->prepare($sql_query);
    $stmt->execute($parameters); // Always use positional if $use_positional_params is true
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_listings_found = count($listings);
} catch (PDOException $e) {
     echo "<div style='color: red; border: 1px solid red; padding: 10px; margin: 10px;'>";
     echo "<b>Database Error:</b> " . htmlspecialchars($e->getMessage()) . "<br>";
     echo "<b>SQL Query:</b> " . htmlspecialchars($sql_query) . "<br>";
     echo "<b>Parameters:</b> <pre>" . htmlspecialchars(print_r($parameters, true)) . "</pre>";
     echo "</div>";
     $total_listings_found = 0;
     $listings = [];
}
?>

<div class="search-page-wrapper container">
    <!-- Search Header -->
    <div class="search-header-section">
        <h1 class="search-page-title">Find Your Perfect PG</h1>
        <p class="search-subtitle">Showing <strong><?php echo $total_listings_found; ?></strong> PGs <?php echo $location_display; ?></p>
    </div>
    
    <div class="search-content-area">
        <aside class="filters-sidebar">
            <h3>🔍 Filters</h3>
            <form action="search.php" method="GET">
                <div class="filter-group">
                    <h4>Location (City)</h4>
                    <input type="text" name="location" placeholder="Enter city" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
                </div>
                <div class="filter-group">
                    <h4>PG Type</h4>
                    <?php $current_pg_type = $_GET['pg_type'] ?? ''; ?>
                    <label><input type="radio" name="pg_type" value="male" <?php echo ($current_pg_type == 'male') ? 'checked' : ''; ?>> Male</label>
                    <label><input type="radio" name="pg_type" value="female" <?php echo ($current_pg_type == 'female') ? 'checked' : ''; ?>> Female</label>
                    <label><input type="radio" name="pg_type" value="coed" <?php echo ($current_pg_type == 'coed') ? 'checked' : ''; ?>> Co-ed</label>
                    <label><input type="radio" name="pg_type" value="" <?php echo ($current_pg_type == '') ? 'checked' : ''; ?>> Any</label>
                </div>
                <div class="filter-group">
                    <h4>Max Budget (Per Month)</h4>
                    <?php $current_max_budget = $_GET['max_budget'] ?? '50000'; ?>
                    <input type="range" id="budget_range" name="max_budget" min="100" max="50000" value="<?php echo htmlspecialchars($current_max_budget); ?>" step="500" oninput="updateBudgetDisplay(this.value)">
                    <p>Up to ₹<span id="budget_display"><?php echo htmlspecialchars($current_max_budget); ?></span></p>
                </div>
                <div class="filter-group">
                    <h4>Amenities</h4>
                    <?php
                        try {
                            $amenities_stmt = $conn->query("SELECT amenity_name FROM amenities ORDER BY amenity_name");
                            $all_amenities = $amenities_stmt->fetchAll(PDO::FETCH_ASSOC);
                            $current_amenities = $_GET['amenities'] ?? [];
                            foreach ($all_amenities as $amenity) {
                                $amenity_name = htmlspecialchars($amenity['amenity_name']);
                                $checked = (is_array($current_amenities) && in_array($amenity_name, $current_amenities)) ? 'checked' : '';
                                echo '<label><input type="checkbox" name="amenities[]" value="' . $amenity_name . '" ' . $checked . '> ' . ucfirst(str_replace('_', ' ', $amenity_name)) . '</label>';
                            }
                        } catch (PDOException $e) { echo "Could not load amenities."; }
                    ?>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" class="btn apply-filters-btn" style="flex: 1;">Apply Filters</button>
                    <a href="search.php" class="btn prev-step-btn" style="flex: 1; background-color:#aaa; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">Clear All</a>
                </div>
            </form>
        </aside>

        <section class="search-results-main">
            <div class="listings-container">
                <?php if (!empty($listings)): ?>
                    <?php foreach ($listings as $listing): ?>
                        <div class="pg-card">
                            <?php
                                $photo_filename = !empty($listing['photo_path']) ? htmlspecialchars($listing['photo_path']) : 'default_pg.png';
                                $photo_url = BASE_URL . 'uploads/pg_photos/' . $photo_filename;
                            ?>
                            <div class="card-image-wrapper">
                                <img src="<?php echo $photo_url; ?>" alt="<?php echo htmlspecialchars($listing['pg_name']); ?> Image">
                            </div>
                            <div class="card-content">
                                <h3><?php echo htmlspecialchars($listing['pg_name']); ?></h3>
                                <p class="location">📍 <?php echo htmlspecialchars($listing['location_city']); ?>, India</p>
                                <div class="rating">
                                    <span class="rating-stars">★★★★☆</span>
                                    <span class="rating-value">4.5</span>
                                </div>
                                <p class="price"><span class="price-label">From</span><br>₹<?php echo htmlspecialchars($listing['rent_per_person']); ?>/month</p>
                                <a href="<?php echo BASE_URL; ?>pg_details.php?id=<?php echo $listing['pg_id']; ?>" class="btn details-btn">View Details</a>
                                </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; width: 100%;">No PG listings found matching your criteria.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<script>
    function updateBudgetDisplay(value) { document.getElementById('budget_display').innerText = value; }
    updateBudgetDisplay(document.getElementById('budget_range').value);
</script>

<?php include 'includes/footer.php'; ?> 