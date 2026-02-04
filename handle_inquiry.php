<?php
/*
 * Inquiry Form Handler
 * This file receives inquiries from PG Details page and saves them to database.
*/

// --- START: Error Reporting for Debugging (REMOVE LATER) ---
ini_set('display_errors', 1);
error_reporting(E_ALL);
// --- END: Error Reporting ---

// 1. Include database connection and start session
include 'includes/config.php';
session_start(); 

// 2. Security Check: User Logged In and is 'Tenant'
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
    // If not logged in, redirect to login page
    $_SESSION['error_message'] = "You must be logged in to send an inquiry.";
    header("Location: login.php"); 
    exit;
}
if ($_SESSION['user_type'] != 'tenant') {
    // If Owner, redirect to their dashboard
    $_SESSION['error_message'] = "Only tenants can send inquiries.";
    header("Location: dashboards/owner_dashboard.php"); 
    exit;
}

// 3. Check form submission method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Receive data from form
    $pg_id = $_POST['pg_id'];
    $tenant_user_id = $_SESSION['user_id'];
    $message = $_POST['message'] ?? ''; // Message is optional

    // Validation
    if (empty($pg_id) || !is_numeric($pg_id)) {
        $_SESSION['error_message'] = "Invalid PG Listing ID.";
        header("Location: pg_details.php?id=" . $pg_id); 
        exit;
    }

    try {
        // SQL query to insert into inquiries table
        $sql = "INSERT INTO inquiries (pg_id, tenant_user_id, message) 
                VALUES (:pg_id, :tenant_user_id, :message)";
                 
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
        $stmt->bindParam(':tenant_user_id', $tenant_user_id, PDO::PARAM_INT);
        $stmt->bindParam(':message', $message);
        
        // Execute query
        $stmt->execute();
        
        // --- Success ---
        // Store success message and redirect to Tenant Dashboard
        $_SESSION['success_message'] = "Your inquiry has been successfully sent to the PG owner!";
        header("Location: dashboards/tenant_dashboard.php");
        exit();

    } catch(PDOException $e) {
        // If database error occurs, redirect back to details page
        $_SESSION['error_message'] = "Inquiry failed due to server error.";
        error_log("Inquiry Error: " . $e->getMessage()); 
        header("Location: pg_details.php?id=" . $pg_id); 
        exit();
    }

} else {
    // If direct access, redirect to homepage
    header("Location: index.php");
    exit();
}
?>