<?php
/**
 * Password Reset Handler
 * Handles forgot password requests and sends reset emails
 */

require_once __DIR__ . '/includes/config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

header('Content-Type: application/json');

try {
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get and validate email
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if (empty($email)) {
        throw new Exception('Email address is required');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }

    // Check if user exists in database
    try {
        $sql = "SELECT user_id, full_name, email FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // For security, don't reveal if email exists
            echo json_encode([
                'success' => true,
                'message' => 'If this email exists, you will receive a password reset link shortly.'
            ]);
            exit;
        }

        // Generate reset token
        $reset_token = bin2hex(random_bytes(32));
        $token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store reset token in database
        $update_sql = "UPDATE users SET reset_token = :token, reset_token_expiry = :expiry WHERE user_id = :user_id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->execute([
            'token' => $reset_token,
            'expiry' => $token_expiry,
            'user_id' => $user['user_id']
        ]);

        // Create reset link
        $reset_link = BASE_URL . 'reset_password.php?token=' . $reset_token;

        // Send email (using basic mail for now)
        $to = $user['email'];
        $subject = 'PG Spotter - Password Reset Request';
        $message = "
            <html>
                <head>
                    <title>Password Reset</title>
                </head>
                <body>
                    <h2>Password Reset Request</h2>
                    <p>Hi " . htmlspecialchars($user['full_name']) . ",</p>
                    <p>You requested a password reset. Click the link below to reset your password:</p>
                    <p><a href='" . htmlspecialchars($reset_link) . "'>" . htmlspecialchars($reset_link) . "</a></p>
                    <p>This link will expire in 1 hour.</p>
                    <p>If you didn't request this, please ignore this email.</p>
                    <p>Best regards,<br>PG Spotter Team</p>
                </body>
            </html>
        ";

        // Set headers for HTML email
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $headers .= "From: noreply@pgspotter.com\r\n";

        // Send email
        mail($to, $subject, $message, $headers);

        echo json_encode([
            'success' => true,
            'message' => 'Password reset link has been sent to your email. Please check your inbox.'
        ]);

    } catch (PDOException $e) {
        error_log('Database error in forgot password: ' . $e->getMessage());
        throw new Exception('Database error occurred');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
