<?php
// वह पासवर्ड जिसे आप admin के लिए उपयोग करना चाहते हैं: 'admin'
$admin_password_text = "aman1234"; 

// पासवर्ड को hash करें
$hashed_password = password_hash($admin_password_text, PASSWORD_DEFAULT);

echo "एडमिन लॉगिन के लिए उपयोग किया जाने वाला पासवर्ड: " . $admin_password_text . "\n";
echo "आपके डेटाबेस के लिए Hashed Password: " . $hashed_password . "\n";
?>