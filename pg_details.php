<?php
session_start();
include 'includes/config.php'; // Database connection

// --- 1. GLOBAL SECURITY CHECK ---
// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
    $current_page_url = urlencode(BASE_URL . 'pg_details.php?id=' . $_GET['id']);
    header("Location: login.php?redirect=" . $current_page_page_url);
    exit;
}

// Security Check: Only Tenants can view details (Owners should not be here, but for safety)
if ($_SESSION['user_type'] != 'tenant' && $_SESSION['user_type'] != 'owner') {
     // Non-user/Unauthorized users are already redirected above. This is for extra safety.
}


// --- 2. Get PG ID from URL ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid PG ID.");
}
$pg_id = (int)$_GET['id']; 
$user_id = $_SESSION['user_id'];

// --- 3. Fetch PG Details from Database ---
$pg_details = null;
$pg_photos = [];
$pg_amenities = [];

try {
    // Fetch main PG details with location and owner info
    $sql_details = "SELECT p.*, pl.latitude, pl.longitude 
                   FROM pg_listings p 
                   LEFT JOIN pg_location pl ON p.pg_id = pl.pg_id 
                   WHERE p.pg_id = :pg_id";
    $stmt_details = $conn->prepare($sql_details);
    $stmt_details->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
    $stmt_details->execute();
    $pg_details = $stmt_details->fetch(PDO::FETCH_ASSOC);

    if (!$pg_details) {
        die("PG not found.");
    }

    // Check if listing is suspended - only owner and admin can view suspended listings
    if ($pg_details['is_suspended'] == 1) {
        $is_owner = ($pg_details['owner_id'] == $user_id);
        $is_admin = ($_SESSION['user_type'] == 'admin');
        
        if (!$is_owner && !$is_admin) {
            die("This listing is currently unavailable.");
        }
    }

    // Fetch photos and amenities (logic remains the same)
    $sql_photos = "SELECT photo_path FROM pg_photos WHERE pg_id = :pg_id ORDER BY is_primary DESC";
    $stmt_photos = $conn->prepare($sql_photos);
    $stmt_photos->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
    $stmt_photos->execute();
    $pg_photos = $stmt_photos->fetchAll(PDO::FETCH_ASSOC);

    $sql_amenities = "SELECT a.amenity_name FROM pg_amenities pa JOIN amenities a ON pa.amenity_id = a.amenity_id WHERE pa.pg_id = :pg_id";
    $stmt_amenities = $conn->prepare($sql_amenities);
    $stmt_amenities->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
    $stmt_amenities->execute();
    $pg_amenities = $stmt_amenities->fetchAll(PDO::FETCH_COLUMN);

    // --- Check if this PG is in the user's favorites (NEW) ---
    $is_favorite = false;
    if ($_SESSION['user_type'] == 'tenant') {
        $sql_fav = "SELECT COUNT(*) FROM favorites WHERE user_id = :user_id AND pg_id = :pg_id";
        $stmt_fav = $conn->prepare($sql_fav);
        $stmt_fav->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_fav->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
        $stmt_fav->execute();
        if ($stmt_fav->fetchColumn() > 0) {
            $is_favorite = true;
        }
    }

    // --- Fetch Reviews and Rating ---
    $reviews = [];
    $avg_rating = 0;
    $total_reviews = 0;
    $user_review = null;

    $sql_reviews = "SELECT r.*, u.full_name FROM reviews r 
                    JOIN users u ON r.user_id = u.user_id 
                    WHERE r.pg_id = :pg_id 
                    ORDER BY r.created_at DESC";
    $stmt_reviews = $conn->prepare($sql_reviews);
    $stmt_reviews->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
    $stmt_reviews->execute();
    $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($reviews)) {
        $total_reviews = count($reviews);
        $sum_rating = array_sum(array_column($reviews, 'rating'));
        $avg_rating = round($sum_rating / $total_reviews, 1);

        // Check if current user has already reviewed
        if (isset($_SESSION['user_id'])) {
            foreach ($reviews as $review) {
                if ($review['user_id'] == $_SESSION['user_id']) {
                    $user_review = $review;
                    break;
                }
            }
        }
    }

    // Track view (call API via JavaScript later)

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// --- 4. Include Header ---
include 'includes/header.php';
?>

<div class="pg-details-wrapper container">

    <?php
        if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])) {
            $msg_type = isset($_SESSION['success_message']) ? 'success' : 'error';
            $msg = $msg_type == 'success' ? $_SESSION['success_message'] : $_SESSION['error_message'];
            echo '<div class="dashboard-alert ' . $msg_type . '-message">' . htmlspecialchars($msg) . '</div>';
            unset($_SESSION['success_message'], $_SESSION['error_message']);
        }
    ?>

    <?php
        // Show suspension warning if listing is suspended
        if ($pg_details['is_suspended'] == 1):
            $is_owner = ($pg_details['owner_id'] == $user_id);
    ?>
        <div style="background: #fff3cd; border: 2px solid #856404; border-radius: 8px; padding: 20px; margin-bottom: 20px;">
            <h3 style="color: #856404; margin: 0 0 10px 0;">
                ⚠️ This Listing is Currently Suspended
            </h3>
            <?php if ($is_owner): ?>
                <p style="margin: 0; color: #856404; font-weight: 600;">
                    Your listing has been suspended by an administrator. It is not visible to potential tenants.
                </p>
                <?php if (!empty($pg_details['suspension_reason'])): ?>
                    <div style="background: white; padding: 15px; border-radius: 5px; margin-top: 10px;">
                        <strong style="color: #856404;">Reason for Suspension:</strong><br>
                        <p style="margin: 5px 0 0 0; color: #333;">
                            <?php echo nl2br(htmlspecialchars($pg_details['suspension_reason'])); ?>
                        </p>
                    </div>
                <?php endif; ?>
                <p style="margin: 10px 0 0 0; color: #856404;">
                    <em>Please contact the administrator if you have questions about this suspension.</em>
                </p>
            <?php else: ?>
                <p style="margin: 0; color: #856404;">
                    You are viewing this listing as an administrator. This listing is hidden from public view.
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="pg-details-container">

        <div class="pg-photos-section">
            <?php if (!empty($pg_photos)): ?>
                <div class="main-photo">
                    <img src="<?php echo BASE_URL . 'uploads/pg_photos/' . htmlspecialchars($pg_photos[0]['photo_path']); ?>" 
                         alt="<?php echo htmlspecialchars($pg_details['pg_name']); ?> Main Image">
                    <button class="fullscreen-btn" onclick="openGallery(0)">
                        <i class="fas fa-expand"></i> View Fullscreen
                    </button>
                </div>
                <?php if (count($pg_photos) > 1): ?>
                    <div class="thumbnail-photos">
                        <?php foreach ($pg_photos as $index => $photo): ?>
                            <img src="<?php echo BASE_URL . 'uploads/pg_photos/' . htmlspecialchars($photo['photo_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($pg_details['pg_name']); ?> Thumbnail"
                                 onclick="updatePreviewImage(<?php echo $index; ?>)"
                                 class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                 data-index="<?php echo $index; ?>">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="main-photo">
                     <img src="<?php echo BASE_URL . 'uploads/pg_photos/default_pg.png'; ?>" 
                          alt="<?php echo htmlspecialchars($pg_details['pg_name']); ?> Default Image">
                </div>
            <?php endif; ?>

            <!-- Location Map Below Thumbnails -->
            <?php if (!empty($pg_details['latitude']) && !empty($pg_details['longitude'])): ?>
                <div class="pg-location-preview">
                    <h3>Location Preview</h3>
                    <div id="pgLocationMap" style="width: 100%; height: 250px; border-radius: 8px; margin: 15px 0;"></div>
                    <div class="location-details">
                        <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($pg_details['location_city']); ?>, India</p>
                        <p class="full-address"><?php echo nl2br(htmlspecialchars($pg_details['address'])); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- REVIEWS SECTION - After Map/Address -->
            <div class="detail-section" style="margin-top: 30px;">
                <h3>Reviews & Ratings</h3>
                
                <!-- Submit Review Form -->
                <?php if ($_SESSION['user_type'] == 'tenant'): ?>
                <div class="review-form-container">
                    <h4><?php echo $user_review ? 'Update Your Review' : 'Leave a Review'; ?></h4>
                    <form id="reviewForm" class="review-form">
                        <input type="hidden" name="pg_id" value="<?php echo $pg_id; ?>">
                        
                        <div class="form-group">
                            <label>Rating:</label>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" 
                                           id="star<?php echo $i; ?>" 
                                           <?php echo ($user_review && $user_review['rating'] == $i) ? 'checked' : ''; ?>>
                                    <label for="star<?php echo $i; ?>" class="star-label">★</label>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reviewComment">Your Review:</label>
                            <textarea id="reviewComment" name="comment" rows="4" 
                                      placeholder="Share your experience at this PG..."
                                      required><?php echo $user_review ? htmlspecialchars($user_review['comment']) : ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn submit-btn">Submit Review</button>
                    </form>
                </div>
                <hr class="detail-divider">
                <?php endif; ?>

                <!-- Display Reviews -->
                <div class="reviews-list">
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                                <span class="review-rating">
                                    <?php for ($i = 1; $i <= 5; $i++) {
                                        echo ($i <= $review['rating']) ? '★' : '☆';
                                    } ?>
                                </span>
                            </div>
                            <small class="review-date">
                                <?php echo date('M d, Y', strtotime($review['created_at'])); ?>
                            </small>
                            <p class="review-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                            
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $review['user_id']): ?>
                            <div class="review-actions-inline">
                                <a href="dashboards/edit_review.php?id=<?php echo $review['review_id']; ?>" class="btn-edit-small">Edit</a>
                                <button onclick="confirmDeleteReview(<?php echo $review['review_id']; ?>)" class="btn-delete-small">Delete</button>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-reviews">No reviews yet. Be the first to review!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Gallery Modal -->
        <div id="galleryModal" class="gallery-modal">
            <span class="close-gallery">&times;</span>
            <button class="nav-btn prev-btn" onclick="changeImage(-1)">❮</button>
            <button class="nav-btn next-btn" onclick="changeImage(1)">❯</button>
            <div class="modal-content">
                <?php if (!empty($pg_photos)): foreach ($pg_photos as $photo): ?>
                    <img src="<?php echo BASE_URL . 'uploads/pg_photos/' . htmlspecialchars($photo['photo_path']); ?>" 
                         class="gallery-img" alt="<?php echo htmlspecialchars($pg_details['pg_name']); ?>">
                <?php endforeach; endif; ?>
            </div>
            <div class="image-counter">Image <span id="currentImageNum">1</span> of <?php echo count($pg_photos); ?></div>
        </div>

        <div class="pg-info-section">
            <h1><?php echo htmlspecialchars($pg_details['pg_name']); ?></h1>
            <div class="rating detail-rating">
                <span><?php echo $avg_rating; ?></span> 
                <?php 
                    for ($i = 1; $i <= 5; $i++) {
                        echo ($i <= $avg_rating) ? '★' : '☆';
                    }
                ?>
                <span class="review-count">(<?php echo $total_reviews; ?> Reviews)</span>
            </div>
            <p class="detail-location"><?php echo htmlspecialchars($pg_details['location_city']); ?>, India</p>
            <p class="detail-address"><?php echo nl2br(htmlspecialchars($pg_details['address'])); ?></p>

            <div class="detail-price">₹<?php echo htmlspecialchars($pg_details['rent_per_person']); ?> <span>/ month per person</span></div>

            <a href="#inquiry-form" class="btn submit-btn contact-owner-btn">Contact Owner / Send Inquiry</a>
            
            <div class="detail-section" style="text-align: center; margin-top: 20px;">
                <?php if ($_SESSION['user_type'] == 'tenant'): ?>
                    <?php if ($is_favorite): ?>
                        <a href="handle_favorite.php?pg_id=<?php echo $pg_id; ?>&action=remove" class="btn prev-step-btn" style="background-color: #dc3545; color: white; width: 80%;">★ Remove from Favorites</a>
                    <?php else: ?>
                        <a href="handle_favorite.php?pg_id=<?php echo $pg_id; ?>&action=add" class="btn next-step-btn" style="background-color: #28a745; color: white; width: 80%;">☆ Save to Favorites</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <hr class="detail-divider">

            <hr class="detail-divider">

            <div class="detail-section">
                <h3>Description</h3>
                <p><?php echo nl2br(htmlspecialchars($pg_details['description'])); ?></p>
            </div>

            <hr class="detail-divider">

            <div class="detail-section">
                <h3>Amenities</h3>
                <?php if (!empty($pg_amenities)): ?>
                    <div class="amenities-grid">
                        <?php foreach ($pg_amenities as $amenity): ?>
                            <div class="amenity-button">
                                <i class="fas fa-check-circle"></i>
                                <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($amenity))); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p>No specific amenities listed.</p>
                <?php endif; ?>
            </div>

            <hr class="detail-divider">

            <div class="detail-section">
                <h3>Rent & Other Details</h3>
                <ul>
                    <li><strong>Security Deposit:</strong> ₹<?php echo htmlspecialchars($pg_details['security_deposit']); ?></li>
                    <li><strong>Notice Period:</strong> <?php echo htmlspecialchars($pg_details['notice_period_days']); ?> days</li>
                    <li><strong>PG Type:</strong> <?php echo ucfirst(htmlspecialchars($pg_details['pg_type'])); ?></li>
                    <li><strong>Total Rooms:</strong> <?php echo htmlspecialchars($pg_details['num_rooms']); ?></li>
                </ul>
            </div>

            <hr class="detail-divider">

            <div class="detail-section" id="inquiry-form">
                <h3>Send Inquiry to Owner</h3>
                <?php if($_SESSION['user_type'] == 'tenant'): ?>
                    <form action="handle_inquiry.php" method="POST" class="pg-form simple-inquiry-form">
                        <input type="hidden" name="pg_id" value="<?php echo $pg_id; ?>">
                        <div class="form-group">
                           <label for="message">Your Message (Optional)</label>
                           <textarea id="message" name="message" rows="4" placeholder="Ask about availability, rules, etc. Your contact details will be sent."></textarea>
                        </div>
                        <button type="submit" class="btn submit-btn">Send Inquiry</button>
                    </form>
                <?php elseif($_SESSION['user_type'] == 'owner'): ?>
                     <p>You are currently logged in as the **Owner** of this listing. You cannot send an inquiry to yourself.</p>
                <?php endif; ?>
            </div>

        </div>

    </div>
</div>

<!-- Initialize gallery configuration -->
<script>
window.pgGalleryConfig = {
    totalImages: <?php echo !empty($pg_photos) ? count($pg_photos) : 0; ?>,
    latitude: <?php echo !empty($pg_details['latitude']) ? $pg_details['latitude'] : 'null'; ?>,
    longitude: <?php echo !empty($pg_details['longitude']) ? $pg_details['longitude'] : 'null'; ?>,
    pgName: <?php echo json_encode($pg_details['pg_name']); ?>,
    locationCity: <?php echo json_encode($pg_details['location_city']); ?>,
    pgId: <?php echo $pg_id; ?>
};
</script>

<!-- Review Form Handler -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Track view count
    fetch('track_view.php?pg_id=' + window.pgGalleryConfig.pgId)
        .catch(error => console.log('View tracked'));

    // Star rating hover and selection handling
    const starRating = document.querySelector('.star-rating');
    if (starRating) {
        const starLabels = starRating.querySelectorAll('.star-label');
        const starInputs = starRating.querySelectorAll('input[type="radio"]');
        
        // Handle hover
        starLabels.forEach((label, index) => {
            label.addEventListener('mouseenter', () => {
                highlightStars(index + 1);
            });
        });
        
        starRating.addEventListener('mouseleave', () => {
            const checkedInput = starRating.querySelector('input[type="radio"]:checked');
            if (checkedInput) {
                const checkedIndex = Array.from(starInputs).indexOf(checkedInput);
                highlightStars(checkedIndex + 1);
            } else {
                highlightStars(0);
            }
        });
        
        // Handle click
        starInputs.forEach((input, index) => {
            input.addEventListener('change', () => {
                highlightStars(index + 1);
            });
        });
        
        // Function to highlight stars from left to right
        function highlightStars(rating) {
            starLabels.forEach((label, index) => {
                if (index < rating) {
                    label.style.color = '#ffc107';
                } else {
                    label.style.color = '#ddd';
                }
            });
        }
        
        // Initialize with current rating if exists
        const checkedInput = starRating.querySelector('input[type="radio"]:checked');
        if (checkedInput) {
            const checkedIndex = Array.from(starInputs).indexOf(checkedInput);
            highlightStars(checkedIndex + 1);
        }
    }

    // Delete review confirmation
    function confirmDeleteReview(reviewId) {
        if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
            window.location.href = 'dashboards/delete_review.php?id=' + reviewId;
        }
    }
    window.confirmDeleteReview = confirmDeleteReview;

    // Review form submission
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(reviewForm);
            
            fetch('handle_review.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    }
});
</script>

<style>
.review-form-container {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.review-form-container h4 {
    margin-top: 0;
}

.star-rating {
    display: flex;
    gap: 10px;
    margin: 10px 0;
}

.star-rating input {
    display: none;
}

.star-label {
    font-size: 28px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s ease;
}

.star-rating input:checked ~ .star-label,
.star-label:hover,
.star-label:hover ~ .star-label {
    color: #ffc107;
}

.reviews-list {
    margin-top: 20px;
}

.review-item {
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    margin-bottom: 15px;
    background: #fff;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.review-rating {
    color: #ffc107;
    font-size: 16px;
}

.review-date {
    color: #999;
    display: block;
    margin-bottom: 8px;
}

.review-comment {
    margin: 0;
    line-height: 1.6;
    color: #333;
}

.review-actions-inline {
    margin-top: 10px;
    display: flex;
    gap: 10px;
}

.btn-edit-small, .btn-delete-small {
    padding: 5px 12px;
    font-size: 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-edit-small {
    background: #007bff;
    color: white;
    text-decoration: none;
}

.btn-edit-small:hover {
    background: #0056b3;
}

.btn-delete-small {
    background: #dc3545;
    color: white;
}

.btn-delete-small:hover {
    background: #c82333;
}

.no-reviews {
    text-align: center;
    color: #999;
    padding: 20px;
}
</style>
};
</script>

<?php
include 'includes/footer.php';
?>