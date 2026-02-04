<?php
/**
 * Reset Password Page
 * Allows users to set a new password after clicking the reset link
 */

session_start();

require_once __DIR__ . '/includes/config.php';

$error = '';
$success = '';
$token = isset($_GET['token']) ? trim($_GET['token']) : '';

// Validate token format
if (empty($token) || !ctype_xdigit($token)) {
    $error = 'Invalid password reset link.';
} else {
    // Check if token is valid and not expired
    try {
        $sql = "SELECT user_id FROM users WHERE reset_token = :token AND reset_token_expiry > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['token' => $token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $error = 'This password reset link has expired or is invalid. Please request a new one.';
        }
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        $error = 'An error occurred. Please try again later.';
    }
}

// Handle password reset submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    if (empty($new_password) || empty($confirm_password)) {
        $error = 'Please fill in all password fields.';
    } elseif (strlen($new_password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            // Hash password
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // Update password and clear reset token
            $update_sql = "UPDATE users SET password = :password, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = :token";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->execute([
                'password' => $hashed_password,
                'token' => $token
            ]);

            $success = 'Password reset successfully! Redirecting to login...';
            header('refresh:2;url=login.php');

        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            $error = 'An error occurred while resetting password. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - PG Spotter</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .reset-container {
            max-width: 400px;
            margin: 60px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .reset-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 10px;
        }

        .reset-container p {
            text-align: center;
            color: #666;
            margin-bottom: 25px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.1);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .error-message {
            background-color: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }

        .success-message {
            background-color: #efe;
            color: #3c3;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #3c3;
        }

        .back-link {
            text-align: center;
            margin-top: 15px;
        }

        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .password-strength {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/includes/header.php'; ?>

    <div class="reset-container">
        <h1>Reset Your Password</h1>
        <p>Enter your new password below</p>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (empty($error) && empty($success)): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <div class="password-strength">Minimum 6 characters</div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <button type="submit" class="btn">Reset Password</button>
            </form>

            <div class="back-link">
                <a href="login.php">Back to Login</a>
            </div>
        <?php endif; ?>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
