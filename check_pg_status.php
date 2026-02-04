<?php
require 'includes/config.php';

$stmt = $conn->query('SHOW COLUMNS FROM pg_listings WHERE Field = "status"');
$col = $stmt->fetch(PDO::FETCH_ASSOC);
echo "pg_listings status type: " . $col['Type'];
