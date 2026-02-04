<?php 
// 1. Include config file for database connection and BASE_URL (CRITICAL FIX)
include 'includes/config.php'; 

// 2. Start session
session_start();

// 3. Include header
include 'includes/header.php'; 
?>

<div class="auth-wrapper container">
    
    <div class="auth-container">
        
        <div class="auth-header">
            <h2>Join PG Spotter Today!</h2>
            <p>Create an account to list your PG or save your favorite properties.</p>
        </div>

        <?php
            // Check if there is any error message in session
            if (isset($_SESSION['error_message'])) {
                echo '<div class="message error-message">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']);
            }
        ?>
        <form action="handle_signup.php" method="POST" class="auth-form">

            <div class="form-group">
                <label>I am a...</label>
                <div class="role-selector">
                    <label class="role-label">
                        <input type="radio" name="user_type" value="owner" required>
                        <span>PG Owner</span>
                    </label>
                    <label class="role-label">
                        <input type="radio" name="user_type" value="tenant" required>
                        <span>Tenant/Seeker</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
                </div>
            </div>

            <div class="form-group terms-group">
                <label>
                    <input type="checkbox" id="terms" name="terms" required>
                    I agree to the PG Spotter <a href="terms.php" target="_blank">Terms and Conditions</a>.
                </label>
            </div>

            <div class="form-group">
                <button type="submit" class="btn submit-btn auth-btn">Sign Up</button>
            </div>

            <div class="auth-footer">
                <p>OR</p>
                <button type="button" class="btn social-btn google-btn" onclick="handleGoogleLogin()">Continue with Google</button>
                <p class="switch-auth">
                    Already have an account? <a href="login.php">Login</a>
                </p>
            </div>

        </form>
    </div>
</div>

<script>
function handleGoogleLogin() {
    alert('Google Login feature coming soon! Please use email signup for now.');
}
</script>

<?php 
// Include footer
include 'includes/footer.php'; 
?>