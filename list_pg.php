<?php 
session_start();
include 'includes/config.php'; 

// Check if user is logged in
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
    $_SESSION['error_message'] = "Please login first to list your PG";
    $_SESSION['redirect_after_login'] = BASE_URL . 'list_pg.php';  // Store the page to redirect after login
    header("Location: " . BASE_URL . "login.php");
    exit();
}

// Check if user is logged in and is a tenant
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'tenant') {
    $_SESSION['error'] = "Access denied. Please login with your PG owner account to list a PG.";
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// Check for Edit Mode
$is_edit_mode = isset($_GET['edit']) && is_numeric($_GET['edit']);
$edit_pg_id = $is_edit_mode ? (int)$_GET['edit'] : null;
$listing_data = [];
$selected_amenities = [];
$photos_data = [];

if ($is_edit_mode && isset($_SESSION['edit_listing']) && $_SESSION['edit_listing']['pg_id'] == $edit_pg_id) {
    // Load data from session set by edit_listing.php
    $listing_data = $_SESSION['edit_listing']['data'];
    $selected_amenities = $_SESSION['edit_listing']['amenities'];
    $photos_data = $_SESSION['edit_listing']['photos'];
    // Clear session data once loaded
    unset($_SESSION['edit_listing']); 
}

// If not edit mode, use empty array to prevent errors
if (!$listing_data) {
    $listing_data = array_fill_keys(['pg_name', 'pg_type', 'location', 'address', 'num_rooms', 'description', 'rent', 'deposit', 'notice_period', 'owner_name', 'contact_number', 'email'], '');
    $listing_data['rent'] = $listing_data['deposit'] = $listing_data['notice_period'] = '';
}

// Header
include 'includes/header.php';
?>

<div class="list-pg-wrapper container">
    <div class="list-pg-container">

        <div class="list-pg-header">
            <h2><?php echo $is_edit_mode ? 'Edit Listing ID: ' . $edit_pg_id : 'List Your PG with PG Spotter'; ?></h2>
            <p>Easily list your property and connect with thousands of tenants.</p>
        </div>

        <?php
            // Error Message (from deletion or submission attempts)
            if (isset($_SESSION['error_message'])) {
                echo '<div class="message error-message">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']);
            }
        ?>

        <div class="step-indicator">
            <div class="step active clickable" data-step="1" onclick="goToStep(1)"><span>1</span> PG Details</div>
            <div class="step clickable" data-step="2" onclick="goToStep(2)"><span>2</span> Amenities & Rent</div>
            <div class="step clickable" data-step="3" onclick="goToStep(3)"><span>3</span> Photos</div>
            <div class="step clickable" data-step="4" onclick="goToStep(4)"><span>4</span> Contact</div>
        </div>

        <form action="<?php echo $is_edit_mode ? 'handle_list_pg.php?id=' . $edit_pg_id : 'handle_list_pg.php'; ?>" 
              method="POST" 
              class="pg-form" 
              enctype="multipart/form-data"
              id="pgListingForm">
            
            <?php if ($is_edit_mode): ?>
                <input type="hidden" name="edit_mode" value="<?php echo $edit_pg_id; ?>">
            <?php endif; ?>
            <!-- Add CSRF protection -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>">

            <style>
                #locationMap { 
                    width: 100%; 
                    border: 1px solid #ddd;
                    margin: 10px 0;
                }
                .map-help {
                    color: #666;
                    font-size: 0.9em;
                    margin-bottom: 15px;
                }
                .location-marker {
                    background-color: #ff4444;
                    border-radius: 50%;
                    border: 2px solid white;
                    box-shadow: 0 0 4px rgba(0,0,0,0.3);
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const mapElement = document.getElementById('locationMap');
                    let marker = null;

                    function setHiddenLatLng(lat, lng) {
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                    }

                    function initGooglePicker() {
                        try {
                            const savedLat = parseFloat(document.getElementById('latitude').value) || null;
                            const savedLng = parseFloat(document.getElementById('longitude').value) || null;
                            const center = savedLat && savedLng ? { lat: savedLat, lng: savedLng } : { lat: 20.5937, lng: 78.9629 };

                            const map = new google.maps.Map(mapElement, {
                                center: center,
                                zoom: savedLat && savedLng ? 16 : 5,
                                mapTypeId: 'satellite'
                            });

                            if (savedLat && savedLng) {
                                marker = new google.maps.Marker({ position: center, map: map });
                            }

                            // Click to place marker
                            map.addListener('click', function(e) {
                                const lat = e.latLng.lat();
                                const lng = e.latLng.lng();
                                setHiddenLatLng(lat, lng);
                                if (marker) marker.setPosition({ lat, lng });
                                else marker = new google.maps.Marker({ position: { lat, lng }, map: map });
                            });

                            // Try to center by city name when location field changes using Google Geocoder if available
                            const geocoder = new google.maps.Geocoder();
                            document.getElementById('location').addEventListener('change', function() {
                                const city = this.value;
                                if (city) {
                                    geocoder.geocode({ address: city }, function(results, status) {
                                        if (status === 'OK' && results[0]) {
                                            map.setCenter(results[0].geometry.location);
                                            map.setZoom(13);
                                        }
                                    });
                                }
                            });

                        } catch (err) {
                            console.error('Google picker error:', err);
                            mapElement.innerHTML = '<div style="height:100%;display:flex;align-items:center;justify-content:center;background:#f5f5f5;border-radius:8px;padding:20px;text-align:center;">Unable to initialize the map picker.</div>';
                        }
                    }

                    function initLeafletPicker() {
                        try {
                            const map = L.map('locationMap').setView([20.5937, 78.9629], 5);
                            const streetsLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '© OpenStreetMap contributors'
                            });
                            const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                                maxZoom: 19,
                                attribution: 'Tiles © Esri'
                            });
                            satelliteLayer.addTo(map);
                            L.control.layers({ 'Streets': streetsLayer, 'Satellite': satelliteLayer }).addTo(map);

                            const savedLat = document.getElementById('latitude').value;
                            const savedLng = document.getElementById('longitude').value;
                            if (savedLat && savedLng) {
                                marker = L.marker([savedLat, savedLng]).addTo(map);
                                map.setView([savedLat, savedLng], 16);
                            }

                            document.getElementById('location').addEventListener('change', function() {
                                const city = this.value;
                                if (city) {
                                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(city)}`)
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.length > 0) {
                                                map.setView([data[0].lat, data[0].lon], 13);
                                            }
                                        });
                                }
                            });

                            map.on('click', function(e) {
                                const lat = e.latlng.lat;
                                const lng = e.latlng.lng;
                                setHiddenLatLng(lat, lng);
                                if (marker) marker.setLatLng([lat, lng]);
                                else marker = L.marker([lat, lng]).addTo(map);
                            });

                        } catch (err) {
                            console.error('Leaflet picker error:', err);
                            mapElement.innerHTML = '<div style="height:100%;display:flex;align-items:center;justify-content:center;background:#f5f5f5;border-radius:8px;padding:20px;text-align:center;">Unable to initialize the map picker.</div>';
                        }
                    }

                    if (window.google && window.google.maps) {
                        initGooglePicker();
                    } else if (typeof L !== 'undefined') {
                        initLeafletPicker();
                    } else {
                        // Poll briefly for Google Maps or Leaflet to load
                        let tries = 0;
                        const t = setInterval(() => {
                            tries++;
                            if (window.google && window.google.maps) {
                                clearInterval(t);
                                initGooglePicker();
                            } else if (typeof L !== 'undefined') {
                                clearInterval(t);
                                initLeafletPicker();
                            } else if (tries > 25) {
                                clearInterval(t);
                                mapElement.innerHTML = '<div style="height:100%;display:flex;align-items:center;justify-content:center;background:#f5f5f5;border-radius:8px;padding:20px;text-align:center;">Unable to initialize the map picker.</div>';
                            }
                        }, 200);
                    }
                });
            </script>

            <div class="form-step active" data-step="1">
                <h3>Step 1: Tell Us About Your PG</h3>
                
                <div class="form-group">
                    <label for="pg_name">PG Name</label>
                    <input type="text" id="pg_name" name="pg_name" autocomplete="organization" value="<?php echo htmlspecialchars($listing_data['pg_name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="pg_type">PG Type (For)</label>
                    <select id="pg_type" name="pg_type" required>
                        <option value="">-- Select Type --</option>
                        <option value="male" <?php echo ($listing_data['pg_type'] ?? '') == 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo ($listing_data['pg_type'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="coed" <?php echo ($listing_data['pg_type'] ?? '') == 'coed' ? 'selected' : ''; ?>>Co-ed / Unisex</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="location">Location (City)</label>
                    <input type="text" id="location" name="location" autocomplete="address-level2" value="<?php echo htmlspecialchars($listing_data['location_city'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Select PG Location on Map</label>
                    <div id="locationMap" style="height: 300px; margin-bottom: 10px; border-radius: 8px;"></div>
                    <p class="map-help">Click on the map to mark your PG's exact location</p>
                    <input type="hidden" id="latitude" name="latitude" value="<?php echo htmlspecialchars($listing_data['latitude'] ?? ''); ?>" required>
                    <input type="hidden" id="longitude" name="longitude" value="<?php echo htmlspecialchars($listing_data['longitude'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Full Address</label>
                    <textarea id="address" name="address" autocomplete="street-address" rows="3" required><?php echo htmlspecialchars($listing_data['address'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="rooms">Number of Rooms</label>
                    <input type="number" id="rooms" name="rooms" min="1" autocomplete="off" value="<?php echo htmlspecialchars($listing_data['num_rooms'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Property Description</label>
                    <textarea id="description" name="description" rows="5" placeholder="Tell tenants what makes your PG special..."><?php echo htmlspecialchars($listing_data['description'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-navigation">
                    <button type="button" class="btn next-step-btn" data-next-step="2">Next Step &rarr;</button>
                </div>
            </div>

            <div class="form-step" data-step="2">
                 <h3>Step 2: Amenities & Rent</h3>

                <h4>Rent Details</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="rent">Monthly Rent (per person)</label>
                        <input type="number" id="rent" name="rent" placeholder="₹" min="0" autocomplete="off" value="<?php echo htmlspecialchars($listing_data['rent_per_person'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="deposit">Security Deposit</label>
                        <input type="number" id="deposit" name="deposit" placeholder="₹" min="0" autocomplete="off" value="<?php echo htmlspecialchars($listing_data['security_deposit'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notice_period">Notice Period (in days)</label>
                    <input type="number" id="notice_period" name="notice_period" min="0" autocomplete="off" value="<?php echo htmlspecialchars($listing_data['notice_period_days'] ?? '30'); ?>">
                </div>
                
                <h4>Included Services</h4>
                <div class="amenities-grid">
                    <?php 
                        $all_amenities = ['wifi', 'food', 'laundry', 'housekeeping', 'ac', 'parking', 'geyser', 'cctv', 'power_backup', 'attached_bathroom'];
                        $services = array_slice($all_amenities, 0, 4); // Included Services
                        $general = array_slice($all_amenities, 4); // General Amenities
                    ?>

                    <?php foreach ($services as $amenity): 
                        $checked = in_array($amenity, $selected_amenities) ? 'checked' : ''; 
                        $amenity_id = 'amenity_' . $amenity; ?>
                        <div class="amenity-box">
                            <input type="checkbox" id="<?php echo $amenity_id; ?>" name="amenities[]" value="<?php echo $amenity; ?>" <?php echo $checked; ?>>
                            <label for="<?php echo $amenity_id; ?>"><?php echo ucfirst($amenity); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h4>General Amenities</h4>
                <div class="amenities-grid">
                    <?php foreach ($general as $amenity): 
                        $checked = in_array($amenity, $selected_amenities) ? 'checked' : ''; 
                        $amenity_id = 'amenity_' . $amenity; ?>
                        <div class="amenity-box">
                            <input type="checkbox" id="<?php echo $amenity_id; ?>" name="amenities[]" value="<?php echo $amenity; ?>" <?php echo $checked; ?>>
                            <label for="<?php echo $amenity_id; ?>"><?php echo ucfirst(str_replace('_', ' ', $amenity)); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn prev-step-btn" data-prev-step="1">← Previous Step</button>
                    <button type="button" class="btn next-step-btn" data-next-step="3">Next Step →</button>
                </div>
            </div>

            <div class="form-step" data-step="3">
                 <h3>Step 3: Upload Photos</h3>
                
                <div class="form-group">
                    <label>Existing Photos (Delete if needed)</label>
                    <p>Mark one as Primary Photo. New photos will be added below.</p>
                    <div class="existing-photos-grid listings-container">
                        <?php if (!empty($photos_data)): ?>
                             <?php foreach ($photos_data as $photo): ?>
                                <div class="existing-photo-item pg-card <?php echo (!empty($photo['is_primary']) && $photo['is_primary']) ? 'primary-photo' : ''; ?>">
                                     <img src="<?php echo BASE_URL . 'uploads/pg_photos/' . htmlspecialchars($photo['photo_path']); ?>" alt="Photo">
                                     <div class="photo-controls">
                                         <label class="primary-photo-label">
                                             <input type="radio" name="primary_photo" value="<?php echo htmlspecialchars($photo['photo_path']); ?>"
                                                    <?php echo (!empty($photo['is_primary']) && $photo['is_primary']) ? 'checked' : ''; ?>>
                                             Make Primary
                                         </label>
                                         <a href="handle_photo_delete.php?pg=<?php echo $edit_pg_id; ?>&photo=<?php echo $photo['photo_id']; ?>" onclick="return confirm('Delete this photo?');" style="color:red; font-size:12px; margin-left:10px;">Delete</a>
                                         <?php if (!empty($photo['is_primary']) && $photo['is_primary']): ?>
                                             <span class="primary-badge">Primary Photo</span>
                                         <?php endif; ?>
                                     </div>
                                     <input type="hidden" name="existing_photos[]" value="<?php echo htmlspecialchars($photo['photo_path']); ?>">
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No photos uploaded yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Upload New Photos</label>
                    <div class="photo-upload-box">
                        <input type="file" id="pg_photos" name="pg_photos[]" multiple accept="image/png, image/jpeg">
                        <label for="pg_photos" class="upload-label">
                            <strong>Drag & Drop Photos Here</strong><br>
                            or Click to Upload
                        </label>
                    </div>
                    <div id="photo-preview-container" class="photo-preview-grid">
                        <!-- Preview images will be added here -->
                    </div>
                </div>

                <style>
                    .photo-preview-grid {
                        display: grid;
                        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                        gap: 15px;
                        margin-top: 20px;
                    }
                    .preview-item {
                        position: relative;
                        aspect-ratio: 1;
                        border: 1px solid #ddd;
                        border-radius: 8px;
                        overflow: hidden;
                    }
                    .preview-item img {
                        width: 100%;
                        height: 100%;
                        object-fit: cover;
                    }
                    .remove-preview {
                        position: absolute;
                        top: 5px;
                        right: 5px;
                        background: rgba(255, 255, 255, 0.8);
                        border: none;
                        border-radius: 50%;
                        width: 24px;
                        height: 24px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #ff4444;
                        font-weight: bold;
                    }
                    .photo-upload-box {
                        border: 2px dashed #ccc;
                        padding: 20px;
                        text-align: center;
                        background: #f9f9f9;
                        border-radius: 8px;
                        margin-bottom: 15px;
                    }
                    .photo-upload-box.dragover {
                        border-color: #4CAF50;
                        background: #e8f5e9;
                    }
                </style>
                
                <div class="form-navigation">
                    <button type="button" class="btn prev-step-btn" data-prev-step="2">← Previous Step</button>
                    <button type="button" class="btn next-step-btn" data-next-step="4">Next Step →</button>
                </div>
            </div>

            <div class="form-step" data-step="4">
                 <h3>Step 4: Contact & Publish</h3>
                
                <div class="form-group">
                    <label for="owner_name">Owner's Full Name</label>
                    <input type="text" id="owner_name" name="owner_name" autocomplete="name" value="<?php echo htmlspecialchars($listing_data['owner_contact_name'] ?? ''); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="contact_number">Primary Contact Number</label>
                        <input type="text" id="contact_number" name="contact_number" autocomplete="tel" value="<?php echo htmlspecialchars($listing_data['owner_contact_number'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" autocomplete="email" value="<?php echo htmlspecialchars($listing_data['owner_email'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-group terms-group">
                    <label>
                        <input type="checkbox" id="terms" name="terms" required>
                        I agree to the PG Spotter <a href="terms.php" target="_blank">Terms and Conditions</a> and <a href="privacy.php" target="_blank">Privacy Policy</a>.
                    </label>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn prev-step-btn" data-prev-step="3">← Previous Step</button>
                    <button type="submit" class="btn submit-btn" id="submitButton"><?php echo $is_edit_mode ? 'Update Listing' : 'Submit Listing'; ?></button>
                </div>
            </div>

        </form>
    </div>
</div>

<div id="submitStatus" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
     background: rgba(0,0,0,0.8); color: white; padding: 20px; border-radius: 8px; z-index: 1000;">
    Submitting your listing... Please wait.
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Form submission handling
    const form = document.getElementById('pgListingForm') || document.querySelector('.pg-form');
    const submitStatus = document.getElementById('submitStatus');
    const submitBtn = document.getElementById('submitButton') || document.querySelector('.submit-btn');
    console.log('list_pg script initialized', { formExists: !!form, submitBtnExists: !!submitBtn });

    // Add click handlers for step indicators
    document.querySelectorAll('.step[data-step]').forEach(step => {
        step.addEventListener('click', function() {
            const stepNumber = this.getAttribute('data-step');
            goToStep(parseInt(stepNumber));
        });
    });

    // Validation functions for each step
    // Function to show error message
    function showError(message, element) {
        let errorDiv = element.parentElement.querySelector('.error-message');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            element.parentElement.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }

    // Function to clear error message
    function clearError(element) {
        const errorDiv = element.parentElement.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }

        // Delegated listener to visually mark primary selection from existing or new uploads
        document.addEventListener('change', function(e) {
            if (e.target && (e.target.name === 'primary_photo' || e.target.name === 'primary_photo_new')) {
                document.querySelectorAll('.preview-item, .existing-photo-item').forEach(it => it.classList.remove('primary-photo'));
                const container = e.target.closest('.preview-item') || e.target.closest('.existing-photo-item');
                if (container) container.classList.add('primary-photo');
            }
        });

    function validateStep1() {
        const requiredFields = [
            { id: 'pg_name', message: 'PG Name is required' },
            { id: 'pg_type', message: 'Please select a PG Type' },
            { id: 'location', message: 'Location is required' },
            { id: 'address', message: 'Address is required' },
            { id: 'rooms', message: 'Number of rooms is required' }
        ];
        let isValid = true;
        
        requiredFields.forEach(field => {
            const input = document.getElementById(field.id);
            if (!input.value.trim()) {
                input.classList.add('invalid');
                showError(field.message, input);
                isValid = false;
            } else {
                input.classList.remove('invalid');
                clearError(input);
            }
        });
        
        return isValid;
    }

    function validateStep2() {
        const rentFields = [
            { id: 'rent', message: 'Monthly rent is required' },
            { id: 'deposit', message: 'Security deposit is required' },
            { id: 'notice_period', message: 'Notice period is required' }
        ];
        let isValid = true;

        // Validate rent fields
        rentFields.forEach(field => {
            const input = document.getElementById(field.id);
            if (!input.value.trim()) {
                input.classList.add('invalid');
                showError(field.message, input);
                isValid = false;
            } else {
                input.classList.remove('invalid');
                clearError(input);
            }
        });

        // Check included services
        const servicesGrid = document.querySelector('.amenities-grid');
        const serviceCheckboxes = servicesGrid.querySelectorAll('input[type="checkbox"]');
        const hasService = Array.from(serviceCheckboxes).some(cb => cb.checked);
        
        // Create or get error container for services
        let servicesErrorDiv = document.createElement('div');
        servicesErrorDiv.className = 'error-message amenities-error';
        if (!servicesGrid.nextElementSibling?.classList.contains('error-message')) {
            servicesGrid.insertAdjacentElement('afterend', servicesErrorDiv);
        } else {
            servicesErrorDiv = servicesGrid.nextElementSibling;
        }
        
        // Check general amenities (second grid)
        const amenitiesGrid = document.querySelectorAll('.amenities-grid')[1];
        const amenityCheckboxes = amenitiesGrid.querySelectorAll('input[type="checkbox"]');
        const hasAmenity = Array.from(amenityCheckboxes).some(cb => cb.checked);
        
        // Create or get error container for amenities
        let amenitiesErrorDiv = document.createElement('div');
        amenitiesErrorDiv.className = 'error-message amenities-error';
        if (!amenitiesGrid.nextElementSibling?.classList.contains('error-message')) {
            amenitiesGrid.insertAdjacentElement('afterend', amenitiesErrorDiv);
        } else {
            amenitiesErrorDiv = amenitiesGrid.nextElementSibling;
        }

        if (!hasService) {
            servicesErrorDiv.textContent = 'Please select at least one included service';
            servicesErrorDiv.style.display = 'block';
            servicesGrid.classList.add('invalid-section');
            isValid = false;
        } else {
            servicesErrorDiv.style.display = 'none';
            servicesGrid.classList.remove('invalid-section');
        }

        if (!hasAmenity) {
            amenitiesErrorDiv.textContent = 'Please select at least one general amenity';
            amenitiesErrorDiv.style.display = 'block';
            amenitiesGrid.classList.add('invalid-section');
            isValid = false;
        } else {
            amenitiesErrorDiv.style.display = 'none';
            amenitiesGrid.classList.remove('invalid-section');
        }

        return isValid;
    }

    function validateStep3() {
        const photoInput = document.getElementById('pg_photos');
        const photoContainer = photoInput.closest('.form-group');
        const existingPhotos = document.querySelector('.existing-photos-grid');
        const hasExistingPhotos = existingPhotos && existingPhotos.querySelector('.existing-photo-item');
        
        if (photoInput.files.length === 0 && !hasExistingPhotos) {
            showError('Please upload at least one photo of your PG', photoContainer);
            return false;
        } else {
            clearError(photoContainer);
            return true;
        }
    }

    function validateStep4() {
        const requiredFields = ['owner_name', 'contact_number', 'email', 'terms'];
        let isValid = true;

        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            if (!input.value.trim() || (field === 'terms' && !input.checked)) {
                input.classList.add('invalid');
                isValid = false;
            } else {
                input.classList.remove('invalid');
            }
        });

        return isValid;
    }

    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate all steps
            for (let i = 1; i <= 4; i++) {
                if (!validateStep(i)) {
                    const stepName = document.querySelector(`.step[data-step="${i}"]`).textContent.trim().split(' ')[1];
                    alert(`Please complete all required fields in ${stepName}`);
                    goToStep(i);
                    return false;
                }
            }

            // Show submission status
            if (submitStatus) submitStatus.style.display = 'block';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
            }

            try {
                // Create FormData object
                const formData = new FormData(this);
                
                // Add amenities data
                const services = Array.from(document.querySelectorAll('.amenities-grid:first-of-type input[type="checkbox"]:checked')).map(cb => cb.value);
                const amenities = Array.from(document.querySelectorAll('.amenities-grid:last-of-type input[type="checkbox"]:checked')).map(cb => cb.value);
                
                formData.append('included_services', JSON.stringify(services));
                formData.append('general_amenities', JSON.stringify(amenities));

                // Submit the form
                this.submit();
            } catch (error) {
                console.error('Submission error:', error);
                alert('An error occurred while submitting the form. Please try again.');
                if (submitStatus) submitStatus.style.display = 'none';
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('loading');
                }
            }
        });
    }

    // Prevent accidental form submission when pressing enter
    form.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });
    // Image preview functionality
    const photoInput = document.getElementById('pg_photos');
    const previewContainer = document.getElementById('photo-preview-container');
    const uploadBox = document.querySelector('.photo-upload-box');

    // Handle drag and drop (only if elements exist)
    if (uploadBox) {
        uploadBox.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadBox.classList.add('dragover');
        });

        uploadBox.addEventListener('dragleave', () => {
            uploadBox.classList.remove('dragover');
        });

        uploadBox.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadBox.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                if (photoInput) photoInput.files = e.dataTransfer.files;
                handleFileSelect(e.dataTransfer.files);
            }
        });
    }

    // Handle file selection
    if (photoInput) {
        photoInput.addEventListener('change', (e) => {
            handleFileSelect(e.target.files);
        });
    }

    function handleFileSelect(files) {
        if (!previewContainer) return;
        previewContainer.innerHTML = ''; // Clear existing previews
        
        Array.from(files).forEach((file, index) => {
            if (!file.type.match('image.*')) {
                return;
            }

            const reader = new FileReader();
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            
                reader.onload = (e) => {
                previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <div class="photo-controls">
                        <label class="primary-photo-label">
                            <input type="radio" name="primary_photo_new" value="${index}">
                            Make Primary
                        </label>
                        <button type="button" class="remove-preview" data-index="${index}">&times;</button>
                    </div>
                `;
                previewContainer.appendChild(previewItem);

                // Add remove button functionality
                const removeBtn = previewItem.querySelector('.remove-preview');
                removeBtn.addEventListener('click', () => {
                    previewItem.remove();
                    // Create a new FileList without the removed file
                    const dt = new DataTransfer();
                    Array.from(photoInput.files)
                        .filter((_, i) => i !== index)
                        .forEach(file => dt.items.add(file));
                    photoInput.files = dt.files;
                });

                // Primary radio change: visually mark selected preview as primary
                const radio = previewItem.querySelector('input[name="primary_photo_new"]');
                radio.addEventListener('change', (ev) => {
                    // Remove primary class from all previews and existing photo items
                    document.querySelectorAll('.preview-item, .existing-photo-item').forEach(it => it.classList.remove('primary-photo'));
                    previewItem.classList.add('primary-photo');
                });
            };

            reader.readAsDataURL(file);
        });
    }

    const nextButtons = document.querySelectorAll('.next-step-btn');
    const prevButtons = document.querySelectorAll('.prev-step-btn');
    
    function goToStep(stepNumber) {
        stepNumber = parseInt(stepNumber);
        const currentStepNumber = parseInt(document.querySelector('.form-step.active').dataset.step);
        
        // Always allow going back to previous steps
        if (stepNumber < currentStepNumber) {
            switchToStep(stepNumber);
            return true;
        }
        
        // Validate all previous steps before moving forward
        if (stepNumber > currentStepNumber) {
            for (let i = 1; i < stepNumber; i++) {
                if (!validateStep(i)) {
                    const stepName = document.querySelector(`.step[data-step="${i}"]`).textContent.trim().split(' ')[1];
                    alert(`Please complete all required fields in ${stepName} before proceeding.`);
                    switchToStep(i);
                    return false;
                }
            }
        }

        // If we're moving forward, validate current step
        if (stepNumber > currentStepNumber && !validateStep(currentStepNumber)) {
            return false;
        }

        switchToStep(stepNumber);
        return true;
    }

    function switchToStep(stepNumber) {
        // Update active classes
        document.querySelector('.form-step.active').classList.remove('active');
        document.querySelector('.step.active').classList.remove('active');
        
        document.querySelector(`.form-step[data-step="${stepNumber}"]`).classList.add('active');
        document.querySelector(`.step[data-step="${stepNumber}"]`).classList.add('active');

        // Scroll to top of the form
        document.querySelector('.list-pg-container').scrollIntoView({ behavior: 'smooth' });
    }

    function validateStep(stepNumber) {
        switch(stepNumber) {
            case 1: return validateStep1();
            case 2: return validateStep2();
            case 3: return validateStep3();
            case 4: return validateStep4();
            default: return true;
        }
    }

    nextButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const nextStepNumber = parseInt(button.getAttribute('data-next-step'));
            const currentStepNumber = parseInt(button.closest('.form-step').dataset.step);
            
            console.log('Validating step:', currentStepNumber);
            if (validateStep(currentStepNumber)) {
                console.log('Validation passed, moving to step:', nextStepNumber);
                switchToStep(nextStepNumber);
            } else {
                console.log('Validation failed for step:', currentStepNumber);
            }
        });
    });

    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            let currentStep = button.closest('.form-step');
            currentStep.classList.remove('active');
            let prevStepNumber = button.getAttribute('data-prev-step');
            let prevStep = document.querySelector(`.form-step[data-step="${prevStepNumber}"]`);
            prevStep.classList.add('active');
            document.querySelector('.step.active').classList.remove('active');
            document.querySelector(`.step[data-step="${prevStepNumber}"]`).classList.add('active');
        });
    });

});
</script>

<style>
/* Form validation styles */
.invalid {
    border-color: #ff4444 !important;
    background-color: #fff8f8;
}

.form-group input.invalid:focus,
.form-group textarea.invalid:focus {
    box-shadow: 0 0 0 2px rgba(255, 68, 68, 0.2);
}

.error-message {
    color: #ff4444;
    font-size: 0.875rem;
    margin-top: 5px;
    display: none;
}

.amenities-grid .error-message,
.amenities-error {
    grid-column: 1 / -1;
    margin: 10px 0;
    padding: 8px;
    background-color: #fff8f8;
    border-radius: 4px;
    border: 1px solid #ff4444;
    text-align: center;
    display: none;
}

.invalid-section {
    border: 2px solid #ff4444;
    background-color: #fff8f8;
    border-radius: 4px;
    padding: 10px;
    position: relative;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.error-message.amenities-error {
    display: none;
    margin: 10px 0;
    padding: 8px 15px;
    color: #ff4444;
    background-color: #fff8f8;
    border: 1px solid #ff4444;
    border-radius: 4px;
    text-align: center;
    font-weight: bold;
}

.step.clickable {
    cursor: pointer;
}

.step.clickable:hover {
    opacity: 0.8;
}

/* Add loading spinner */
.submit-btn.loading {
    position: relative;
    color: transparent !important;
}

.submit-btn.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
    border: 4px solid #ffffff;
    border-top-color: transparent;
    border-radius: 50%;
    animation: button-loading-spinner 1s ease infinite;
}

@keyframes button-loading-spinner {
    from {
        transform: rotate(0turn);
    }
    to {
        transform: rotate(1turn);
    }
}
</style>


<?php 
include 'includes/footer.php'; 
?>