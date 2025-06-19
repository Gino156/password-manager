<?php
session_start();
require 'db.php';
require_once __DIR__ . '/vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$g = new GoogleAuthenticator();
$secret = $g->generateSecret();

$stmt = $conn->prepare("UPDATE users SET otp_secret = ? WHERE id = ?");
$stmt->bind_param("si", $secret, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();

$userEmail = "user" . $_SESSION['user_id'] . "@yourdomain.com";
$qrUrl = GoogleQrUrl::generate($userEmail, $secret, 'MyPasswordManager');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Setup 2FA - MyPasswordManager</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .qr-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        img {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
        }

        a {
            text-decoration: none;
            background-color: #0d6efd;
            color: #fff;
            padding: 12px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>

<body>
    <div class="qr-container">
        <h2>📱 Scan this QR Code</h2>
        <p>Open your Authenticator app and scan the code below to set up 2FA.</p>
        <img src="<?= htmlspecialchars($qrUrl) ?>" alt="QR Code">
        <br>
        <a href="dashboard.php">✅ Done</a>
    </div>
</body>

</html>