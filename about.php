<?php
// 1. Include config file for database connection and BASE_URL (CRITICAL FIX)
include 'includes/config.php'; 

// 2. Start session (if using session variables)
session_start();

// 3. Include header
include 'includes/header.php';
?>

<div class="about-page-modern">
    <div class="container">
        
        <!-- Hero Section -->
        <div class="about-hero">
            <div class="hero-content">
                <h1>About PG Spotter</h1>
                <p class="hero-subtitle">Your Trusted Partner in Finding and Listing PGs</p>
                <p class="hero-description">Connecting quality accommodations with the perfect tenants since 2024</p>
            </div>
        </div>

        <!-- Mission Section -->
        <div class="about-mission-section">
            <div class="mission-card">
                <div class="mission-icon">🎯</div>
                <h2>Our Mission</h2>
                <p>To simplify the search for quality PG accommodations and empower owners to easily connect with tenants, creating a seamless and trustworthy experience for everyone.</p>
            </div>
        </div>

        <!-- Story Section -->
        <div class="about-story-section">
            <div class="story-content">
                <h2>Our Story</h2>
                <p>Founded with a vision to revolutionize the PG accommodation industry, PG Spotter has grown from a simple idea to a trusted platform serving thousands of students, professionals, and property owners across India.</p>
                <p>We understand the challenges faced by both tenants searching for the perfect accommodation and owners looking to connect with reliable tenants. Our platform bridges this gap, making the entire process smooth, transparent, and efficient.</p>
            </div>
            <div class="story-image">
                <div class="story-placeholder">
                    <span style="font-size: 80px;">🏠</span>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="about-features-section">
            <h2 class="section-title">Why Choose PG Spotter?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">✅</div>
                    <h3>Verified Listings</h3>
                    <p>We ensure all listings are verified for your safety and convenience. Every property undergoes thorough verification.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Community Focus</h3>
                    <p>Connecting you with a community, not just a room. Build lasting relationships with fellow tenants.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🛡️</div>
                    <h3>Dedicated Support</h3>
                    <p>Our team is always here to help you, 24/7. Get instant assistance whenever you need it.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Quick & Easy</h3>
                    <p>Find your perfect PG in minutes with our intuitive search and filter system.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Best Prices</h3>
                    <p>Compare prices across multiple properties to find the best deal that fits your budget.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Mobile Friendly</h3>
                    <p>Access our platform anytime, anywhere on any device for seamless browsing.</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="about-cta-section">
            <div class="cta-content">
                <h2>Ready to Get Started?</h2>
                <p>Find your next PG or list your property today!</p>
                <div class="cta-buttons">
                    <a href="search.php" class="btn-cta btn-primary">🔍 Find a PG</a>
                    <a href="list_pg.php" class="btn-cta btn-secondary">➕ List Your PG</a>
                </div>
            </div>
        </div>

    </div>
</div>
<?php 
// Step 4: Include footer
include 'includes/footer.php'; 
?>