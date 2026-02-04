<?php
include 'includes/config.php';

try {
    // Add is_primary column to pg_photos table if it doesn't exist
    $sql = "SHOW COLUMNS FROM pg_photos LIKE 'is_primary'";
    $result = $conn->query($sql);
    
    if ($result->rowCount() == 0) {
        // Add the is_primary column
        $conn->exec("ALTER TABLE pg_photos 
                    ADD COLUMN is_primary BOOLEAN DEFAULT FALSE");
        
        // Add a constraint to ensure only one primary photo per PG
        $conn->exec("CREATE TRIGGER ensure_single_primary_photo
                    BEFORE UPDATE ON pg_photos
                    FOR EACH ROW
                    BEGIN
                        IF NEW.is_primary = TRUE THEN
                            UPDATE pg_photos 
                            SET is_primary = FALSE 
                            WHERE pg_id = NEW.pg_id 
                            AND photo_id != NEW.photo_id;
                        END IF;
                    END;");
        
        echo "Successfully added primary photo feature to the database.";
    } else {
        echo "Primary photo column already exists.";
    }
    
} catch(PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>