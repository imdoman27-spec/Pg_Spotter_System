<?php
session_start();
include 'includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_loggedin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to submit a review']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pg_id = isset($_POST['pg_id']) ? (int)$_POST['pg_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $user_id = $_SESSION['user_id'];

    // Validate inputs
    if ($pg_id <= 0 || $rating < 1 || $rating > 5 || empty($comment)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
        exit;
    }

    try {
        // Check if user has already reviewed this PG
        $check_sql = "SELECT review_id FROM reviews WHERE pg_id = :pg_id AND user_id = :user_id";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
        $check_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            // Update existing review
            $update_sql = "UPDATE reviews SET rating = :rating, comment = :comment, updated_at = NOW() 
                          WHERE pg_id = :pg_id AND user_id = :user_id";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $update_stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $update_stmt->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
            $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $update_stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Review updated successfully']);
        } else {
            // Insert new review
            $insert_sql = "INSERT INTO reviews (pg_id, user_id, rating, comment) 
                          VALUES (:pg_id, :user_id, :rating, :comment)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bindParam(':pg_id', $pg_id, PDO::PARAM_INT);
            $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $insert_stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $insert_stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $insert_stmt->execute();
            echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed']);
?>
