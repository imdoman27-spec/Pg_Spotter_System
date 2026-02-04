<?php
session_start();
include 'includes/config.php';

// Check if request is AJAX
if (!isset($_GET['pg_id']) || !is_numeric($_GET['pg_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid PG ID']);
    exit;
}

$pg_id = (int)$_GET['pg_id'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$ip_address = $_SERVER['REMOTE_ADDR'];

try {
    // Insert view record
    $sql = "INSERT INTO pg_views (pg_id, user_id, ip_address) 
            VALUES (:pg_id, :user_id, :ip_address)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
    $stmt->execute();

    // Update view count in pg_listings
    $update_sql = "UPDATE pg_listings SET view_count = view_count + 1 WHERE pg_id = :pg_id";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
    $update_stmt->execute();

    // Get updated view count
    $count_sql = "SELECT view_count FROM pg_listings WHERE pg_id = :pg_id";
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
    $count_stmt->execute();
    $result = $count_stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'view_count' => $result['view_count'] ?? 0]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
