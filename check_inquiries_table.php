<?php
require 'includes/config.php';

try {
    $stmt = $conn->query('DESCRIBE inquiries');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Inquiries table structure:\n";
    foreach($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
}
