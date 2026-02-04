<?php
require 'includes/config.php';

$stmt = $conn->query('DESCRIBE pg_listings');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Columns in pg_listings table:\n";
foreach($columns as $col) {
    echo $col['Field'] . " (" . $col['Type'] . ")" . PHP_EOL;
}

// Check specifically for suspension columns
$has_is_suspended = false;
$has_suspension_reason = false;

foreach($columns as $col) {
    if($col['Field'] == 'is_suspended') $has_is_suspended = true;
    if($col['Field'] == 'suspension_reason') $has_suspension_reason = true;
}

echo "\nSuspension columns check:\n";
echo "is_suspended: " . ($has_is_suspended ? "EXISTS" : "MISSING") . "\n";
echo "suspension_reason: " . ($has_suspension_reason ? "EXISTS" : "MISSING") . "\n";
