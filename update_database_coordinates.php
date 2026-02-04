<?php
include 'includes/config.php';

try {
    // Add latitude and longitude columns if they don't exist
    $sql = "SHOW COLUMNS FROM pg_listings LIKE 'latitude'";
    $result = $conn->query($sql);
    
    if ($result->rowCount() == 0) {
        // Add the columns
        $conn->exec("ALTER TABLE pg_listings 
                    ADD COLUMN latitude DECIMAL(10,8) DEFAULT NULL,
                    ADD COLUMN longitude DECIMAL(11,8) DEFAULT NULL");
        
        // Add an index for geographic queries
        $conn->exec("CREATE INDEX idx_pg_location ON pg_listings(latitude, longitude)");
        
        echo "Successfully added location coordinates columns to the database.";
    } else {
        echo "Location coordinates columns already exist.";
    }
    
} catch(PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>