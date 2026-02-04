<?php
session_start();
include 'includes/config.php';

header('Content-Type: application/json');

try {
    // Fetch listings and coordinates from pg_location when available
    $sql = "SELECT p.pg_id, p.pg_name, p.location_city, p.rent_per_person, pl.latitude, pl.longitude
            FROM pg_listings p
            LEFT JOIN pg_location pl ON p.pg_id = pl.pg_id
            WHERE p.status = 'approved'";

    $stmt = $conn->query($sql);
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Backfill missing coordinates with temporary test coords so map shows markers during development
    $modified_locations = array_map(function($location) {
        if (empty($location['latitude']) || empty($location['longitude'])) {
            $location['latitude'] = 20.5937 + (rand(-500, 500) / 100);
            $location['longitude'] = 78.9629 + (rand(-500, 500) / 100);
        }
        return $location;
    }, $locations);

    echo json_encode($modified_locations);
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch PG locations', 'details' => $e->getMessage()]);
}