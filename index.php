<?php
/**
 * PG Spotter - Homepage
 * 
 * @description Main landing page of the PG Spotter website.
 * Displays featured listings, search functionality, and hero section.
 * 
 * @author Doman Verma
 * @version 1.0
 */

// Initialize session
session_start();

// Include required files
include 'includes/config.php';  // Database configuration
include 'includes/header.php';  // Site header

// Fetch featured listings for homepage display
$listings = [];
try {
    // Query: Featured listings (max 3) with one photo path, excluding suspended listings
    $sql = "SELECT p.pg_id, p.pg_name, p.location_city, p.rent_per_person, MIN(ph.photo_path) AS photo_path
            FROM pg_listings p
            LEFT JOIN pg_photos ph ON p.pg_id = ph.pg_id
            WHERE (p.is_suspended = 0 OR p.is_suspended IS NULL)
            GROUP BY p.pg_id
            ORDER BY p.created_at DESC
            LIMIT 3"; 

    $stmt = $conn->query($sql);
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
     echo "Error fetching listings: " . $e->getMessage(); // For debugging
}
?>

<!-- Enhanced Hero Section -->
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="container hero-content-wrapper">
        <div class="hero-text-section">
            <h1 class="hero-title">Spot Your Perfect PG</h1>
            <p class="hero-subtitle">Discover the best paying guest accommodations across India</p>
            <form action="search.php" method="GET" class="search-form">
                <div class="search-form-wrapper">
                    <div class="search-input-group">
                        <input type="text" name="location" placeholder="Enter Location" required>
                        <span class="search-icon">📍</span>
                    </div>
                    <div class="search-input-group">
                        <input type="number" name="budget" placeholder="Max Budget" min="1000">
                        <span class="search-icon">₹</span>
                    </div>
                    <div class="search-input-group">
                        <input type="text" name="amenities" placeholder="Amenities">
                        <span class="search-icon">⭐</span>
                    </div>
                    <button type="submit" class="btn spot-it-btn">Spot It!</button>
                </div>
            </form>
        </div>
    </div>
</section>
<section class="featured-listings container">
    <div class="section-header">
        <h2>✨ Featured PG Listings</h2>
        <p class="section-subtitle">Handpicked properties for the best experience</p>
    </div>

    <div class="listings-container">

        <?php if (!empty($listings)): ?>
            <?php foreach ($listings as $listing): ?>
                <div class="pg-card">
                    <?php 
                        // Photo path check karo
                        $photo_filename = !empty($listing['photo_path']) ? htmlspecialchars($listing['photo_path']) : 'default_pg.png'; 
                        $photo_url = BASE_URL . 'uploads/pg_photos/' . $photo_filename;
                    ?>
                    <div class="card-image-wrapper">
                        <img src="<?php echo $photo_url; ?>" alt="<?php echo htmlspecialchars($listing['pg_name']); ?> Image"> 
                        <span class="featured-badge">Featured</span>
                    </div>
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($listing['pg_name']); ?></h3>
                        <p class="location">📍 <?php echo htmlspecialchars($listing['location_city']); ?>, India</p>
                        <div class="rating">
                            <span class="rating-stars">★★★★☆</span>
                            <span class="rating-value">4.5</span>
                        </div>
                        <p class="price">₹<?php echo htmlspecialchars($listing['rent_per_person']); ?><span class="price-label">/month</span></p>
                        <a href="<?php echo BASE_URL; ?>pg_details.php?id=<?php echo $listing['pg_id']; ?>" class="btn details-btn">View Details →</a>
                        </div>
                </div>
                <?php endforeach; ?>
        <?php else: ?>
            <div class="no-listings-message">
                <p>No featured PG listings found yet. Check back soon!</p>
            </div>
        <?php endif; ?>

    </div>
    <div class="view-more-section">
        <a href="<?php echo BASE_URL; ?>search.php" class="btn browse-all-btn">Browse All Listings</a>
    </div>
</section>


<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to List Your Property?</h2>
            <p>Join thousands of property owners earning passive income</p>
            <a href="<?php echo BASE_URL; ?>signup.php" class="btn cta-primary-btn">Get Started as Owner</a>
            <p class="cta-secondary-text">or browse thousands of listings as a tenant</p>
            <a href="<?php echo BASE_URL; ?>search.php" class="btn cta-secondary-btn">View All Listings</a>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container">
        <div class="section-header">
            <h2>🗺️ Explore PGs on the Map</h2>
            <p class="section-subtitle">Find PGs in your preferred location across India</p>
        </div>
        <div id="pgMap" class="map-container"></div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mapDiv = document.getElementById('pgMap');

    function renderUnable(message) {
        mapDiv.innerHTML = `<div style="height:100%;display:flex;align-items:center;justify-content:center;background:#f5f5f5;border-radius:8px;padding:20px;text-align:center;">${message}</div>`;
    }

    function initGoogleMap() {
        try {
            const center = { lat: 20.5937, lng: 78.9629 };
            const indiaBounds = {
                north: 35.817, // approx northernmost lat of India
                south: 6.554,  // approx southernmost lat of India
                west: 68.111,  // approx westernmost lng of India
                east: 97.395   // approx easternmost lng of India
            };

            const map = new google.maps.Map(mapDiv, {
                center: center,
                zoom: 5,
                minZoom: 4,
                maxZoom: 18,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_RIGHT,
                    mapTypeIds: [
                        google.maps.MapTypeId.ROADMAP,
                        google.maps.MapTypeId.HYBRID
                    ]
                },
                // Show all important features
                streetViewControl: true,
                scaleControl: true,
                zoomControl: true,
                rotateControl: true,
                fullscreenControl: true,
                // Enhanced map features
                styles: [],  // Keep default Google styling for POIs
                // Show labels and POIs
                mapId: "", // Use default Google Maps styling
                // Always show labels
                backgroundColor: '#fff',
                // Restrict map to India bounding box
                restriction: {
                    latLngBounds: indiaBounds,
                    strictBounds: true
                }
            });

            // Enable all relevant layers for enhanced detail
            map.setOptions({
                // Show transit options
                transit: { visible: true },
                // Show roads and labels
                roads: { visible: true },
                // Show landmarks and businesses
                landmarks: { visible: true },
                // Show point of interest labels
                poi: { visible: true }
            });

            // Explicitly fit and enforce India bounds to avoid a world view
            try {
                const indiaLatLngBounds = new google.maps.LatLngBounds(
                    new google.maps.LatLng(indiaBounds.south, indiaBounds.west),
                    new google.maps.LatLng(indiaBounds.north, indiaBounds.east)
                );
                map.fitBounds(indiaLatLngBounds);
                // Ensure the map stays within bounds if restriction isn't applied
                google.maps.event.addListener(map, 'idle', function() {
                    const currentBounds = map.getBounds();
                    if (!currentBounds) return;
                    if (!indiaLatLngBounds.contains(currentBounds.getNorthEast()) || !indiaLatLngBounds.contains(currentBounds.getSouthWest())) {
                        map.fitBounds(indiaLatLngBounds);
                    }
                });
            } catch (e) {
                console.warn('Could not enforce India bounds on Google Maps:', e);
            }

            const infoWindow = new google.maps.InfoWindow();

            // Custom marker style for better visibility on satellite
            const markerStyle = {
                path: google.maps.SymbolPath.CIRCLE,
                fillColor: '#FF4444',
                fillOpacity: 1,
                strokeColor: '#FFFFFF',
                strokeWeight: 2,
                scale: 10
            };

            // Track cities and their average coordinates for labels
            const citiesMap = new Map();

            // Enable enhanced map features with transit and POIs
            const transitLayer = new google.maps.TransitLayer();
            transitLayer.setMap(map);

            // Enable traffic layer
            const trafficLayer = new google.maps.TrafficLayer();
            trafficLayer.setMap(map);

            // Style for city labels
            const cityLabelStyle = {
                strokeColor: '#000000',
                strokeWeight: 3,
                strokeOpacity: 0.8,
                fillColor: '#FFFFFF',
                fillOpacity: 1.0,
                scale: 12,
                text: ''
            };

            fetch('get_pg_locations.php')
                .then(resp => { if (!resp.ok) throw new Error(resp.statusText); return resp.json(); })
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        renderUnable('No PG locations available to show on the map.');
                        return;
                    }

                    data.forEach(loc => {
                        const lat = parseFloat(loc.latitude);
                        const lng = parseFloat(loc.longitude);
                        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

                        const marker = new google.maps.Marker({
                            position: { lat: lat, lng: lng },
                            map: map,
                            icon: markerStyle,
                            label: {
                                text: loc.pg_name.substring(0, 1),
                                color: '#FFFFFF',
                                fontSize: '14px',
                                fontWeight: 'bold'
                            },
                            title: loc.pg_name
                        });

                        const content = `
                            <div class="map-info-window">
                                <h3>${loc.pg_name}</h3>
                                <p>${loc.location_city}</p>
                                <p>₹${loc.rent_per_person}/month</p>
                                <a href="pg_details.php?id=${loc.pg_id}">View Details</a>
                            </div>
                        `;

                        marker.addListener('click', () => {
                            infoWindow.setContent(content);
                            infoWindow.open(map, marker);
                        });
                    });

                    // Add city labels after all markers
                    citiesMap.forEach((coords, cityName) => {
                        if (coords.count >= 1) {
                            new google.maps.Marker({
                                position: { lat: coords.lat, lng: coords.lng },
                                map: map,
                                icon: {
                                    path: 'M -2,-2 2,-2 2,2 -2,2 z', // Rectangle shape
                                    fillColor: '#FFFFFF',
                                    fillOpacity: 0.8,
                                    strokeColor: '#000000',
                                    strokeWeight: 2,
                                    scale: 30,
                                },
                                label: {
                                    text: `${cityName} (${coords.count})`,
                                    color: '#000000',
                                    fontSize: '14px',
                                    fontWeight: 'bold'
                                },
                                zIndex: 1000 // Keep city labels on top
                            });
                        }
                    });
                })
                .catch(err => {
                    console.error('Failed to load PG locations (Google):', err);
                    renderUnable('Unable to load map data. Please try again later.');
                });
        } catch (e) {
            console.error('Google Map init error:', e);
            renderUnable('Unable to initialize the map.');
        }
    }

    function initLeafletMap() {
        try {
            // Leaflet: restrict to India bounds and set initial view
            const indiaBoundsLeaflet = L.latLngBounds([
                [6.554, 68.111],   // south-west (lat, lng)
                [35.817, 97.395]   // north-east (lat, lng)
            ]);

            const map = L.map('pgMap', {
                maxBounds: indiaBoundsLeaflet,
                maxBoundsViscosity: 1.0
            });

            // Fit the map to India's bounds initially
            map.fitBounds(indiaBoundsLeaflet);

            const streets = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            });

            const satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: 'Tiles © Esri'
            });

            streets.addTo(map);
            L.control.layers({ 'Streets': streets, 'Satellite': satellite }).addTo(map);

            // Prevent users from panning outside India bounds and enforce min zoom
            try {
                map.setMaxBounds(indiaBoundsLeaflet);
                map.setMinZoom(4);
            } catch (e) {
                // ignore if methods unavailable in older Leaflet builds
            }

            fetch('get_pg_locations.php')
                .then(response => { if (!response.ok) throw new Error(response.statusText); return response.json(); })
                .then(data => {
                    if (!Array.isArray(data) || data.length === 0) {
                        renderUnable('No PG locations available to show on the map.');
                        return;
                    }

                    data.forEach(loc => {
                        const lat = parseFloat(loc.latitude);
                        const lng = parseFloat(loc.longitude);
                        if (Number.isFinite(lat) && Number.isFinite(lng)) {
                            const marker = L.marker([lat, lng]).addTo(map);
                            marker.bindPopup(`
                                <div class="map-info-window">
                                    <h3>${loc.pg_name}</h3>
                                    <p>${loc.location_city}</p>
                                    <p>₹${loc.rent_per_person}/month</p>
                                    <a href="pg_details.php?id=${loc.pg_id}">View Details</a>
                                </div>
                            `);
                        }
                    });
                })
                .catch(err => {
                    console.error('Failed to load PG locations (Leaflet):', err);
                    renderUnable('Unable to load map data. Please try again later.');
                });
        } catch (err) {
            console.error('Leaflet Map init error:', err);
            renderUnable('Unable to initialize the map.');
        }
    }

    // Try Google Maps first if available, otherwise fall back to Leaflet. Poll briefly if script is still loading.
    if (window.google && window.google.maps) {
        initGoogleMap();
    } else if (typeof L !== 'undefined') {
        initLeafletMap();
    } else {
        let attempts = 0;
        const t = setInterval(() => {
            attempts++;
            if (window.google && window.google.maps) {
                clearInterval(t);
                initGoogleMap();
            } else if (typeof L !== 'undefined') {
                clearInterval(t);
                initLeafletMap();
            } else if (attempts > 25) {
                clearInterval(t);
                renderUnable('Unable to initialize the map.');
            }
        }, 200);
    }
});
</script>

<!-- Testimonials Section -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header">
            <h2>💬 Hear from our Spotters</h2>
            <p class="section-subtitle">Real experiences from our community members</p>
        </div>
        <div class="testimonials-container">
            <div class="testimonial-card">
                <div class="testimonial-header">
                    <div class="testimonial-rating">★★★★★</div>
                    <span class="user-badge">Tenant</span>
                </div>
                <p class="testimonial-text">"Found my dream PG through PG Spotter! The location filter and detailed photos helped me make the right choice. The place is exactly as advertised, and I couldn't be happier with my decision."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">SP</div>
                    <div class="author-info">
                        <h4>Sarah Patel</h4>
                        <p>Software Engineer</p>
                    </div>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="testimonial-header">
                    <div class="testimonial-rating">★★★★★</div>
                    <span class="user-badge owner-badge">Owner</span>
                </div>
                <p class="testimonial-text">"As a PG owner, listing my property was incredibly easy. The platform helped me reach genuine tenants, and the inquiry system made communication smooth. Highly recommend for both owners and tenants!"</p>
                <div class="testimonial-author">
                    <div class="author-avatar">RK</div>
                    <div class="author-info">
                        <h4>Rajesh Kumar</h4>
                        <p>PG Owner</p>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="testimonial-header">
                    <div class="testimonial-rating">★★★★★</div>
                    <span class="user-badge">Student</span>
                </div>
                <p class="testimonial-text">"The map feature made it so easy to find PGs near my college. I could instantly see all options in my preferred area. The detailed amenities list and verified reviews gave me complete confidence in my choice."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">PS</div>
                    <div class="author-info">
                        <h4>Priya Sharma</h4>
                        <p>Student</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map initialization handled above (Google Maps preferred, Leaflet fallback) -->

<?php
// Review system implementation
function addReview($listingId, $reviewData) {
    // Code to add review to the database
}

function getReviews($listingId) {
    // Code to fetch reviews from the database
}

// Event listener for view count increment
if (isset($_GET['tenant_id'])) {
    $tenantId = $_GET['tenant_id'];
    if ($tenantId == 'tenant') {
        // Code to increment view count
    }
}

// Include footer
include 'includes/footer.php';
?>