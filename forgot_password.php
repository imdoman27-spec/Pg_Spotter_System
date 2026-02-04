<?php 
// Include configuration first
require_once __DIR__ . '/includes/config.php';
// Step 1: Header ko include karein
include 'includes/header.php'; 
?>

<div class="auth-wrapper container">
    
    <div class="auth-container">
        
        <div class="auth-header">
            <h2>Forgot Your Password?</h2>
            <p>Enter your email address and we'll send you a reset link.</p>
        </div>

        <form action="handle_forgot_password.php" method="POST" class="auth-form">

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn submit-btn auth-btn">Send Reset Link</button>
            </div>

            <div class="auth-footer">
                <p class="switch-auth">
                    Remembered your password? <a href="login.php">Login</a>
                </p>
            </div>

        </form>
    </div>
</div>
<?php 
// Step 2: Footer ko include karein
include 'includes/footer.php'; 
?>