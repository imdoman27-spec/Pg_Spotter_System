<?php
// Add this to your handle_list_pg.php file where photos are processed

// Get the primary photo selection
$primary_photo = $_POST['primary_photo'] ?? null;

// Handle photo uploads
if (isset($_FILES['pg_photos']) && !empty($_FILES['pg_photos']['name'][0])) {
    $upload_dir = 'uploads/pg_photos/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // First, set all existing photos as non-primary for this PG
    if ($new_pg_id) {
        $sql_reset = "UPDATE pg_photos SET is_primary = FALSE WHERE pg_id = :pg_id";
        $stmt_reset = $conn->prepare($sql_reset);
        $stmt_reset->execute(['pg_id' => $new_pg_id]);
    }

    // Prepare photo insertion statement
    $sql_photo = "INSERT INTO pg_photos (pg_id, photo_path, is_primary) VALUES (:pg_id, :photo_path, :is_primary)";
    $stmt_photo = $conn->prepare($sql_photo);

    foreach ($_FILES['pg_photos']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['pg_photos']['error'][$key] === UPLOAD_ERR_OK) {
            $file_type = $_FILES['pg_photos']['type'][$key];
            $file_size = $_FILES['pg_photos']['size'][$key];
            
            // Validate file
            if (!in_array($file_type, $allowed_types)) {
                continue; // Skip invalid file types
            }
            if ($file_size > $max_size) {
                continue; // Skip files that are too large
            }
            
            // Generate unique filename
            $file_extension = pathinfo($_FILES['pg_photos']['name'][$key], PATHINFO_EXTENSION);
            $unique_filename = uniqid('pg_', true) . '.' . $file_extension;
            $upload_path = $upload_dir . $unique_filename;
            
            // Move file and insert record
            if (move_uploaded_file($tmp_name, $upload_path)) {
                // Check if this should be the primary photo
                $is_primary = ($primary_photo === null && $key === 0) || 
                            $unique_filename === $primary_photo;
                
                $stmt_photo->execute([
                    'pg_id' => $new_pg_id,
                    'photo_path' => $unique_filename,
                    'is_primary' => $is_primary
                ]);
            }
        }
    }
}

// Update primary photo selection for existing photos
if ($primary_photo && $new_pg_id) {
    // First, reset all photos to non-primary
    $sql_reset = "UPDATE pg_photos SET is_primary = FALSE WHERE pg_id = :pg_id";
    $stmt_reset = $conn->prepare($sql_reset);
    $stmt_reset->execute(['pg_id' => $new_pg_id]);
    
    // Then set the selected photo as primary
    $sql_update = "UPDATE pg_photos SET is_primary = TRUE 
                   WHERE pg_id = :pg_id AND photo_path = :photo_path";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->execute([
        'pg_id' => $new_pg_id,
        'photo_path' => $primary_photo
    ]);
}
?>