<?php
session_start();
include 'includes/config.php'; 

// Security Check: Must be logged in as a Tenant
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_type'] != 'tenant') {
    $_SESSION['error_message'] = "Please login as a Tenant to manage favorites.";
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// 1. Data Validation
if (!isset($_GET['pg_id']) || !is_numeric($_GET['pg_id']) || !isset($_GET['action'])) {
    header("Location: index.php"); // Or handle error
    exit;
}

$pg_id = (int)$_GET['pg_id'];
$action = $_GET['action']; // 'add' or 'remove'
$redirect_url = "pg_details.php?id=" . $pg_id;

try {
    if ($action == 'add') {
        // Insert (Add to favorites)
        $sql = "INSERT INTO favorites (user_id, pg_id) VALUES (:user_id, :pg_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['success_message'] = "PG added to your favorites!";
        
    } elseif ($action == 'remove') {
        // Delete (Remove from favorites)
        $sql = "DELETE FROM favorites WHERE user_id = :user_id AND pg_id = :pg_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['success_message'] = "PG removed from your favorites.";
        
    } else {
        $_SESSION['error_message'] = "Invalid action specified.";
    }

} catch (PDOException $e) {
    // Ignore unique key constraint violation
    if (strpos($e->getMessage(), 'unique_favorite') !== false) {
        $_SESSION['error_message'] = "This PG is already in your favorites.";
    } else {
        $_SESSION['error_message'] = "Server error while updating favorites.";
    }
}

header("Location: " . $redirect_url);
exit();
?>