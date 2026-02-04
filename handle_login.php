<?php
/*
 * Login Form Handler
 * Handles login, session setup, and redirects user based on user_type (including Admin).
*/

include 'includes/config.php';
session_start();

// Check karein ki form POST method se submit hua hai ya nahi
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. Basic Validation
    if (empty($email) || empty($password)) {
        $_SESSION['error_message'] = "Please enter both email and password.";
        header("Location: login.php");
        exit();
    }

    try {
        // 2. Fetch user data
        $sql = "SELECT user_id, full_name, password, user_type, profile_pic FROM users WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            
            // 3. Password Match! Store user info in session
            $_SESSION['user_loggedin'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_type'] = $user['user_type']; 
            $_SESSION['profile_pic'] = $user['profile_pic'] ?? ''; 

            // 4. --- REDIRECTION LOGIC (Handle Admin, Owner, Tenant) ---
            
            $redirect_url = '';

            if ($user['user_type'] == 'admin') {
                $redirect_url = 'dashboards/admin_dashboard.php'; // Admin Dashboard
            } elseif ($user['user_type'] == 'owner') {
                $redirect_url = 'dashboards/owner_dashboard.php'; // Owner Dashboard
            } else { // tenant
                $redirect_url = 'dashboards/tenant_dashboard.php'; // Tenant Dashboard
            }

            // Check if a specific page was saved in the session (e.g., from pg_details.php)
            if (isset($_SESSION['redirect_after_login']) && !empty($_SESSION['redirect_after_login'])) {
                $redirect_url = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']); // Clear the saved URL
            } 
            
            // Final Redirect
            header("Location: " . $redirect_url);
            exit();

        } else {
            // Invalid credentials
            $_SESSION['error_message'] = "Incorrect email or password.";
            header("Location: login.php");
            exit();
        }

    } catch(PDOException $e) {
        // Log the error for debugging (you can remove the error_log line later)
        // error_log("Login Error: " . $e->getMessage()); 
        $_SESSION['error_message'] = "Login failed due to a server error."; 
        header("Location: login.php");
        exit();
    } // This '}' closes the try...catch block

} else {
    // If accessed directly without POST
    header("Location: index.php");
    exit();
} // This '}' closes the initial