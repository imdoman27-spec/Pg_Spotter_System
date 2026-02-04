<?php
/*
 * Signup Form Handler
 * This file receives data from 'signup.php' form.
*/

// 1. Include database connection file
include 'includes/config.php';

// 2. Start PHP session (required to maintain user login state)
session_start();

// 3. Check if form is submitted via POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 4. Store form data in variables
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_type = $_POST['user_type']; // 'owner' or 'tenant'

    // 5. Validation (Data ko check karna)
    
    // Check karein ki password aur confirm password match karte hain ya nahi
    if ($password !== $confirm_password) {
        // Agar match nahi karte, toh error message set karein aur waapis signup page par bhej dein
        $_SESSION['error_message'] = "Passwords do not match!";
        header("Location: signup.php");
        exit(); // Code ko yahin rok dein
    }

    // Check karein ki email pehle se database mein hai ya nahi
    try {
        $sql_check = "SELECT user_id FROM users WHERE email = :email";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            // If email is already registered
            $_SESSION['error_message'] = "Email address is already registered!";
            header("Location: signup.php");
            exit();
        }

    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: signup.php");
        exit();
    }


    // 6. Hash the password (Most important for security)
    // We cannot save passwords in plain text
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    
    // 7. Insert new user into database
    try {
        $sql_insert = "INSERT INTO users (full_name, email, password, user_type) 
                       VALUES (:full_name, :email, :password, :user_type)";
        
        $stmt_insert = $conn->prepare($sql_insert);

        // Bind values
        $stmt_insert->bindParam(':full_name', $full_name);
        $stmt_insert->bindParam(':email', $email);
        $stmt_insert->bindParam(':password', $hashed_password); // Save hashed password
        $stmt_insert->bindParam(':user_type', $user_type);

        // Execute query
        $stmt_insert->execute();

        // If successfully registered
        $_SESSION['success_message'] = "Registration successful! Please login.";
        header("Location: login.php"); // Redirect user to login page
        exit();

    } catch(PDOException $e) {
        // If error occurs during insert
        $_SESSION['error_message'] = "Registration failed: " . $e->getMessage();
        header("Location: signup.php");
        exit();
    }

} else {
    // If user tries to access this file directly via URL
    // Redirect them to homepage
    header("Location: index.php");
    exit();
}
?>