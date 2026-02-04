<?php
/**
 * Password Reset System - Test & Verification Script
 * Run this script to verify all components are working correctly
 */

session_start();
require_once __DIR__ . '/includes/config.php';

$results = [];
$errors = [];

// Test 1: Database Connection
try {
    $test_conn = new PDO("mysql:host=localhost;dbname=pgspotter_db", 'root', '');
    $test_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $results[] = "✅ Database connection successful";
} catch (Exception $e) {
    $errors[] = "❌ Database connection failed: " . $e->getMessage();
}

// Test 2: Check if reset_token columns exist
try {
    $sql = "DESCRIBE users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $has_reset_token = false;
    $has_reset_expiry = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'reset_token') $has_reset_token = true;
        if ($col['Field'] === 'reset_token_expiry') $has_reset_expiry = true;
    }
    
    if ($has_reset_token && $has_reset_expiry) {
        $results[] = "✅ Password reset columns exist in users table";
    } else {
        $errors[] = "❌ Missing reset_token or reset_token_expiry columns. Run the SQL migration.";
    }
} catch (Exception $e) {
    $errors[] = "❌ Error checking database schema: " . $e->getMessage();
}

// Test 3: Check if files exist
$files_to_check = [
    'handle_forgot_password.php',
    'reset_password.php',
    'forgot_password.php',
    'login.php'
];

foreach ($files_to_check as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        $results[] = "✅ File exists: $file";
    } else {
        $errors[] = "❌ File missing: $file";
    }
}

// Test 4: Email Configuration
if (defined('EMAIL_FROM_ADDRESS')) {
    if (!empty(EMAIL_FROM_ADDRESS)) {
        $results[] = "✅ Email configuration defined: " . EMAIL_FROM_ADDRESS;
    } else {
        $results[] = "⚠️  Email FROM address is empty (using default mail)";
    }
} else {
    $errors[] = "❌ Email configuration not defined";
}

// Test 5: Test Database Query (get sample user)
try {
    $sql = "SELECT user_id, email, full_name FROM users LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $results[] = "✅ Can query users table. Sample user: {$user['email']}";
    } else {
        $errors[] = "❌ No users found in database";
    }
} catch (Exception $e) {
    $errors[] = "❌ Error querying users: " . $e->getMessage();
}

// Test 6: Check PHP functions
$php_functions = [
    'mail' => 'mail() - for sending emails',
    'password_hash' => 'password_hash() - for hashing passwords',
    'random_bytes' => 'random_bytes() - for generating tokens',
    'strtotime' => 'strtotime() - for date/time handling'
];

foreach ($php_functions as $func => $desc) {
    if (function_exists($func)) {
        $results[] = "✅ PHP function available: $desc";
    } else {
        $errors[] = "❌ PHP function missing: $desc";
    }
}

// Test 7: Check file permissions
$writable_files = [
    'handle_forgot_password.php',
    'reset_password.php'
];

foreach ($writable_files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path) && is_readable($path)) {
        $results[] = "✅ File is readable: $file";
    } else {
        $errors[] = "❌ File permission issue: $file";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset System - Test Results</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 30px;
        }
        
        .test-section {
            margin-bottom: 30px;
        }
        
        .test-section h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        
        .result-item {
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 4px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .result-success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }
        
        .result-error {
            background-color: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }
        
        .result-warning {
            background-color: #fff3e0;
            color: #e65100;
            border-left: 4px solid #ff9800;
        }
        
        .summary {
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
            background: #f5f5f5;
        }
        
        .summary h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .summary-item {
            padding: 8px 0;
            font-size: 16px;
        }
        
        .summary-item strong {
            color: #333;
        }
        
        .status-ok {
            color: #4caf50;
        }
        
        .status-issue {
            color: #f44336;
        }
        
        .action-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .action-buttons a, .action-buttons button {
            padding: 10px 20px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transition: transform 0.2s;
        }
        
        .action-buttons a:hover, .action-buttons button:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Password Reset System</h1>
            <p>Test & Verification Report</p>
        </div>
        
        <div class="content">
            <div class="test-section">
                <h2>✅ Successful Checks</h2>
                <?php foreach ($results as $result): ?>
                    <div class="result-item result-success">
                        <?php echo htmlspecialchars($result); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="test-section">
                    <h2>❌ Issues Found</h2>
                    <?php foreach ($errors as $error): ?>
                        <div class="result-item result-error">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="summary">
                <h2>📊 Summary</h2>
                <div class="summary-item">
                    <strong>Total Checks:</strong> <?php echo count($results) + count($errors); ?>
                </div>
                <div class="summary-item">
                    <strong>Passed:</strong> <span class="status-ok"><?php echo count($results); ?></span>
                </div>
                <div class="summary-item">
                    <strong>Failed:</strong> <span class="status-issue"><?php echo count($errors); ?></span>
                </div>
                <div class="summary-item" style="margin-top: 15px;">
                    <strong>Status:</strong> 
                    <?php if (empty($errors)): ?>
                        <span class="status-ok">✅ All systems operational!</span>
                    <?php else: ?>
                        <span class="status-issue">❌ Some issues need attention</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="forgot_password.php">🔗 Test Forgot Password</a>
                <a href="login.php">📝 Test Login</a>
                <a href="PASSWORD_RESET_SETUP.md">📚 Setup Guide</a>
                <button onclick="window.location.reload()">🔄 Refresh Tests</button>
            </div>
        </div>
    </div>
</body>
</html>
