<?php
session_start(); // Starting session is required for message

// Security check - if no success message, redirect to homepage
if (!isset($_SESSION['success_message'])) {
    header("Location: index.php");
    exit;
}

// Destination URL (Owner Dashboard)
$redirect_url = "dashboards/owner_dashboard.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5;url=<?php echo $redirect_url; ?>">
    <title>Listing Submitted</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Specific styles for this page */
        body { background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; } /* Center content vertically and horizontally */
        .success-box {
            padding: 40px;
            border-radius: 8px;
            background-color: #d4edda; /* Light green */
            color: #155724; /* Dark green */
            border: 1px solid #c3e6cb;
            max-width: 500px;
            text-align: center;
             box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .success-box h2 {
            margin-bottom: 15px;
        }
        .success-box p {
            margin-bottom: 20px;
            font-size: 1.1em;
        }
        .redirect-info {
            font-size: 0.9em;
            color: #555;
        }
         .checkmark {
            font-size: 4em; /* Make checkmark larger */
            color: #155724; /* Dark green color */
            display: block; /* Ensure it takes its own line */
            margin-bottom: 15px; /* Space below checkmark */
        }
    </style>
</head>
<body>
    <div class="success-box">
        <span class="checkmark">&#10004;</span>
        <h2>Success!</h2>
        <p><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
        <p class="redirect-info">You will be redirected to your dashboard in 5 seconds...</p>
        <?php unset($_SESSION['success_message']); ?>
    </div>
</body>
</html>