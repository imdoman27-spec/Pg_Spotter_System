<?php
include 'includes/config.php'; 
session_start();

// If redirect parameter exists in URL, store it in session
$redirect_url_from_get = $_GET['redirect'] ?? ''; 
if (!empty($redirect_url_from_get)) {
    // Store in session so handle_login.php can use it
    $_SESSION['redirect_after_login'] = $redirect_url_from_get;
}

// Header ko include karein
include 'includes/header.php';
?>

<div class="auth-wrapper container">

    <div class="auth-container">

        <div class="auth-header">
            <h2>Welcome Back!</h2>
            <p>Login to your PG Spotter account.</p>
        </div>

        <?php
            // Error Message Display
            if (isset($_SESSION['error_message'])) {
                echo '<div class="message error-message">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']);
            }
        ?>
        <form action="handle_login.php" method="POST" class="auth-form">

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="form-group" style="text-align: right;">
                <a href="forgot_password.php" class="switch-auth">Forgot Password?</a>
            </div>

            <div class="form-group">
                <button type="submit" class="btn submit-btn auth-btn">Login</button>
            </div>

            <div class="auth-footer">
                <p>OR</p>
                <button type="button" class="btn social-btn google-btn" onclick="handleGoogleLogin()">Continue with Google</button>
                <p class="switch-auth">
                    Don't have an account? <a href="signup.php">Sign Up</a>
                </p>
            </div>

        </form>
    </div>
</div>

<script>
function handleGoogleLogin() {
    // Show alert for now - can integrate with Google OAuth later
    alert('Google Login feature coming soon! Please use email login for now.');
    // In future, replace with actual Google OAuth implementation
    // window.location.href = 'https://accounts.google.com/o/oauth2/v2/auth?...';
}
</script>

<?php
include 'includes/footer.php';
?>