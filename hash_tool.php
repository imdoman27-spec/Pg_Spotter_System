<?php
$admin_password_text = "admin"; 
$hashed_password = password_hash($admin_password_text, PASSWORD_DEFAULT);
echo "नया Hashed Password: " . $hashed_password . "\n";
?>