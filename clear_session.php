<?php
// Simple session clear script - no Laravel dependencies
session_start();

// Clear all session data
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear all cookies
if (isset($_COOKIE)) {
    foreach ($_COOKIE as $name => $value) {
        setcookie($name, '', time() - 3600, '/');
    }
}

// Return simple HTML response
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Cleared</title>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .success { color: green; font-size: 18px; }
        .info { color: #666; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="success">✅ Session Cleared Successfully!</div>
    <div class="info">
        <p>All session data and cookies have been cleared.</p>
        <p>You can now try logging in again.</p>
        <p><a href="/login">Go to Login Page</a></p>
    </div>
    
    <script>
        // Also clear browser storage
        if (typeof(Storage) !== "undefined") {
            localStorage.clear();
            sessionStorage.clear();
        }
        
        // Show timestamp
        document.body.innerHTML += '<div class="info">Cleared at: ' + new Date().toLocaleString() + '</div>';
    </script>
</body>
</html>
