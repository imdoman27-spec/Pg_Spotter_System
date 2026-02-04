<?php
/**
 * PG Listing Form Handler
 * 
 * @description Processes PG listing form submissions for both new listings (INSERT) 
 * and updates to existing listings (UPDATE). Handles data validation, file uploads,
 * and database operations.
 * 
 * @author Your Name
 * @version 1.0
 */

// Configure error reporting for development environment
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Initialize required resources
include 'includes/config.php';  // Database connection
session_start();               // Start user session

// Ensure required tables exist and have correct structure
try {
    // Check amenities master table
    $check_amenities_master = $conn->query("SHOW TABLES LIKE 'amenities'");
    if ($check_amenities_master->rowCount() == 0) {
        // Create amenities master table
        $conn->exec("CREATE TABLE amenities (
            amenity_id INT AUTO_INCREMENT PRIMARY KEY,
            amenity_name VARCHAR(50) NOT NULL UNIQUE
        )");
        
        // Populate with default amenities
        $default_amenities = [
            'wifi', 'food', 'laundry', 'housekeeping',  // Services
            'ac', 'parking', 'geyser', 'cctv', 'power_backup', 'attached_bathroom'  // General Amenities
        ];
        
        $insert_amenity = $conn->prepare("INSERT INTO amenities (amenity_name) VALUES (:name)");
        foreach ($default_amenities as $amenity) {
            $insert_amenity->execute(['name' => $amenity]);
        }
    }

    // Check pg_amenities linking table
    $check_pg_amenities = $conn->query("SHOW TABLES LIKE 'pg_amenities'");
    if ($check_pg_amenities->rowCount() == 0) {
        $conn->exec("CREATE TABLE pg_amenities (
            pg_amenity_id INT AUTO_INCREMENT PRIMARY KEY,
            pg_id INT NOT NULL,
            amenity_id INT NOT NULL,
            FOREIGN KEY (pg_id) REFERENCES pg_listings(pg_id) ON DELETE CASCADE,
            FOREIGN KEY (amenity_id) REFERENCES amenities(amenity_id) ON DELETE CASCADE,
            UNIQUE KEY unique_pg_amenity (pg_id, amenity_id)
        )");
    }

    // Check pg_photos table
    $check_photos = $conn->query("SHOW TABLES LIKE 'pg_photos'");
    if ($check_photos->rowCount() == 0) {
        $conn->exec("CREATE TABLE pg_photos (
            photo_id INT AUTO_INCREMENT PRIMARY KEY,
            pg_id INT NOT NULL,
            photo_path VARCHAR(255) NOT NULL,
            is_primary BOOLEAN DEFAULT FALSE,
            upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (pg_id) REFERENCES pg_listings(pg_id) ON DELETE CASCADE
        )");
    }
} catch(PDOException $e) {
    error_log("Database structure error: " . $e->getMessage());
}

// Security Checks
if (!isset($_SESSION['user_loggedin']) || $_SESSION['user_type'] != 'owner') {
    $_SESSION['error_message'] = "Please login to manage listings.";
    header("Location: login.php");
    exit;
}
$owner_id = $_SESSION['user_id'];

// Check for UPDATE mode
$is_update_mode = isset($_POST['edit_mode']) && is_numeric($_POST['edit_mode']);
$pg_id_to_edit = $is_update_mode ? (int)$_POST['edit_mode'] : null;

// Form Submission Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Validation ---
    $required_fields = [
        'pg_name', 'pg_type', 'location', 'address', 'rooms',
        'rent', 'deposit', 'notice_period',
        'owner_name', 'contact_number', 'email', 'terms'
    ];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['error_message'] = "Please fill out all required fields.";
            header("Location: list_pg.php" . ($is_update_mode ? "?edit=$pg_id_to_edit" : ""));
            exit;
        }
    }

    // Assign text data
    $pg_name = $_POST['pg_name'];
    $pg_type = $_POST['pg_type'];
    $location_city = $_POST['location'];
    $address = $_POST['address'];
    $num_rooms = $_POST['rooms'];
    $description = $_POST['description'] ?? '';
    $rent_per_person = $_POST['rent'];
    $security_deposit = $_POST['deposit'];
    $notice_period_days = $_POST['notice_period'];
    $owner_contact_name = $_POST['owner_name'];
    $owner_contact_number = $_POST['contact_number'];
    $owner_email = $_POST['email'];
    $selected_amenities = isset($_POST['amenities']) ? $_POST['amenities'] : [];

    // Email Validation
    if (!filter_var($owner_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Invalid email format.";
        header("Location: list_pg.php" . ($is_update_mode ? "?edit=$pg_id_to_edit" : ""));
        exit;
    }

    // --- Database Transaction ---
    $new_pg_id = $pg_id_to_edit;
    $redirect_url = "dashboards/owner_dashboard.php";

    try {
        $conn->beginTransaction();

        if ($is_update_mode) {
            // STEP A.1: UPDATE pg_listings
            $sql_pg = "UPDATE pg_listings SET 
                       pg_name = :pg_name, pg_type = :pg_type, location_city = :location_city, 
                       address = :address, num_rooms = :num_rooms, description = :description,
                       rent_per_person = :rent_per_person, security_deposit = :security_deposit, 
                       notice_period_days = :notice_period_days, owner_contact_name = :owner_contact_name, 
                       owner_contact_number = :owner_contact_number, owner_email = :owner_email
                       WHERE pg_id = :pg_id AND owner_id = :owner_id";
            $stmt_pg = $conn->prepare($sql_pg);
            $stmt_pg->bindParam(':pg_id', $pg_id_to_edit, PDO::PARAM_INT);
            $stmt_pg->bindParam(':owner_id', $owner_id, PDO::PARAM_INT);
            $message_success = "Listing ID $pg_id_to_edit updated successfully!";

            // STEP B.1: Delete Old Amenities
            $conn->exec("DELETE FROM pg_amenities WHERE pg_id = $pg_id_to_edit");

        } else {
            // STEP A.2: INSERT New pg_listings
            $sql_pg = "INSERT INTO pg_listings (
                owner_id, pg_name, pg_type, location_city, address, num_rooms, description,
                rent_per_person, security_deposit, notice_period_days,
                owner_contact_name, owner_contact_number, owner_email
            ) VALUES (
                :owner_id, :pg_name, :pg_type, :location_city, :address, :num_rooms, :description,
                :rent_per_person, :security_deposit, :notice_period_days,
                :owner_contact_name, :owner_contact_number, :owner_email
            )";
            $stmt_pg = $conn->prepare($sql_pg);
            $stmt_pg->bindParam(':owner_id', $owner_id, PDO::PARAM_INT);
            $message_success = "PG Listing submitted successfully! It's under review.";
        }

        // Bind Common Parameters
        $stmt_pg->bindParam(':pg_name', $pg_name);
        $stmt_pg->bindParam(':pg_type', $pg_type);
        $stmt_pg->bindParam(':location_city', $location_city);
        $stmt_pg->bindParam(':address', $address);
        $stmt_pg->bindParam(':num_rooms', $num_rooms, PDO::PARAM_INT);
        $stmt_pg->bindParam(':description', $description);
        $stmt_pg->bindParam(':rent_per_person', $rent_per_person, PDO::PARAM_INT);
        $stmt_pg->bindParam(':security_deposit', $security_deposit, PDO::PARAM_INT);
        $stmt_pg->bindParam(':notice_period_days', $notice_period_days, PDO::PARAM_INT);
        $stmt_pg->bindParam(':owner_contact_name', $owner_contact_name);
        $stmt_pg->bindParam(':owner_contact_number', $owner_contact_number);
        $stmt_pg->bindParam(':owner_email', $owner_email);

        $stmt_pg->execute();

        // Get new PG ID if inserted
        if (!$is_update_mode) {
            $new_pg_id = $conn->lastInsertId();
        }

        // STEP C: Insert Amenities
        if (!empty($selected_amenities) && $new_pg_id) {
            $sql_amenity = "INSERT INTO pg_amenities (pg_id, amenity_id) 
                            SELECT :pg_id, amenity_id FROM amenities WHERE amenity_name = :amenity_name";
            $stmt_amenity = $conn->prepare($sql_amenity);
            foreach ($selected_amenities as $amenity_name) {
                $stmt_amenity->bindParam(':pg_id', $new_pg_id, PDO::PARAM_INT);
                $stmt_amenity->bindParam(':amenity_name', $amenity_name);
                $stmt_amenity->execute();
            }
        }

        // Get primary photo selections from the form (existing filename or index for new uploads)
        $primary_photo = $_POST['primary_photo'] ?? null; // existing photo filename
        $primary_photo_new = isset($_POST['primary_photo_new']) ? $_POST['primary_photo_new'] : null; // index of new upload

        // STEP D: Handle Photo Uploads
        if (isset($_FILES['pg_photos']) && !empty($_FILES['pg_photos']['name'][0])) {
            $upload_dir = 'uploads/pg_photos/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $max_size = 5 * 1024 * 1024; // 5MB

            // Reset existing primary flag for this PG if any selection is made (we'll re-set below)
            if ($new_pg_id) {
                $resetStmt = $conn->prepare("UPDATE pg_photos SET is_primary = FALSE WHERE pg_id = :pg_id");
                $resetStmt->execute(['pg_id' => $new_pg_id]);
            }

            // Track whether a primary has been set (either existing or new)
            $primarySet = false;

            foreach ($_FILES['pg_photos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['pg_photos']['error'][$key] === UPLOAD_ERR_OK) {
                    $file_type = $_FILES['pg_photos']['type'][$key];
                    $file_size = $_FILES['pg_photos']['size'][$key];

                    // Validate file
                    if (!in_array($file_type, $allowed_types)) {
                        throw new Exception('Invalid file type. Only JPG and PNG allowed.');
                    }
                    if ($file_size > $max_size) {
                        throw new Exception('File too large. Maximum size is 5MB.');
                    }

                    // Generate unique filename
                    $file_extension = pathinfo($_FILES['pg_photos']['name'][$key], PATHINFO_EXTENSION);
                    $unique_filename = uniqid('pg_' . $new_pg_id . '_') . '.' . $file_extension;
                    $destination = $upload_dir . $unique_filename;

                    // Move file and save to database
                    if (move_uploaded_file($tmp_name, $destination)) {
                        // Determine if this uploaded file should be marked primary
                        $is_primary = 0;
                        if ($primary_photo_new !== null && (string)$primary_photo_new === (string)$key) {
                            $is_primary = 1;
                            $primarySet = true;
                        }

                        $sql_photo = "INSERT INTO pg_photos (pg_id, photo_path, is_primary) VALUES (:pg_id, :photo_path, :is_primary)";
                        $stmt_photo = $conn->prepare($sql_photo);
                        $stmt_photo->bindParam(':pg_id', $new_pg_id, PDO::PARAM_INT);
                        $stmt_photo->bindParam(':photo_path', $unique_filename);
                        $stmt_photo->bindParam(':is_primary', $is_primary, PDO::PARAM_INT);
                        $stmt_photo->execute();
                    } else {
                        throw new Exception('Failed to upload file: ' . $_FILES['pg_photos']['name'][$key]);
                    }
                }
            }

            // If user selected an existing photo as primary, set it now (overrides new uploads)
            if ($primary_photo && $new_pg_id) {
                $stmt_update_primary = $conn->prepare("UPDATE pg_photos SET is_primary = TRUE WHERE pg_id = :pg_id AND photo_path = :photo_path");
                $stmt_update_primary->execute(['pg_id' => $new_pg_id, 'photo_path' => $primary_photo]);
                $primarySet = true;
            }

            // If no primary was set at all but there are photos, mark the first photo for this PG as primary
            if (!$primarySet && $new_pg_id) {
                $stmt_first = $conn->prepare("SELECT photo_id FROM pg_photos WHERE pg_id = :pg_id ORDER BY photo_id ASC LIMIT 1");
                $stmt_first->execute(['pg_id' => $new_pg_id]);
                $first = $stmt_first->fetch(PDO::FETCH_ASSOC);
                if ($first) {
                    $stmt_mark = $conn->prepare("UPDATE pg_photos SET is_primary = TRUE WHERE photo_id = :photo_id");
                    $stmt_mark->execute(['photo_id' => $first['photo_id']]);
                }
            }
        }

        // If no files were uploaded but an existing photo was chosen as primary, set it
        if ((!(isset($_FILES['pg_photos']) && !empty($_FILES['pg_photos']['name'][0]))) && $primary_photo && $new_pg_id) {
            $stmt_update_primary = $conn->prepare("UPDATE pg_photos SET is_primary = FALSE WHERE pg_id = :pg_id");
            $stmt_update_primary->execute(['pg_id' => $new_pg_id]);
            $stmt_set = $conn->prepare("UPDATE pg_photos SET is_primary = TRUE WHERE pg_id = :pg_id AND photo_path = :photo_path");
            $stmt_set->execute(['pg_id' => $new_pg_id, 'photo_path' => $primary_photo]);
        }

        // Handle amenities
        if (isset($_POST['amenities']) && is_array($_POST['amenities'])) {
            // First, delete existing amenities if updating
            if ($is_update_mode) {
                $delete_amenities = $conn->prepare("DELETE FROM pg_amenities WHERE pg_id = :pg_id");
                $delete_amenities->execute(['pg_id' => $new_pg_id]);
            }
            
            // Insert new amenities using the amenity IDs
            $sql_amenity = "INSERT INTO pg_amenities (pg_id, amenity_id) 
                           SELECT :pg_id, amenity_id 
                           FROM amenities 
                           WHERE amenity_name = :amenity_name";
            $stmt_amenity = $conn->prepare($sql_amenity);
            
            foreach ($_POST['amenities'] as $amenity_name) {
                try {
                    // First verify if the amenity exists
                    $check_amenity = $conn->prepare("SELECT amenity_id FROM amenities WHERE amenity_name = ?");
                    $check_amenity->execute([$amenity_name]);
                    $amenity = $check_amenity->fetch();
                    
                    if ($amenity) {
                        $stmt_amenity->execute([
                            'pg_id' => $new_pg_id,
                            'amenity_name' => $amenity_name
                        ]);
                    } else {
                        error_log("Amenity not found in master table: $amenity_name");
                    }
                } catch (PDOException $e) {
                    error_log("Failed to insert amenity '$amenity_name' for PG $new_pg_id: " . $e->getMessage());
                    // Continue with other amenities
                }
            }

        // Verify amenities were saved
            $verify = $conn->prepare("SELECT COUNT(*) FROM pg_amenities WHERE pg_id = ?");
            $verify->execute([$new_pg_id]);
            $count = $verify->fetchColumn();
            
            if ($count == 0 && !empty($_POST['amenities'])) {
                error_log("Warning: No amenities were saved for PG ID: $new_pg_id");
            } else {
                error_log("Successfully saved $count amenities for PG ID: $new_pg_id");
            }
        }

        // For location updates, use the correct PG ID (either new or existing)
        $current_pg_id = $is_update_mode ? $pg_id_to_edit : $new_pg_id;

        // Persist PG location into pg_location table if latitude/longitude provided
        try {
            $latitude = isset($_POST['latitude']) ? trim($_POST['latitude']) : null;
            $longitude = isset($_POST['longitude']) ? trim($_POST['longitude']) : null;
            if ($latitude !== null && $longitude !== null && $latitude !== '' && $longitude !== '') {
                // Validate numeric ranges
                $lat = floatval($latitude);
                $lng = floatval($longitude);
                if (is_finite($lat) && is_finite($lng) && $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180) {
                    // Upsert into pg_location (insert or update existing row)
                    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM pg_location WHERE pg_id = :pg_id");
                    $stmt_check->execute(['pg_id' => $current_pg_id]);
                    $exists = $stmt_check->fetchColumn();
                    if ($exists) {
                        $stmt_upd = $conn->prepare("UPDATE pg_location SET latitude = :lat, longitude = :lng WHERE pg_id = :pg_id");
                        $stmt_upd->execute(['lat' => $lat, 'lng' => $lng, 'pg_id' => $current_pg_id]);
                    } else {
                        $stmt_ins = $conn->prepare("INSERT INTO pg_location (pg_id, latitude, longitude) VALUES (:pg_id, :lat, :lng)");
                        $stmt_ins->execute(['pg_id' => $current_pg_id, 'lat' => $lat, 'lng' => $lng]);
                    }
                } else {
                    error_log("Invalid coordinates submitted for PG ID $new_pg_id: lat=$latitude lng=$longitude");
                }
            }
        } catch (PDOException $e) {
            // Don't break the whole transaction for location write failure, but log it
            error_log('Failed to persist pg location: ' . $e->getMessage());
        }

        $conn->commit();

        // Final Success Redirect
        $_SESSION['success_message'] = $message_success;
        header("Location: listing_success.php");
        exit();

    } catch(PDOException $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "DB Error: " . $e->getMessage();
        header("Location: list_pg.php" . ($is_update_mode ? "?edit=$pg_id_to_edit" : ""));
        exit();
    } catch(Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header("Location: list_pg.php" . ($is_update_mode ? "?edit=$pg_id_to_edit" : ""));
        exit();
    }

} else {
    header("Location: index.php");
    exit();
}
?>
