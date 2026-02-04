<?php 
// 1. Include config file for Database connection and BASE_URL
include 'includes/config.php'; 

// 2. Start the session
session_start();

// 3. Include the header
include 'includes/header.php'; 
?>

<main class="contact-page-modern">
    <div class="container">
        
        <!-- Hero Header -->
        <div class="contact-hero">
            <h1>Get In Touch</h1>
            <p>We're here to help! Reach out for support, inquiries, or feedback.</p>
        </div>

        <div class="contact-wrapper">
            
            <!-- Contact Form Section -->
            <div class="contact-form-section">
                <div class="form-header">
                    <h2>📨 Send Us a Message</h2>
                    <p>Fill out the form below and we'll get back to you within 24 hours</p>
                </div>
                
                <form id="contactForm" action="handle_contact.php" method="POST" class="modern-contact-form">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="What is this regarding?" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Type your message here..." rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit-modern">
                        <span>Send Message</span>
                        <span>✉️</span>
                    </button>
                </form>
            </div>

            <!-- Contact Information Cards -->
            <div class="contact-info-section">
                <div class="info-header">
                    <h2>💬 Other Ways to Connect</h2>
                </div>

                <div class="contact-info-card">
                    <div class="info-icon phone-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h3>Phone</h3>
                        <p class="info-value">+91 123-456-7890</p>
                        <span class="info-detail">Mon-Fri: 9 AM - 6 PM IST</span>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="info-icon email-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h3>Email</h3>
                        <p class="info-value">support@pgspotter.com</p>
                        <span class="info-detail">Response within 24 hours</span>
                    </div>
                </div>

                <div class="contact-info-card">
                    <div class="info-icon location-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Location</h3>
                        <p class="info-value">123 PG Lane, Tech Park</p>
                        <span class="info-detail">Raipur, CG 492001</span>
                    </div>
                </div>

                <div class="social-links-card">
                    <h3>Follow Us</h3>
                    <div class="social-icons">
                        <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<?php 
// Include the footer
include 'includes/footer.php'; 
?>