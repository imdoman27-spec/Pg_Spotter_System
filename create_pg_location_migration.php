<?php
// Run this script once to create the pg_location table.
include 'includes/config.php';
try {
    $sql = "CREATE TABLE IF NOT EXISTS pg_location (
        pg_id INT NOT NULL,
        latitude DECIMAL(10,8) DEFAULT NULL,
        longitude DECIMAL(11,8) DEFAULT NULL,
        PRIMARY KEY (pg_id),
        CONSTRAINT fk_pg_location_listing FOREIGN KEY (pg_id) REFERENCES pg_listings(pg_id) ON DELETE CASCADE
    )";
    $conn->exec($sql);
    echo "pg_location table created or already exists.";
} catch (PDOException $e) {
    echo "Error creating pg_location table: " . $e->getMessage();
}
?>