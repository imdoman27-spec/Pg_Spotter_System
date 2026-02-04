<?php
// 1. Start session (required to access session variables)
session_start();

// 2. Unset/destroy all session variables
$_SESSION = array(); // Replace all session data with empty array

// If session cookie is being used, delete it as well
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Completely destroy session
session_destroy();

// 4. Redirect user to homepage
header("Location: index.php");
exit(); // Stop script execution here
?>