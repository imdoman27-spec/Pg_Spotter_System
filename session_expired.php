<?php
session_start();
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : 'Session expired. Please close this tab and revisit our site.';
unset($_SESSION['error_message']); // Clear the message after displaying
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - PG Spotter</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .session-expired {
            text-align: center;
            padding: 50px 20px;
            max-width: 600px;
            margin: 100px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .session-expired h1 {
            color: #3952a3;
            margin-bottom: 20px;
        }
        .session-expired p {
            color: #666;
            margin-bottom: 30px;
            font-size: 18px;
            line-height: 1.6;
        }
        .home-link {
            display: inline-block;
            padding: 12px 25px;
            background-color: #3952a3;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .home-link:hover {
            background-color: #2c407c;
        }
    </style>
</head>
<body>
    <div class="session-expired">
        <h1>Session Expired</h1>
        <p><?php echo htmlspecialchars($error_message); ?></p>
        <a href="index.php" class="home-link">Go to Homepage</a>
    </div>

    <script>
    // Prevent going back to the previous page
    history.pushState(null, null, document.URL);
    window.addEventListener('popstate', function () {
        history.pushState(null, null, document.URL);
    });
    </script>
</body>
</html>