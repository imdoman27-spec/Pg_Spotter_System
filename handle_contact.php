<?php
/*
 * Contact Form Handler
 * Receives data from contact form. Ideally, this would send an email,
 * but for a college project we'll redirect to a success page
 * or save to database. For now we'll give a simple success message.
*/

session_start();
include 'includes/config.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Basic Validation
    if (empty($_POST['email']) || empty($_POST['full_name']) || empty($_POST['message'])) {
        // If AJAX request, return JSON
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
            exit;
        }

        $_SESSION['error_message'] = "Please fill in all contact form fields.";
        header("Location: contact.php");
        exit;
    }

    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message_body = htmlspecialchars($_POST['message']);

    try {
        // Create table if it doesn't exist
        $createSql = "CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            subject VARCHAR(255) DEFAULT NULL,
            message TEXT NOT NULL,
            status VARCHAR(50) DEFAULT 'new',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $conn->exec($createSql);

        // Insert message
        $insertSql = "INSERT INTO contact_messages (full_name, email, subject, message) VALUES (:full_name, :email, :subject, :message)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':subject', $subject);
        $stmt->bindParam(':message', $message_body);
        $stmt->execute();

        // Respond based on request type
        $successMsg = "Thank you, " . $full_name . "! Your message has been received and we will get back to you shortly.";
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $successMsg]);
            exit;
        }

        $_SESSION['success_message'] = $successMsg;
        header("Location: contact.php");
        exit();

    } catch (PDOException $e) {
        // Log the error and respond
        error_log('Contact Form DB Error: ' . $e->getMessage());
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Server error. Please try again later.']);
            exit;
        }
        $_SESSION['error_message'] = 'Server error. Please try again later.';
        header("Location: contact.php");
        exit();
    }

} else {
    header("Location: index.php");
    exit();
}
?>